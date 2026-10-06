<?php

namespace Tests\Feature;

use App\Models\Demande;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DemandeGestionTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test de création d'une demande avec statut initial forcé à 'déposée' et génération d'un UUID.
     */
    public function test_creation_demande_force_statut_deposee(): void
    {
        $payload = [
            'npi' => '1234567890',
            'type_acte' => 'acte de naissance',
            'nombre_copies' => 2,
        ];

        $response = $this->postJson('/api/v1/demandes', $payload);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'data' => [
                    'npi' => '1234567890',
                    'type_acte' => 'acte de naissance',
                    'nombre_copies' => 2,
                    'statut' => 'déposée',
                    'motif_rejet' => null,
                ],
            ]);

        $this->assertDatabaseHas('demandes', [
            'npi' => '1234567890',
            'statut' => 'déposée',
        ]);
    }

    /**
     * Test de validation sur le NPI (strictement 10 chiffres), le type d'acte et le nombre de copies.
     */
    public function test_validation_champs_creation_demande(): void
    {
        $payloadInvalide = [
            'npi' => '12345', // < 10 chiffres
            'type_acte' => 'passeport', // type d'acte non autorisé
            'nombre_copies' => 10, // > 5
        ];

        $response = $this->postJson('/api/v1/demandes', $payloadInvalide);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
            ])
            ->assertJsonValidationErrors(['npi', 'type_acte', 'nombre_copies']);
    }

    /**
     * Test de récupération paginée des demandes par NPI avec filtre statut optionnel.
     */
    public function test_recuperation_demandes_par_npi_avec_filtre(): void
    {
        Demande::create([
            'npi' => '9999999999',
            'type_acte' => 'casier judiciaire',
            'nombre_copies' => 1,
            'statut' => 'déposée',
        ]);

        Demande::create([
            'npi' => '9999999999',
            'type_acte' => 'certificat de résidence',
            'nombre_copies' => 3,
            'statut' => 'en cours de traitement',
        ]);

        // Sans filtre
        $response = $this->getJson('/api/v1/usagers/9999999999/demandes');
        $response->assertStatus(200)
            ->assertJsonCount(2, 'data.data');

        // Avec filtre statut = 'en cours de traitement'
        $responseFiltre = $this->getJson('/api/v1/usagers/9999999999/demandes?statut=en cours de traitement');
        $responseFiltre->assertStatus(200)
            ->assertJsonCount(1, 'data.data')
            ->assertJsonPath('data.data.0.statut', 'en cours de traitement');
    }

    /**
     * Test du cycle de vie : 'déposée' -> 'en cours de traitement'.
     */
    public function test_transition_deposee_vers_en_cours(): void
    {
        $demande = Demande::create([
            'npi' => '1111111111',
            'type_acte' => 'acte de naissance',
            'nombre_copies' => 1,
            'statut' => 'déposée',
        ]);

        $response = $this->patchJson("/api/v1/demandes/{$demande->reference}/statut", [
            'statut' => 'en cours de traitement',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.statut', 'en cours de traitement');
    }

    /**
     * Test d'interdiction de saut direct 'déposée' -> 'validée'.
     */
    public function test_interdiction_transition_directe_deposee_vers_validee(): void
    {
        $demande = Demande::create([
            'npi' => '1111111111',
            'type_acte' => 'acte de naissance',
            'nombre_copies' => 1,
            'statut' => 'déposée',
        ]);

        $response = $this->patchJson("/api/v1/demandes/{$demande->reference}/statut", [
            'statut' => 'validée',
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
            ]);
    }

    /**
     * Test de rejet obligatoire avec motif d'au moins 5 caractères.
     */
    public function test_motif_rejet_obligatoire_pour_statut_rejete(): void
    {
        $demande = Demande::create([
            'npi' => '2222222222',
            'type_acte' => 'casier judiciaire',
            'nombre_copies' => 1,
            'statut' => 'en cours de traitement',
        ]);

        // Sans motif -> doit échouer
        $responseSansMotif = $this->patchJson("/api/v1/demandes/{$demande->reference}/statut", [
            'statut' => 'rejetée',
        ]);
        $responseSansMotif->assertStatus(422);

        // Avec motif valide -> doit réussir
        $responseAvecMotif = $this->patchJson("/api/v1/demandes/{$demande->reference}/statut", [
            'statut' => 'rejetée',
            'motif_rejet' => 'Document justificatif illisible.',
        ]);
        $responseAvecMotif->assertStatus(200)
            ->assertJsonPath('data.statut', 'rejetée')
            ->assertJsonPath('data.motif_rejet', 'Document justificatif illisible.');
    }

    /**
     * Test d'immuabilité des états finaux 'validée' et 'rejetée'.
     */
    public function test_immuabilite_etats_finaux(): void
    {
        $demandeValidee = Demande::create([
            'npi' => '3333333333',
            'type_acte' => 'certificat de résidence',
            'nombre_copies' => 1,
            'statut' => 'validée',
        ]);

        $response = $this->patchJson("/api/v1/demandes/{$demandeValidee->reference}/statut", [
            'statut' => 'en cours de traitement',
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
            ]);
    }

    /**
     * Test d'obtention des statistiques regroupées par statut.
     */
    public function test_statistiques_demandes(): void
    {
        Demande::create(['npi' => '1000000000', 'type_acte' => 'acte de naissance', 'nombre_copies' => 1, 'statut' => 'déposée']);
        Demande::create(['npi' => '2000000000', 'type_acte' => 'casier judiciaire', 'nombre_copies' => 1, 'statut' => 'déposée']);
        Demande::create(['npi' => '3000000000', 'type_acte' => 'certificat de résidence', 'nombre_copies' => 1, 'statut' => 'validée']);

        $response = $this->getJson('/api/v1/demandes/stats');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'déposée' => 2,
                    'en cours de traitement' => 0,
                    'validée' => 1,
                    'rejetée' => 0,
                ],
            ]);
    }
}
