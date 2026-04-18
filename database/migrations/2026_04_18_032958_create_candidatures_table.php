<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('candidatures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('offre_id')->constrained()->cascadeOnDelete();
            $table->foreignId('profil_id')->constrained()->cascadeOnDelete();
            $table->text('message')->nullable();
            $table->enum('statut',['en_attente','acceptee','refusee'])->default('en_attente');
            $table->unique(['offer_id','profil_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('candidatures');
    }
};
