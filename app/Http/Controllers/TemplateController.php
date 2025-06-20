<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class TemplateController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Afficher la liste des templates
     */
    public function index()
    {
        // Pour l'instant, nous utilisons une liste statique
        // À terme, cela pourrait être stocké en base de données
        $templates = [
            [
                'id' => 1,
                'nom' => 'Acte de Vente Immobilière',
                'type' => 'acte',
                'categorie' => 'Droit Immobilier',
                'description' => 'Template standard pour les actes de vente immobilière',
                'fichier' => 'acte_vente_immobiliere.docx',
                'created_at' => '2024-01-15',
            ],
            [
                'id' => 2,
                'nom' => 'Contrat de Société',
                'type' => 'acte',
                'categorie' => 'Droit Commercial',
                'description' => 'Template pour la création de sociétés',
                'fichier' => 'contrat_societe.docx',
                'created_at' => '2024-01-10',
            ],
            [
                'id' => 3,
                'nom' => 'Courrier de Relance',
                'type' => 'courrier',
                'categorie' => 'Administration',
                'description' => 'Template de courrier de relance standard',
                'fichier' => 'courrier_relance.docx',
                'created_at' => '2024-01-08',
            ],
            [
                'id' => 4,
                'nom' => 'Procuration Générale',
                'type' => 'acte',
                'categorie' => 'Droit Civil',
                'description' => 'Template de procuration générale',
                'fichier' => 'procuration_generale.docx',
                'created_at' => '2024-01-05',
            ],
        ];

        return view('pages.templates', compact('templates'));
    }

    /**
     * Afficher le formulaire de création d'un template
     */
    public function create()
    {
        $categories = [
            'Droit Civil',
            'Droit Commercial', 
            'Droit Immobilier',
            'Droit des Affaires',
            'Administration',
            'Authentifications',
        ];

        $types = ['acte', 'courrier'];

        return view('pages.ajouter', compact('categories', 'types')); // Utilise la vue ajouter existante
    }

    /**
     * Enregistrer un nouveau template
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nom' => 'required|string|max:255',
            'type' => 'required|in:acte,courrier',
            'categorie' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'fichier' => 'required|file|mimes:doc,docx,pdf|max:10240', // 10MB max
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                           ->withErrors($validator)
                           ->withInput();
        }

        // Stocker le fichier
        if ($request->hasFile('fichier')) {
            $file = $request->file('fichier');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('templates', $filename, 'public');
        }

        // Pour l'instant, nous simulons la création
        // À terme, cela sera stocké en base de données

        return redirect()->route('templates.index')
                        ->with('success', 'Template créé avec succès.');
    }

    /**
     * Afficher les détails d'un template
     */
    public function show($id)
    {
        // Simulation d'un template
        $template = [
            'id' => $id,
            'nom' => 'Acte de Vente Immobilière',
            'type' => 'acte',
            'categorie' => 'Droit Immobilier',
            'description' => 'Template standard pour les actes de vente immobilière',
            'fichier' => 'acte_vente_immobiliere.docx',
            'created_at' => '2024-01-15',
        ];

        return view('pages.page', compact('template')); // Utilise la vue page générique existante
    }

    /**
     * Télécharger un template
     */
    public function download($id)
    {
        // Simulation - à terme, récupérer le template depuis la base de données
        $filename = 'acte_vente_immobiliere.docx';
        $filePath = 'templates/' . $filename;

        if (Storage::disk('public')->exists($filePath)) {
            return Storage::disk('public')->download($filePath);
        }

        return redirect()->back()->with('error', 'Fichier non trouvé.');
    }

    /**
     * Afficher le formulaire d'édition d'un template
     */
    public function edit($id)
    {
        // Simulation d'un template
        $template = [
            'id' => $id,
            'nom' => 'Acte de Vente Immobilière',
            'type' => 'acte',
            'categorie' => 'Droit Immobilier',
            'description' => 'Template standard pour les actes de vente immobilière',
            'fichier' => 'acte_vente_immobiliere.docx',
        ];

        $categories = [
            'Droit Civil',
            'Droit Commercial', 
            'Droit Immobilier',
            'Droit des Affaires',
            'Administration',
            'Authentifications',
        ];

        $types = ['acte', 'courrier'];

        return view('pages.settings', compact('template', 'categories', 'types')); // Utilise la vue settings pour l'édition
    }

    /**
     * Mettre à jour un template
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'nom' => 'required|string|max:255',
            'type' => 'required|in:acte,courrier',
            'categorie' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'fichier' => 'nullable|file|mimes:doc,docx,pdf|max:10240', // 10MB max
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                           ->withErrors($validator)
                           ->withInput();
        }

        // Mettre à jour le fichier si fourni
        if ($request->hasFile('fichier')) {
            $file = $request->file('fichier');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('templates', $filename, 'public');
        }

        // Pour l'instant, nous simulons la mise à jour
        // À terme, cela sera mis à jour en base de données

        return redirect()->route('templates.index')
                        ->with('success', 'Template mis à jour avec succès.');
    }

    /**
     * Supprimer un template
     */
    public function destroy($id)
    {
        // Pour l'instant, nous simulons la suppression
        // À terme, supprimer le fichier du stockage et l'enregistrement de la base de données

        return redirect()->route('templates.index')
                        ->with('success', 'Template supprimé avec succès.');
    }

    /**
     * Rechercher des templates
     */
    public function search(Request $request)
    {
        $query = $request->get('q');
        $type = $request->get('type');
        $categorie = $request->get('categorie');

        // Simulation de recherche
        $templates = [
            [
                'id' => 1,
                'nom' => 'Acte de Vente Immobilière',
                'type' => 'acte',
                'categorie' => 'Droit Immobilier',
                'description' => 'Template standard pour les actes de vente immobilière',
                'fichier' => 'acte_vente_immobiliere.docx',
                'created_at' => '2024-01-15',
            ],
        ];

        return view('pages.templates', compact('templates', 'query', 'type', 'categorie'));
    }
}
