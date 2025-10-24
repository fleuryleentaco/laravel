<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class FetchExternalDocuments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'external:fetch {--per-page=50}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Récupère et importe les documents depuis l\'API externe';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $per = (int)$this->option('per-page');
        $this->info('Démarrage de la récupération depuis l\'API externe...');

        try {
            $fetcher = new \App\Services\ExternalDocumentFetcher();
            $res = $fetcher->fetch($per);
            $this->info("Importés: {$res['imported']}, Ignorés: {$res['skipped']}");
            return 0;
        } catch (\Throwable $ex) {
            $this->error('Erreur: ' . $ex->getMessage());
            return 1;
        }
    }
}
