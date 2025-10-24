<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\DatabaseMessage;

class ReportErrorResult extends Notification implements ShouldQueue
{
    use Queueable;

    public $fileName;
    public $reportDescription;
    public $details;

    public function __construct($fileName, $reportDescription, $details)
    {
        $this->fileName = $fileName;
        $this->reportDescription = $reportDescription;
        $this->details = $details;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'message' => "Résultat de votre réclamation pour le fichier : '{$this->fileName}'\nDescription: {$this->reportDescription}\nDétail: {$this->details}",
            'file_name' => $this->fileName,
            'report_description' => $this->reportDescription,
            'details' => $this->details,
        ];
    }
}
