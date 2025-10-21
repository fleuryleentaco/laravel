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

    // --- MinHash helpers ---
    private function computeMinHash($text, $kShingle = 5, $nHashes = 64)
    {
        $shingles = array_keys($this->shinglesText($text, $kShingle));
        if (empty($shingles)) return [];
        $hashes = array_fill(0, $nHashes, PHP_INT_MAX);
        for ($i=0;$i<$nHashes;$i++) {
            $salt = $i * 0x9e3779b9;
            foreach ($shingles as $s) {
                $h = $this->hashShingle($s, $salt);
                if ($h < $hashes[$i]) $hashes[$i] = $h;
            }
        }
        return $hashes;
    }

    private function hashShingle($shingle, $salt)
    {
        // 32-bit unsigned
        $h = crc32($shingle . '|' . $salt);
        return $h & 0xffffffff;
    }

    private function minhashSimilarity(array $sigA, array $sigB)
    {
        if (empty($sigA) || empty($sigB)) return 0.0;
        $n = min(count($sigA), count($sigB));
        $match = 0;
        for ($i=0;$i<$n;$i++) {
            if ($sigA[$i] === $sigB[$i]) $match++;
        }
        return $match / $n;
    }

    private function shinglesText($text, $k)
    {
        $text = preg_replace('/\s+/',' ',strip_tags(mb_strtolower($text)));
        $words = preg_split('/\s+/', $text);
        $map = [];
        $count = max(0, count($words) - $k + 1);
        for ($i=0;$i<$count;$i++) {
            $sh = implode(' ', array_slice($words, $i, $k));
            $map[$sh] = true;
        }
        return $map;
    }
}
