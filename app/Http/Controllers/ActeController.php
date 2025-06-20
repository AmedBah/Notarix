<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Template;
use App\Models\Document;
use App\Models\ChampActivite;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ActeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Afficher la liste des actes
     */
    public function index()
    {
        $actes = Document::where('type_document', 'acte')
                        ->with(['dossier', 'user'])
                        ->orderBy('created_at', 'desc')
                        ->paginate(20);

        return view('pages.actes.index', compact('actes'));
    }

    /**
     * Afficher le formulaire de création d'un acte
     */
    public function create()
    {
        $templates = Template::where('type', 'acte')
                            ->where('actif', true)
                            ->orderBy('nom')
                            ->get();
        
        $champs_activites = ChampActivite::all();

        return view('pages.actes.create', compact('templates', 'champs_activites'));
    }

    /**
     * Créer un nouvel acte à partir d'un template
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nom_acte' => 'required|string|max:255',
            'template_id' => 'required|exists:templates,id',
            'champ_activite_id' => 'required|exists:champs_activites,id',
            'dossier_id' => 'nullable|exists:dossiers,id',
            'parties' => 'required|array',
            'parties.*.nom' => 'required|string|max:255',
            'parties.*.prenom' => 'required|string|max:255',
            'parties.*.qualite' => 'required|string|max:100',
            'champs_personnalises' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                           ->withErrors($validator)
                           ->withInput();
        }

        try {
            $template = Template::findOrFail($request->template_id);
            
            // Générer le contenu de l'acte à partir du template
            $contenu_acte = $this->genererContenuActe($template, $request->all());

            // Créer le document acte
            $document = Document::create([
                'nom_fichier' => $request->nom_acte . '.docx',
                'taille_fichier' => strlen($contenu_acte),
                'type_fichier' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'type_document' => 'acte',
                'template_id' => $request->template_id,
                'champ_activite_id' => $request->champ_activite_id,
                'dossier_id' => $request->dossier_id,
                'user_id' => Auth::id(),
                'parties' => json_encode($request->parties),
                'champs_personnalises' => json_encode($request->champs_personnalises),
                'statut' => 'brouillon',
                'path' => 'actes/' . uniqid() . '.docx'
            ]);

            // Sauvegarder le fichier
            Storage::put($document->path, $contenu_acte);

            // Enregistrer l'activité
            app(TracabiliteController::class)->logActivity($document->id, 'create', [
                'type' => 'acte',
                'template_used' => $template->nom
            ]);

            return redirect()->route('actes.show', $document->id)
                           ->with('success', 'Acte créé avec succès.');

        } catch (\Exception $e) {
            return redirect()->back()
                           ->with('error', 'Erreur lors de la création : ' . $e->getMessage())
                           ->withInput();
        }
    }

    /**
     * Afficher un acte
     */
    public function show($id)
    {
        $acte = Document::where('type_document', 'acte')
                       ->with(['template', 'dossier', 'user', 'champActivite'])
                       ->findOrFail($id);

        // Enregistrer la consultation
        app(TracabiliteController::class)->logActivity($id, 'view', [
            'type' => 'acte'
        ]);

        return view('pages.actes.show', compact('acte'));
    }

    /**
     * Afficher le formulaire d'édition d'un acte
     */
    public function edit($id)
    {
        $acte = Document::where('type_document', 'acte')
                       ->with(['template'])
                       ->findOrFail($id);

        $templates = Template::where('type', 'acte')
                            ->where('actif', true)
                            ->orderBy('nom')
                            ->get();
        
        $champs_activites = ChampActivite::all();

        return view('pages.actes.edit', compact('acte', 'templates', 'champs_activites'));
    }

    /**
     * Mettre à jour un acte
     */
    public function update(Request $request, $id)
    {
        $acte = Document::where('type_document', 'acte')->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'nom_acte' => 'required|string|max:255',
            'champ_activite_id' => 'required|exists:champs_activites,id',
            'parties' => 'required|array',
            'parties.*.nom' => 'required|string|max:255',
            'parties.*.prenom' => 'required|string|max:255',
            'parties.*.qualite' => 'required|string|max:100',
            'champs_personnalises' => 'nullable|array',
            'statut' => 'required|in:brouillon,finalise,signe',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                           ->withErrors($validator)
                           ->withInput();
        }

        try {
            // Sauvegarder l'ancienne version pour la traçabilité
            $ancienne_version = $acte->toArray();

            // Régénérer le contenu si nécessaire
            if ($request->has('regenerer_contenu') && $acte->template) {
                $nouveau_contenu = $this->genererContenuActe($acte->template, $request->all());
                Storage::put($acte->path, $nouveau_contenu);
                $acte->taille_fichier = strlen($nouveau_contenu);
            }

            $acte->update([
                'nom_fichier' => $request->nom_acte . '.docx',
                'champ_activite_id' => $request->champ_activite_id,
                'parties' => json_encode($request->parties),
                'champs_personnalises' => json_encode($request->champs_personnalises),
                'statut' => $request->statut,
            ]);

            // Enregistrer l'activité
            app(TracabiliteController::class)->logActivity($acte->id, 'modify', [
                'type' => 'acte',
                'changes' => array_diff_assoc($request->all(), $ancienne_version)
            ]);

            return redirect()->route('actes.show', $id)
                           ->with('success', 'Acte mis à jour avec succès.');

        } catch (\Exception $e) {
            return redirect()->back()
                           ->with('error', 'Erreur lors de la mise à jour : ' . $e->getMessage())
                           ->withInput();
        }
    }

    /**
     * Supprimer un acte
     */
    public function destroy($id)
    {
        try {
            $acte = Document::where('type_document', 'acte')->findOrFail($id);
            
            // Enregistrer l'activité avant suppression
            app(TracabiliteController::class)->logActivity($id, 'delete', [
                'type' => 'acte',
                'nom_fichier' => $acte->nom_fichier
            ]);

            // Supprimer le fichier
            if (Storage::exists($acte->path)) {
                Storage::delete($acte->path);
            }

            $acte->delete();

            return redirect()->route('actes.index')
                           ->with('success', 'Acte supprimé avec succès.');

        } catch (\Exception $e) {
            return redirect()->back()
                           ->with('error', 'Erreur lors de la suppression : ' . $e->getMessage());
        }
    }

    /**
     * Dupliquer un acte
     */
    public function duplicate($id)
    {
        try {
            $acte_original = Document::where('type_document', 'acte')->findOrFail($id);
            
            $nouvel_acte = $acte_original->replicate();
            $nouvel_acte->nom_fichier = 'Copie de ' . $acte_original->nom_fichier;
            $nouvel_acte->statut = 'brouillon';
            $nouvel_acte->path = 'actes/' . uniqid() . '.docx';
            $nouvel_acte->created_at = now();
            $nouvel_acte->save();

            // Copier le fichier
            if (Storage::exists($acte_original->path)) {
                Storage::copy($acte_original->path, $nouvel_acte->path);
            }

            // Enregistrer l'activité
            app(TracabiliteController::class)->logActivity($nouvel_acte->id, 'create', [
                'type' => 'acte',
                'duplicated_from' => $id
            ]);

            return redirect()->route('actes.edit', $nouvel_acte->id)
                           ->with('success', 'Acte dupliqué avec succès.');

        } catch (\Exception $e) {
            return redirect()->back()
                           ->with('error', 'Erreur lors de la duplication : ' . $e->getMessage());
        }
    }

    /**
     * Finaliser un acte (changement de statut)
     */
    public function finaliser($id)
    {
        try {
            $acte = Document::where('type_document', 'acte')->findOrFail($id);
            
            if ($acte->statut !== 'brouillon') {
                return redirect()->back()
                               ->with('error', 'Seuls les actes en brouillon peuvent être finalisés.');
            }

            $acte->update(['statut' => 'finalise']);

            // Enregistrer l'activité
            app(TracabiliteController::class)->logActivity($id, 'finalize', [
                'type' => 'acte'
            ]);

            return redirect()->route('actes.show', $id)
                           ->with('success', 'Acte finalisé avec succès.');

        } catch (\Exception $e) {
            return redirect()->back()
                           ->with('error', 'Erreur lors de la finalisation : ' . $e->getMessage());
        }
    }

    /**
     * Générer un PDF à partir d'un acte
     */
    public function generatePdf($id)
    {
        try {
            $acte = Document::where('type_document', 'acte')->findOrFail($id);
            
            // Logique de génération PDF
            // À implémenter selon les besoins (DomPDF, wkhtmltopdf, etc.)
            
            $pdf_path = 'actes/pdf/' . pathinfo($acte->nom_fichier, PATHINFO_FILENAME) . '.pdf';
            
            // Enregistrer l'activité
            app(TracabiliteController::class)->logActivity($id, 'generate_pdf', [
                'type' => 'acte',
                'pdf_path' => $pdf_path
            ]);

            return response()->download(storage_path('app/' . $pdf_path));

        } catch (\Exception $e) {
            return redirect()->back()
                           ->with('error', 'Erreur lors de la génération PDF : ' . $e->getMessage());
        }
    }

    /**
     * Prévisualiser un acte avant création
     */
    public function preview(Request $request)
    {
        try {
            $template = Template::findOrFail($request->template_id);
            $contenu_preview = $this->genererContenuActe($template, $request->all());

            return response()->json([
                'success' => true,
                'contenu' => $contenu_preview
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la prévisualisation : ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Générer le contenu d'un acte à partir d'un template
     */
    private function genererContenuActe($template, $donnees)
    {
        $contenu = $template->contenu;

        // Remplacer les variables du template
        $variables = [
            '{{nom_acte}}' => $donnees['nom_acte'] ?? '',
            '{{date_acte}}' => now()->format('d/m/Y'),
            '{{lieu_acte}}' => 'Notaire', // À adapter
            '{{notaire}}' => Auth::user()->nom . ' ' . Auth::user()->prenom,
        ];

        // Remplacer les parties
        if (isset($donnees['parties']) && is_array($donnees['parties'])) {
            foreach ($donnees['parties'] as $index => $partie) {
                $variables["{{partie_{$index}_nom}}"] = $partie['nom'] ?? '';
                $variables["{{partie_{$index}_prenom}}"] = $partie['prenom'] ?? '';
                $variables["{{partie_{$index}_qualite}}"] = $partie['qualite'] ?? '';
            }
        }

        // Remplacer les champs personnalisés
        if (isset($donnees['champs_personnalises']) && is_array($donnees['champs_personnalises'])) {
            foreach ($donnees['champs_personnalises'] as $champ => $valeur) {
                $variables["{{$champ}}"] = $valeur;
            }
        }

        // Effectuer les remplacements
        foreach ($variables as $variable => $valeur) {
            $contenu = str_replace($variable, $valeur, $contenu);
        }

        return $contenu;
    }
}
