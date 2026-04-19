<?php

namespace App\Listeners;

use App\Events\StatutCandidatureMis;
use Illuminate\Support\Facades\Log;

class LogStatutCandidatureMis
{
    public function handle(StatutCandidatureMis $event): void
    {
        $date          = now()->format('Y-m-d H:i:s');
        $ancienStatut  = $event->ancienStatut;
        $nouveauStatut = $event->nouveauStatut;
        $candidatureId = $event->candidature->id;

        $message = "[{$date}] Statut mis à jour — Candidature #{$candidatureId} | Ancien statut : {$ancienStatut} | Nouveau statut : {$nouveauStatut}";

        Log::build([
            'driver' => 'single',
            'path'   => storage_path('logs/candidatures.log'),
        ])->info($message);
    }
}
