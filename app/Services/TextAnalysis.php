<?php

namespace App\Services;

use Illuminate\Support\Str;

trait TextAnalysis
{
    private function extractContentFromPath($mime, $extension, $fullPath)
    {
        $text = null;
        try {
            if (str_contains($mime, 'pdf') || $extension === 'pdf') {
                if (class_exists('\Smalot\\PdfParser\\Parser')) {
                    $parser = new \Smalot\PdfParser\Parser();
                    $pdf = $parser->parseFile($fullPath);
                    $text = $pdf->getText();
                }
            } elseif (in_array($extension, ['doc','docx'])) {
                if (class_exists('\PhpOffice\\PhpWord\\IOFactory')) {
                    $phpWord = \PhpOffice\PhpWord\IOFactory::load($fullPath);
                    $text = '';
                    foreach ($phpWord->getSections() as $section) {
                        $elements = $section->getElements();
                        foreach ($elements as $e) {
                            if (method_exists($e, 'getText')) {
                                $text .= ' '. $e->getText();
                            }
                        }
                    }
                }
            } elseif (Str::startsWith($mime, 'text') || in_array($extension, ['txt','md','html','htm','csv'])) {
                $text = file_get_contents($fullPath);
            }
        } catch (\Throwable $ex) {
            $text = null;
        }
        return $text;
    }

    /**
     * Compte les mots de manière Unicode-aware
     */
    private function countWords($text)
    {
        if (!$text) return 0;
        $clean = strip_tags($text);
        preg_match_all('/\p{L}+/u', $clean, $matches);
        return count($matches[0] ?? []);
    }

    /**
     * Vérifie les règles de base (longueur + contenu banni)
     */
    private function checkBasicRules($content)
    {
        $errors = [];
        
        if (!$content) {
            return $errors;
        }

        // Règle 1: Longueur minimale
        $minWords = config('antiplag.min_word_count', 20);
        $wordCount = $this->countWords($content);
        if ($wordCount < $minWords) {
            $errors[] = [
                'type' => 'too_short', 
                'message' => "Document trop court ($wordCount mots, minimum $minWords requis)"
            ];
        }

        // Règle 2: Expressions bannies
        $banned = config('antiplag.banned_phrases', ['loremipsum', 'plagiarize_example']);
        foreach ($banned as $bad) {
            if (stripos($content, $bad) !== false) {
                $errors[] = [
                    'type' => 'banned_content', 
                    'message' => "Contient la phrase interdite : $bad"
                ];
            }
        }

        return $errors;
    }

    /**
     * Vérifie la similarité avec d'autres documents
     */
    private function checkSimilarity($document, $threshold = 0.5)
    {
        $errors = [];
        
        if (!$document->content) {
            return $errors;
        }

        // Calculer/récupérer la signature MinHash
        if (empty($document->minhash)) {
            $document->minhash = $this->computeMinHash($document->content, 5, 64);
            $document->save();
        }

        // Récupérer tous les autres documents avec contenu
        $existing = \App\Models\Document::whereNotNull('content')
            ->where('id', '<>', $document->id)
            ->get();

        foreach ($existing as $other) {
            // S'assurer que l'autre document a aussi une signature
            if (empty($other->minhash)) {
                $other->minhash = $this->computeMinHash($other->content, 5, 64);
                $other->save();
            }

            // Préfiltre rapide avec MinHash
            $fastSim = $this->minhashSimilarity(
                $document->minhash ?? [], 
                $other->minhash ?? []
            );

            // Si le préfiltre passe (>= 0.4), calculer Jaccard précis
            if ($fastSim >= 0.4) {
                $jaccardSim = $this->jaccardSimilarityText(
                    $document->content, 
                    $other->content, 
                    5
                );

                // Si similarité significative détectée
                if ($jaccardSim >= $threshold) {
                    $errors[] = [
                        'type' => 'similarity',
                        'message' => "Similaire au document ID {$other->id} '{$other->filename}' (" . round($jaccardSim * 100, 2) . "%)"
                    ];
                }
            }
        }

        return $errors;
    }

    private function jaccardSimilarityText($a, $b, $k = 5)
    {
        if (!$a || !$b) return 0.0;
        $sa = $this->shinglesText($a, $k);
        $sb = $this->shinglesText($b, $k);
        $inter = count(array_intersect_key($sa, $sb));
        $union = count($sa) + count($sb) - $inter;
        if ($union == 0) return 0.0;
        return $inter / $union;
    }

    /**
     * Calcule la signature MinHash pour un texte
     */
    private function computeMinHash($text, $kShingle = 5, $nHashes = 64)
    {
        $shingles = array_keys($this->shinglesText($text, $kShingle));
        if (empty($shingles)) return [];
        
        $hashes = array_fill(0, $nHashes, PHP_INT_MAX);
        
        for ($i = 0; $i < $nHashes; $i++) {
            $salt = $i * 0x9e3779b9;
            foreach ($shingles as $s) {
                $h = $this->hashShingle($s, $salt);
                if ($h < $hashes[$i]) {
                    $hashes[$i] = $h;
                }
            }
        }
        
        return $hashes;
    }

    private function hashShingle($shingle, $salt)
    {
        // Hash 32-bit non-signé
        $h = crc32($shingle . '|' . $salt);
        return $h & 0xffffffff;
    }

    /**
     * Calcule la similarité entre deux signatures MinHash
     */
    private function minhashSimilarity(array $sigA, array $sigB)
    {
        if (empty($sigA) || empty($sigB)) return 0.0;
        
        $n = min(count($sigA), count($sigB));
        if ($n == 0) return 0.0;
        
        $match = 0;
        for ($i = 0; $i < $n; $i++) {
            if ($sigA[$i] === $sigB[$i]) {
                $match++;
            }
        }
        
        return $match / $n;
    }

    /**
     * Génère les shingles (n-grammes de mots) d'un texte
     */
    private function shinglesText($text, $k)
    {
        // Normalisation: minuscules, suppression tags, consolidation espaces
        $text = preg_replace('/\s+/', ' ', strip_tags(mb_strtolower($text, 'UTF-8')));
        
        // Découpage en mots (Unicode-aware)
        $words = preg_split('/\s+/u', trim($text));
        
        $map = [];
        $count = max(0, count($words) - $k + 1);
        
        for ($i = 0; $i < $count; $i++) {
            $sh = implode(' ', array_slice($words, $i, $k));
            $map[$sh] = true;
        }
        
        return $map;
    }
}