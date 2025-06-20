<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;
use App\Models\Dossier;
use App\Models\ChampActivite;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class DossierController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Afficher la liste des dossiers
     */
    public function index()
    {
        $dossiers = Dossier::with(['champActivite', 'user', 'documents'])
                           ->orderBy('created_at', 'desc')
                           ->paginate(20);

        // Log de consultation
        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'consultation',
            'table_name' => 'dossiers',
            'description' => 'Consultation de la liste des dossiers'
        ]);

        return view('pages.dossiers.index', compact('dossiers'));
    }

    /**
     * Afficher le formulaire de création
     */
    public function create()
    {
        $champs_activites = ChampActivite::where('actif', true)->get();
        
        return view('pages.dossiers.create', compact('champs_activites'));
    }

    /**
     * Créer un nouveau dossier
     */
    public function store(Request $request)
    {
        $request->validate([
            'nom_dossier' => 'required|string|max:255',
            'champ_activite_id' => 'required|exists:champs_activites,id',
            'description' => 'nullable|string',
            'client_nom' => 'required|string|max:255',
            'client_prenom' => 'nullable|string|max:255',
            'client_email' => 'nullable|email|max:255',
            'client_telephone' => 'nullable|string|max:20',
        ]);

        // Générer un numéro de dossier unique
        $numeroDossier = $this->genererNumeroDossier();

        $dossier = Dossier::create([
            'nom_dossier' => $request->nom_dossier,
            'numero_dossier' => $numeroDossier,
            'champ_activite_id' => $request->champ_activite_id,
            'description' => $request->description,
            'client_nom' => $request->client_nom,
            'client_prenom' => $request->client_prenom,
            'client_email' => $request->client_email,
            'client_telephone' => $request->client_telephone,
            'user_id' => Auth::id(),
            'statut' => 'ouvert',
            'taille' => 0,
        ]);

        // Log de l'activité
        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'creation',
            'table_name' => 'dossiers',
            'record_id' => $dossier->id,
            'description' => "Création du dossier : {$dossier->nom_dossier}"
        ]);

        return redirect()->route('dossiers.index')
                        ->with('success', 'Dossier créé avec succès');
    }

    /**
     * Afficher un dossier spécifique
     */
    public function show($id)
    {
        $dossier = Dossier::with(['champActivite', 'user', 'documents'])
                          ->findOrFail($id);

        // Log de consultation
        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'consultation',
            'table_name' => 'dossiers',
            'record_id' => $dossier->id,
            'description' => "Consultation du dossier : {$dossier->nom_dossier}"
        ]);

        return view('pages.dossiers.show', compact('dossier'));
    }

    /**
     * Afficher le formulaire d'édition
     */
    public function edit($id)
    {
        $dossier = Dossier::findOrFail($id);
        $champs_activites = ChampActivite::where('actif', true)->get();

        // Vérifier les permissions
        if (!Auth::user()->est_admin && $dossier->user_id !== Auth::id()) {
            abort(403, 'Accès non autorisé');
        }

        return view('pages.dossiers.edit', compact('dossier', 'champs_activites'));
    }

    /**
     * Mettre à jour un dossier
     */
    public function update(Request $request, $id)
    {
        $dossier = Dossier::findOrFail($id);

        // Vérifier les permissions
        if (!Auth::user()->est_admin && $dossier->user_id !== Auth::id()) {
            abort(403, 'Accès non autorisé');
        }

        $request->validate([
            'nom_dossier' => 'required|string|max:255',
            'champ_activite_id' => 'required|exists:champs_activites,id',
            'description' => 'nullable|string',
            'client_nom' => 'required|string|max:255',
            'client_prenom' => 'nullable|string|max:255',
            'client_email' => 'nullable|email|max:255',
            'client_telephone' => 'nullable|string|max:20',
            'statut' => 'required|in:ouvert,ferme,archive',
        ]);

        $dossier->update($request->all());

        // Log de l'activité
        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'modification',
            'table_name' => 'dossiers',
            'record_id' => $dossier->id,
            'description' => "Modification du dossier : {$dossier->nom_dossier}"
        ]);

        return redirect()->route('dossiers.show', $dossier->id)
                        ->with('success', 'Dossier mis à jour avec succès');
    }

    /**
     * Fermer un dossier
     */
    public function fermer($id)
    {
        $dossier = Dossier::findOrFail($id);

        // Vérifier les permissions
        if (!Auth::user()->est_admin && $dossier->user_id !== Auth::id()) {
            abort(403, 'Accès non autorisé');
        }

        $dossier->update(['statut' => 'ferme']);

        // Log de l'activité
        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'fermeture',
            'table_name' => 'dossiers',
            'record_id' => $dossier->id,
            'description' => "Fermeture du dossier : {$dossier->nom_dossier}"
        ]);

        return redirect()->back()->with('success', 'Dossier fermé avec succès');
    }

    /**
     * Archiver un dossier
     */
    public function archiver($id)
    {
        $dossier = Dossier::findOrFail($id);

        // Vérifier les permissions
        if (!Auth::user()->est_admin && $dossier->user_id !== Auth::id()) {
            abort(403, 'Accès non autorisé');
        }

        $dossier->update(['statut' => 'archive']);

        // Archiver automatiquement tous les documents du dossier
        $dossier->documents()->update(['statut' => 'archive']);

        // Log de l'activité
        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'archivage',
            'table_name' => 'dossiers',
            'record_id' => $dossier->id,
            'description' => "Archivage du dossier : {$dossier->nom_dossier}"
        ]);

        return redirect()->back()->with('success', 'Dossier archivé avec succès');
    }

    /**
     * Supprimer un dossier (admin seulement)
     */
    public function destroy($id)
    {
        if (!Auth::user()->est_admin) {
            abort(403, 'Seuls les administrateurs peuvent supprimer des dossiers');
        }

        $dossier = Dossier::findOrFail($id);

        // Supprimer tous les documents du dossier
        foreach ($dossier->documents as $document) {
            if (Storage::disk('public')->exists($document->chemin)) {
                Storage::disk('public')->delete($document->chemin);
            }
            $document->delete();
        }

        // Log de l'activité avant suppression
        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'suppression',
            'table_name' => 'dossiers',
            'record_id' => $dossier->id,
            'description' => "Suppression définitive du dossier : {$dossier->nom_dossier}"
        ]);

        $dossier->delete();

        return redirect()->route('dossiers.index')
                        ->with('success', 'Dossier supprimé définitivement');
    }

    /**
     * Générer un numéro de dossier unique
     */
    private function genererNumeroDossier()
    {
        $annee = date('Y');
        $compteur = Dossier::whereYear('created_at', $annee)->count() + 1;
        
        return $annee . '-' . str_pad($compteur, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Rechercher des dossiers
     */
    public function rechercher(Request $request)
    {
        $query = Dossier::with(['champActivite', 'user', 'documents']);

        if ($request->filled('numero_dossier')) {
            $query->where('numero_dossier', 'LIKE', '%' . $request->numero_dossier . '%');
        }

        if ($request->filled('client_nom')) {
            $query->where('client_nom', 'LIKE', '%' . $request->client_nom . '%');
        }

        if ($request->filled('champ_activite_id')) {
            $query->where('champ_activite_id', $request->champ_activite_id);
        }

        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        if ($request->filled('date_debut')) {
            $query->whereDate('created_at', '>=', $request->date_debut);
        }

        if ($request->filled('date_fin')) {
            $query->whereDate('created_at', '<=', $request->date_fin);
        }

        $dossiers = $query->orderBy('created_at', 'desc')->paginate(20);
        $champs_activites = ChampActivite::where('actif', true)->get();

        return view('pages.dossiers.recherche', compact('dossiers', 'champs_activites'));
    }
}
