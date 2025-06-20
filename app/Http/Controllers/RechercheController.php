<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Document;
use App\Models\Dossier;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class RechercheController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Afficher l'interface de recherche avancée
     */
    public function index()
    {
        $champs_activites = \App\Models\ChampActivite::all();
        $personnes = User::where('role', '!=', 'admin')->get();
        
        return view('pages.recherche.index', compact('champs_activites', 'personnes'));
    }

    /**
     * Effectuer une recherche multicritères
     */
    public function search(Request $request)
    {
        $query = Document::with(['dossier', 'user']);

        // Recherche par nom de fichier
        if ($request->filled('nom_fichier')) {
            $query->where('nom_fichier', 'LIKE', '%' . $request->nom_fichier . '%');
        }

        // Recherche par client/personne
        if ($request->filled('personne_id')) {
            $query->whereHas('dossier', function ($q) use ($request) {
                $q->where('user_id', $request->personne_id);
            });
        }

        // Recherche par numéro de dossier
        if ($request->filled('numero_dossier')) {
            $query->whereHas('dossier', function ($q) use ($request) {
                $q->where('nom_dossier', 'LIKE', '%' . $request->numero_dossier . '%');
            });
        }

        // Recherche par champ d'activité
        if ($request->filled('champ_activite_id')) {
            $query->whereHas('dossier', function ($q) use ($request) {
                $q->where('champ_activite_id', $request->champ_activite_id);
            });
        }

        // Recherche par date de création
        if ($request->filled('date_debut')) {
            $query->whereDate('created_at', '>=', $request->date_debut);
        }

        if ($request->filled('date_fin')) {
            $query->whereDate('created_at', '<=', $request->date_fin);
        }

        // Recherche par type de document
        if ($request->filled('type_document')) {
            $query->where('type_fichier', $request->type_document);
        }

        // Recherche par statut d'archivage
        if ($request->filled('archive_status')) {
            $query->where('archive_status', $request->archive_status);
        }

        // Recherche full-text dans le contenu (si disponible)
        if ($request->filled('contenu')) {
            $query->where('contenu_indexe', 'LIKE', '%' . $request->contenu . '%');
        }

        $documents = $query->orderBy('created_at', 'desc')->paginate(20);

        // Sauvegarder l'historique de recherche
        $this->saveSearchHistory($request->all());

        return view('pages.recherche.results', compact('documents'));
    }

    /**
     * Recherche avec suggestions automatiques
     */
    public function suggest(Request $request)
    {
        $term = $request->get('term');
        $type = $request->get('type', 'all');

        $suggestions = [];

        switch ($type) {
            case 'nom_fichier':
                $suggestions = Document::where('nom_fichier', 'LIKE', '%' . $term . '%')
                                     ->distinct()
                                     ->pluck('nom_fichier')
                                     ->take(10);
                break;

            case 'personne':
                $suggestions = User::where('role', '!=', 'admin')
                                 ->where(function ($q) use ($term) {
                                     $q->where('nom', 'LIKE', '%' . $term . '%')
                                       ->orWhere('prenom', 'LIKE', '%' . $term . '%');
                                 })
                                 ->get()
                                 ->map(function ($user) {
                                     return $user->nom . ' ' . $user->prenom;
                                 })
                                 ->take(10);
                break;

            case 'dossier':
                $suggestions = Dossier::where('nom_dossier', 'LIKE', '%' . $term . '%')
                                    ->distinct()
                                    ->pluck('nom_dossier')
                                    ->take(10);
                break;

            default:
                // Recherche globale
                $fichiers = Document::where('nom_fichier', 'LIKE', '%' . $term . '%')
                                  ->pluck('nom_fichier')
                                  ->take(5);
                
                $personnes = User::where('role', '!=', 'admin')
                               ->where(function ($q) use ($term) {
                                   $q->where('nom', 'LIKE', '%' . $term . '%')
                                     ->orWhere('prenom', 'LIKE', '%' . $term . '%');
                               })
                               ->get()
                               ->map(function ($user) {
                                   return $user->nom . ' ' . $user->prenom;
                               })
                               ->take(5);

                $suggestions = $fichiers->merge($personnes);
                break;
        }

        return response()->json($suggestions);
    }

    /**
     * Afficher l'historique des recherches
     */
    public function history()
    {
        $historique = \App\Models\SearchHistory::where('user_id', Auth::id())
                                             ->orderBy('created_at', 'desc')
                                             ->paginate(20);

        return view('pages.recherche.history', compact('historique'));
    }

    /**
     * Recherche rapide (barre de recherche globale)
     */
    public function quickSearch(Request $request)
    {
        $term = $request->get('q');
        
        if (empty($term)) {
            return response()->json([]);
        }

        // Recherche dans les documents
        $documents = Document::where('nom_fichier', 'LIKE', '%' . $term . '%')
                           ->with(['dossier', 'user'])
                           ->take(5)
                           ->get();

        // Recherche dans les personnes
        $personnes = User::where('role', '!=', 'admin')
                       ->where(function ($q) use ($term) {
                           $q->where('nom', 'LIKE', '%' . $term . '%')
                             ->orWhere('prenom', 'LIKE', '%' . $term . '%')
                             ->orWhere('email', 'LIKE', '%' . $term . '%');
                       })
                       ->take(5)
                       ->get();

        // Recherche dans les dossiers
        $dossiers = Dossier::where('nom_dossier', 'LIKE', '%' . $term . '%')
                         ->with(['user', 'champActivite'])
                         ->take(5)
                         ->get();

        return response()->json([
            'documents' => $documents,
            'personnes' => $personnes,
            'dossiers' => $dossiers
        ]);
    }

    /**
     * Sauvegarder l'historique de recherche
     */
    private function saveSearchHistory($searchParams)
    {
        \App\Models\SearchHistory::create([
            'user_id' => Auth::id(),
            'search_params' => json_encode($searchParams),
            'search_type' => 'advanced',
            'results_count' => 0 // À mettre à jour selon les résultats
        ]);
    }

    /**
     * Filtrage dynamique pour l'interface
     */
    public function filter(Request $request)
    {
        $type = $request->get('type');
        $value = $request->get('value');

        $results = [];

        switch ($type) {
            case 'champ_activite':
                $results = Document::whereHas('dossier', function ($q) use ($value) {
                    $q->where('champ_activite_id', $value);
                })->count();
                break;

            case 'type_document':
                $results = Document::where('type_fichier', $value)->count();
                break;

            case 'archive_status':
                $results = Document::where('archive_status', $value)->count();
                break;
        }

        return response()->json(['count' => $results]);
    }
}
