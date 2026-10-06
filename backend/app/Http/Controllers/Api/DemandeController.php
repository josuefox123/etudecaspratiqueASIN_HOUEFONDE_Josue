<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDemandeRequest;
use App\Http\Requests\UpdateStatutDemandeRequest;
use App\Models\Demande;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DemandeController extends Controller
{
    /**
     * Déposer une demande (statut initial forcé à 'déposée').
     */
    public function store(StoreDemandeRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $validated['statut'] = Demande::STATUT_DEPOSEE;
        $validated['motif_rejet'] = null;

        $demande = Demande::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Demande déposée avec succès.',
            'data' => $demande,
        ], 201);
    }

    /**
     * Consulter les demandes d'un usager (Anti-DDoS avec pagination bornée à 20).
     */
    public function indexByUsager(Request $request, string $npi): JsonResponse
    {
        if (!preg_match('/^[0-9]{10}$/', $npi)) {
            return response()->json([
                'success' => false,
                'message' => 'Le NPI doit comporter exactement 10 chiffres.',
            ], 422);
        }

        $query = Demande::query()
            ->where('npi', $npi)
            ->orderBy('created_at', 'desc');

        if ($request->filled('statut')) {
            $query->where('statut', (string) $request->query('statut'));
        }

        $perPage = max(1, min((int) $request->query('per_page', 20), 20));
        $demandes = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Liste des demandes récupérée avec succès.',
            'data' => $demandes,
        ], 200);
    }

    /**
     * Liste globale des demandes pour l'espace Agent.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Demande::query()->orderBy('created_at', 'desc');

        if ($request->filled('statut')) {
            $query->where('statut', (string) $request->query('statut'));
        }

        if ($request->filled('npi')) {
            $query->where('npi', 'like', '%' . (string) $request->query('npi') . '%');
        }

        $perPage = max(1, min((int) $request->query('per_page', 20), 20));
        $demandes = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Toutes les demandes récupérées avec succès.',
            'data' => $demandes,
        ], 200);
    }

    /**
     * Faire avancer le traitement d'une demande avec verrouillage de transition.
     */
    public function updateStatut(UpdateStatutDemandeRequest $request, string $reference): JsonResponse
    {
        $demande = Demande::where('reference', $reference)->first();

        if (!$demande) {
            return response()->json([
                'success' => false,
                'message' => 'Demande introuvable.',
            ], 404);
        }

        $nouveauStatut = (string) $request->validated('statut');
        $statutActuel = $demande->statut;

        // RÈGLE MÉTIER : Immuabilité absolue des états finaux
        if (in_array($statutActuel, [Demande::STATUT_VALIDEE, Demande::STATUT_REJETEE], true)) {
            return response()->json([
                'success' => false,
                'message' => "Action interdite : une demande déjà {$statutActuel} ne peut plus changer de statut.",
            ], 422);
        }

        // RÈGLE MÉTIER : 'déposée' -> uniquement vers 'en cours de traitement'
        if ($statutActuel === Demande::STATUT_DEPOSEE && $nouveauStatut !== Demande::STATUT_EN_COURS) {
            return response()->json([
                'success' => false,
                'message' => "Transition invalide : une demande déposée doit d'abord passer en cours de traitement.",
            ], 422);
        }

        // RÈGLE MÉTIER : 'en cours de traitement' -> uniquement vers 'validée' ou 'rejetée'
        if ($statutActuel === Demande::STATUT_EN_COURS && !in_array($nouveauStatut, [Demande::STATUT_VALIDEE, Demande::STATUT_REJETEE], true)) {
            return response()->json([
                'success' => false,
                'message' => "Transition invalide : une demande en cours de traitement ne peut être que validée ou rejetée.",
            ], 422);
        }

        $demande->statut = $nouveauStatut;
        $demande->motif_rejet = ($nouveauStatut === Demande::STATUT_REJETEE)
            ? (string) $request->validated('motif_rejet')
            : null;

        $demande->save();

        return response()->json([
            'success' => true,
            'message' => "Statut mis à jour vers '{$nouveauStatut}'.",
            'data' => $demande,
        ], 200);
    }

    /**
     * Dénombrement sécurisé par statut.
     */
    public function stats(): JsonResponse
    {
        $rawStats = Demande::query()
            ->select('statut', DB::raw('count(*) as aggregate'))
            ->groupBy('statut')
            ->pluck('aggregate', 'statut')
            ->toArray();

        return response()->json([
            'success' => true,
            'data' => [
                Demande::STATUT_DEPOSEE => $rawStats[Demande::STATUT_DEPOSEE] ?? 0,
                Demande::STATUT_EN_COURS => $rawStats[Demande::STATUT_EN_COURS] ?? 0,
                Demande::STATUT_VALIDEE => $rawStats[Demande::STATUT_VALIDEE] ?? 0,
                Demande::STATUT_REJETEE => $rawStats[Demande::STATUT_REJETEE] ?? 0,
            ],
        ], 200);
    }
}
