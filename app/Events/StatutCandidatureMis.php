<?php

namespace App\Events;

use App\Models\Candidature;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class StatutCandidatureMis
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly Candidature $candidature,
        public readonly string $ancienStatut,
        public readonly string $nouveauStatut
    ) {}
}
