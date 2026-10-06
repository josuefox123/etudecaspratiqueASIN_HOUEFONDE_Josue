<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('demandes', function (Blueprint $table) {
            $table->id();
            $table->uuid('reference')->unique();
            $table->string('npi', 10)->index();
            $table->enum('type_acte', [
                'acte de naissance',
                'casier judiciaire',
                'certificat de résidence'
            ]);
            $table->unsignedTinyInteger('nombre_copies');
            $table->enum('statut', [
                'déposée',
                'en cours de traitement',
                'validée',
                'rejetée'
            ])->default('déposée')->index();
            $table->text('motif_rejet')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('demandes');
    }
};
