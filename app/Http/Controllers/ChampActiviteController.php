<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ChampActiviteController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Afficher la liste des champs d'activité
     */
    public function index()
    {
        // Pour l'instant, nous utilisons une liste statique
        // À terme, cela pourrait être stocké en base de données
        $champsActivite = [
            ['id' => 1, 'nom' => 'Droit Civil', 'description' => 'Contrats, successions, famille'],
            ['id' => 2, 'nom' => 'Droit Commercial', 'description' => 'Sociétés, fonds de commerce'],
            ['id' => 3, 'nom' => 'Droit Immobilier', 'description' => 'Ventes, achats, baux'],
            ['id' => 4, 'nom' => 'Droit des Affaires', 'description' => 'Création d\'entreprises, fusions'],
            ['id' => 5, 'nom' => 'Authentifications', 'description' => 'Actes authentiques, certifications'],
            ['id' => 6, 'nom' => 'Conseils Juridiques', 'description' => 'Consultations, avis juridiques'],
        ];

        return view('pages.champs-activite', compact('champsActivite'));
    }

    /**
     * Afficher le formulaire de création d'un champ d'activité
     */
    public function create()
    {
        return view('pages.ajouter'); // Utilise la vue ajouter existante
    }

    /**
     * Enregistrer un nouveau champ d'activité
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nom' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                           ->withErrors($validator)
                           ->withInput();
        }

        // Pour l'instant, nous simulons la création
        // À terme, cela sera stocké en base de données
        
        return redirect()->route('champs-activite.index')
                        ->with('success', 'Champ d\'activité créé avec succès.');
    }

    /**
     * Afficher les détails d'un champ d'activité
     */
    public function show($id)
    {
        // Simulation d'un champ d'activité
        $champActivite = [
            'id' => $id,
            'nom' => 'Droit Civil',
            'description' => 'Contrats, successions, famille',
            'created_at' => now(),
        ];

        return view('pages.page', compact('champActivite')); // Utilise la vue page générique existante
    }

    /**
     * Afficher le formulaire d'édition d'un champ d'activité
     */
    public function edit($id)
    {
        // Simulation d'un champ d'activité
        $champActivite = [
            'id' => $id,
            'nom' => 'Droit Civil',
            'description' => 'Contrats, successions, famille',
        ];

        return view('pages.settings', compact('champActivite')); // Utilise la vue settings pour l'édition
    }

    /**
     * Mettre à jour un champ d'activité
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'nom' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                           ->withErrors($validator)
                           ->withInput();
        }

        // Pour l'instant, nous simulons la mise à jour
        // À terme, cela sera mis à jour en base de données

        return redirect()->route('champs-activite.index')
                        ->with('success', 'Champ d\'activité mis à jour avec succès.');
    }

    /**
     * Supprimer un champ d'activité
     */
    public function destroy($id)
    {
        // Pour l'instant, nous simulons la suppression
        // À terme, cela sera supprimé de la base de données

        return redirect()->route('champs-activite.index')
                        ->with('success', 'Champ d\'activité supprimé avec succès.');
    }

    /**
     * Rechercher des champs d'activité
     */
    public function search(Request $request)
    {
        $query = $request->get('q');
        
        // Simulation de recherche
        $champsActivite = [
            ['id' => 1, 'nom' => 'Droit Civil', 'description' => 'Contrats, successions, famille'],
            ['id' => 2, 'nom' => 'Droit Commercial', 'description' => 'Sociétés, fonds de commerce'],
        ];

        return view('pages.champs-activite', compact('champsActivite', 'query'));
    }
}
