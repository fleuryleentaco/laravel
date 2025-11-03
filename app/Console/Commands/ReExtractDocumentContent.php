<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Document;
use App\Services\TextAnalysis;
use Illuminate\Support\Facades\Storage;

class ReExtractDocumentContent extends Command
{
    use TextAnalysis;
    
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'documents:re-extract {--id=* : IDs des documents à ré-extraire} {--all : Ré-extraire tous les documents sans contenu}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Ré-extrait le contenu des documents (utile après installation de PhpSpreadsheet)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $ids = $this->option('id');
        $all = $this->option('all');
        
        if (!$ids && !$all) {
            $this->error('Vous devez spécifier --id=X ou --all');
            return 1;
        }
        
        $query = Document::query();
        
        if ($all) {
            // Tous les documents sans contenu
            $query->whereNull('content')->orWhere('content', '');
        } else {
            // Documents spécifiques
            $query->whereIn('id', $ids);
        }
        
        $documents = $query->get();
        
        if ($documents->isEmpty()) {
            $this->info('Aucun document à traiter.');
            return 0;
        }
        
        $this->info("Traitement de {$documents->count()} document(s)...");
        $bar = $this->output->createProgressBar($documents->count());
        $bar->start();
        
        $success = 0;
        $failed = 0;
        
        foreach ($documents as $doc) {
            try {
                // Récupérer le chemin complet du fichier
                $fullPath = storage_path('app/' . $doc->path);
                
                if (!file_exists($fullPath)) {
                    $this->newLine();
                    $this->warn("Fichier introuvable: {$doc->filename} (ID: {$doc->id})");
                    $failed++;
                    $bar->advance();
                    continue;
                }
                
                // Extraire le contenu
                $extension = pathinfo($doc->filename, PATHINFO_EXTENSION);
                $content = $this->extractContentFromPath($doc->mime, strtolower($extension), $fullPath);
                
                if ($content) {
                    $doc->content = $content;
                    
                    // Recalculer le MinHash
                    $doc->minhash = $this->computeMinHash($content, 5, 64);
                    
                    $doc->save();
                    $success++;
                } else {
                    $failed++;
                }
                
            } catch (\Exception $e) {
                $this->newLine();
                $this->error("Erreur pour {$doc->filename}: " . $e->getMessage());
                $failed++;
            }
            
            $bar->advance();
        }
        
        $bar->finish();
        $this->newLine(2);
        
        $this->info("✅ Succès: {$success}");
        if ($failed > 0) {
            $this->warn("⚠️  Échecs: {$failed}");
        }
        
        return 0;
    }
}
