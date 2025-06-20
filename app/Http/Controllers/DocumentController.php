<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Document;
use App\Models\Dossier;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class DocumentController extends Controller
{
    /**
     * Afficher la liste des documents
     */
    public function index(Request $request)
    {
        $query = Document::with(['dossier', 'createdBy']);
        
        // Filtrage par statut
        if ($request->has('statut') && $request->statut !== '') {
            $query->where('statut', $request->statut);
        }
        
        // Filtrage par type
        if ($request->has('type') && $request->type !== '') {
            $query->where('type', $request->type);
        }
        
        // Recherche textuelle
        if ($request->has('search') && $request->search !== '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nom', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('mots_cles', 'like', "%{$search}%");
            });
        }
        
        $documents = $query->orderBy('created_at', 'desc')->paginate(20);
        
        return view('pages.documents', compact('documents'));
    }

    /**
     * Afficher le formulaire de création d'un document
     */
    public function create()
    {
        $dossiers = Dossier::where('statut', 'ouvert')->orderBy('nom')->get();
        return view('pages.ajouter', compact('dossiers'));
    }

    /**
     * Enregistrer un nouveau document
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nom' => 'required|string|max:255',
            'fichier' => 'required|file|max:50000', // 50MB max
            'dossier_id' => 'required|exists:dossiers,id',
            'type' => 'required|string|max:100',
            'description' => 'nullable|string|max:1000',
            'mots_cles' => 'nullable|string|max:500',
            'confidentialite' => 'required|in:public,prive,confidentiel',
            'duree_conservation' => 'nullable|integer|min:1|max:99',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            // Upload du fichier
            $file = $request->file('fichier');
            $fileName = time() . '_' . Str::slug($request->nom) . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('documents', $fileName, 'public');

            // Création du document
            $document = Document::create([
                'nom' => $request->nom,
                'fichier_chemin' => $filePath,
                'fichier_nom_original' => $file->getClientOriginalName(),
                'fichier_taille' => $file->getSize(),
                'fichier_type' => $file->getMimeType(),
                'dossier_id' => $request->dossier_id,
                'type' => $request->type,
                'description' => $request->description,
                'mots_cles' => $request->mots_cles,
                'confidentialite' => $request->confidentialite,
                'duree_conservation' => $request->duree_conservation,
                'created_by' => Auth::id(),
                'statut' => 'actif',
            ]);

            // Enregistrement de l'activité
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'create',
                'description' => "Création du document : {$document->nom}",
                'model_type' => 'Document',
                'model_id' => $document->id,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            return redirect()->route('documents.show', $document->id)
                ->with('success', 'Document créé avec succès.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Erreur lors de la création du document : ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Afficher un document spécifique
     */
    public function show($id)
    {
        $document = Document::with(['dossier', 'createdBy', 'updatedBy'])->findOrFail($id);
        
        // Enregistrement de la consultation
        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'view',
            'description' => "Consultation du document : {$document->nom}",
            'model_type' => 'Document',
            'model_id' => $document->id,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return view('pages.editDoc', compact('document'));
    }

    /**
     * Télécharger un document
     */
    public function download($id)
    {
        $document = Document::findOrFail($id);
        
        // Vérifier si le fichier existe
        if (!Storage::disk('public')->exists($document->fichier_chemin)) {
            return redirect()->back()->with('error', 'Fichier introuvable.');
        }

        // Enregistrement du téléchargement
        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'download',
            'description' => "Téléchargement du document : {$document->nom}",
            'model_type' => 'Document',
            'model_id' => $document->id,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return Storage::disk('public')->download(
            $document->fichier_chemin,
            $document->fichier_nom_original
        );
    }

    /**
     * Archiver automatiquement un document
     */
    public function archiverAuto($id)
    {
        $document = Document::findOrFail($id);
        
        // Vérifier si le document peut être archivé automatiquement
        if ($document->duree_conservation && $document->created_at->addYears($document->duree_conservation)->isPast()) {
            $document->update([
                'statut' => 'archive_auto',
                'date_archivage' => now(),
                'updated_by' => Auth::id(),
            ]);

            // Enregistrement de l'activité
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'archive_auto',
                'description' => "Archivage automatique du document : {$document->nom}",
                'model_type' => 'Document',
                'model_id' => $document->id,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            return redirect()->back()->with('success', 'Document archivé automatiquement.');
        }

        return redirect()->back()->with('error', 'Ce document ne peut pas être archivé automatiquement.');
    }

    /**
     * Archiver manuellement un document
     */
    public function archiverManuel($id)
    {
        $document = Document::findOrFail($id);
        
        $document->update([
            'statut' => 'archive_manuel',
            'date_archivage' => now(),
            'updated_by' => Auth::id(),
        ]);

        // Enregistrement de l'activité
        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'archive_manual',
            'description' => "Archivage manuel du document : {$document->nom}",
            'model_type' => 'Document',
            'model_id' => $document->id,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return redirect()->back()->with('success', 'Document archivé manuellement.');
    }

    /**
     * Restaurer un document archivé
     */
    public function restore($id)
    {
        $document = Document::findOrFail($id);
        
        $document->update([
            'statut' => 'actif',
            'date_archivage' => null,
            'updated_by' => Auth::id(),
        ]);

        // Enregistrement de l'activité
        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'restore',
            'description' => "Restauration du document : {$document->nom}",
            'model_type' => 'Document',
            'model_id' => $document->id,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return redirect()->back()->with('success', 'Document restauré avec succès.');
    }

    /**
     * Supprimer un document
     */
    public function destroy($id)
    {
        $document = Document::findOrFail($id);
        
        // Supprimer le fichier physique
        if (Storage::disk('public')->exists($document->fichier_chemin)) {
            Storage::disk('public')->delete($document->fichier_chemin);
        }

        // Enregistrement de l'activité avant suppression
        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'delete',
            'description' => "Suppression du document : {$document->nom}",
            'model_type' => 'Document',
            'model_id' => $document->id,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        $document->delete();

        return redirect()->route('documents.index')
            ->with('success', 'Document supprimé avec succès.');
    }

    /**
     * Interface de scan de documents
     */
    public function scan()
    {
        $dossiers = Dossier::where('statut', 'ouvert')->orderBy('nom')->get();
        return view('pages.ajouter', compact('dossiers')); // Réutilise la vue d'ajout pour le scan
    }

    /**
     * Traiter un document scanné
     */
    public function processScan(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nom' => 'required|string|max:255',
            'fichier_scan' => 'required|file|mimes:pdf,jpg,jpeg,png|max:50000',
            'dossier_id' => 'required|exists:dossiers,id',
            'type' => 'required|string|max:100',
            'description' => 'nullable|string|max:1000',
            'confidentialite' => 'required|in:public,prive,confidentiel',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            // Upload du fichier scanné
            $file = $request->file('fichier_scan');
            $fileName = time() . '_scan_' . Str::slug($request->nom) . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('documents/scans', $fileName, 'public');

            // Création du document
            $document = Document::create([
                'nom' => $request->nom,
                'fichier_chemin' => $filePath,
                'fichier_nom_original' => $file->getClientOriginalName(),
                'fichier_taille' => $file->getSize(),
                'fichier_type' => $file->getMimeType(),
                'dossier_id' => $request->dossier_id,
                'type' => $request->type,
                'description' => $request->description,
                'confidentialite' => $request->confidentialite,
                'created_by' => Auth::id(),
                'statut' => 'actif',
                'is_scanned' => true,
            ]);

            // Enregistrement de l'activité
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'scan',
                'description' => "Scan et création du document : {$document->nom}",
                'model_type' => 'Document',
                'model_id' => $document->id,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            return redirect()->route('documents.show', $document->id)
                ->with('success', 'Document scanné et créé avec succès.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Erreur lors du scan du document : ' . $e->getMessage())
                ->withInput();
        }
    }
}
