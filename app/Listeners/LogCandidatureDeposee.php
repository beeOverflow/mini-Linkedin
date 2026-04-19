<?php

namespace App\Listeners;

use App\Events\CandidatureDeposee;
use Illuminate\Support\Facades\Log;

class LogCandidatureDeposee
{
    public function handle(CandidatureDeposee $event): void
    {
        $candidature = $event->candidature;

        // Load relations if not already loaded
        $candidature->loadMissing(['profil.user', 'offre']);

        $date          = now()->format('Y-m-d H:i:s');
        $nomCandidat   = $candidature->profil->user->name ?? 'Inconnu';
        $titreOffre    = $candidature->offre->titre ?? 'Inconnu';

        $message = "[{$date}] Candidature déposée — Candidat : {$nomCandidat} | Offre : {$titreOffre}";

        Log::build([
            'driver' => 'single',
            'path'   => storage_path('logs/candidatures.log'),
        ])->info($message);
    }
}
