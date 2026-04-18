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
        Schema::create('profil_competence',
         function (Blueprint $table) {
            $table->foreignId('profil_id')->constrained()->cascadOnDelete();
            $table->foreignId('competence_id')->constrained()->cascadOnDelete();
            $table->enum('niveau',['debutant','intermediaire','expert']);
            $table->primary(['profil_id','competence_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profil_competence');
    }
};
