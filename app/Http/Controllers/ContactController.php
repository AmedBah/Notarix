<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ContactController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Afficher l'annuaire téléphonique
     */
    public function index()
    {
        // Pour l'instant, nous utilisons une liste statique
        // À terme, cela pourrait être stocké en base de données
        $contacts = [
            [
                'id' => 1,
                'nom' => 'Tribunal de Grande Instance',
                'categorie' => 'Justice',
                'telephone' => '01 23 45 67 89',
                'email' => 'contact@tgi-paris.justice.fr',
                'adresse' => '4 Boulevard du Palais, 75001 Paris',
                'notes' => 'Tribunal principal - Affaires civiles et pénales',
            ],
            [
                'id' => 2,
                'nom' => 'Chambre des Notaires',
                'categorie' => 'Professionnel',
                'telephone' => '01 34 56 78 90',
                'email' => 'info@chambre-notaires.fr',
                'adresse' => '12 Avenue Victoria, 75001 Paris',
                'notes' => 'Organisation professionnelle des notaires',
            ],
            [
                'id' => 3,
                'nom' => 'Service des Hypothèques',
                'categorie' => 'Administration',
                'telephone' => '01 45 67 89 01',
                'email' => 'hypotheques@dgfip.finances.gouv.fr',
                'adresse' => '2 Rue de la Paix, 75002 Paris',
                'notes' => 'Formalités hypothécaires et publicité foncière',
            ],
            [
                'id' => 4,
                'nom' => 'Tribunal de Commerce',
                'categorie' => 'Justice',
                'telephone' => '01 56 78 90 12',
                'email' => 'greffe@tc-paris.fr',
                'adresse' => '1 Quai de Corse, 75004 Paris',
                'notes' => 'Affaires commerciales - Registre du commerce',
            ],
            [
                'id' => 5,
                'nom' => 'SAFER',
                'categorie' => 'Immobilier',
                'telephone' => '01 67 89 01 23',
                'email' => 'contact@safer-idf.com',
                'adresse' => '8 Avenue de l\'Opéra, 75001 Paris',
                'notes' => 'Société d\'aménagement foncier et d\'établissement rural',
            ],
            [
                'id' => 6,
                'nom' => 'Expert Immobilier Dupont',
                'categorie' => 'Expert',
                'telephone' => '01 78 90 12 34',
                'email' => 'dupont@expert-immo.fr',
                'adresse' => '15 Rue de Rivoli, 75001 Paris',
                'notes' => 'Expert en évaluation immobilière - Agréé près les tribunaux',
            ],
        ];

        return view('pages.contacts', compact('contacts'));
    }

    /**
     * Afficher le formulaire de création d'un contact
     */
    public function create()
    {
        $categories = [
            'Justice',
            'Administration',
            'Professionnel',
            'Expert',
            'Immobilier',
            'Banque',
            'Assurance',
            'Autres',
        ];

        return view('pages.contact-create', compact('categories'));
    }

    /**
     * Enregistrer un nouveau contact
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nom' => 'required|string|max:255',
            'categorie' => 'required|string|max:100',
            'telephone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'adresse' => 'nullable|string|max:500',
            'notes' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                           ->withErrors($validator)
                           ->withInput();
        }

        // Pour l'instant, nous simulons la création
        // À terme, cela sera stocké en base de données

        return redirect()->route('contacts.index')
                        ->with('success', 'Contact ajouté avec succès.');
    }

    /**
     * Afficher les détails d'un contact
     */
    public function show($id)
    {
        // Simulation d'un contact
        $contact = [
            'id' => $id,
            'nom' => 'Tribunal de Grande Instance',
            'categorie' => 'Justice',
            'telephone' => '01 23 45 67 89',
            'email' => 'contact@tgi-paris.justice.fr',
            'adresse' => '4 Boulevard du Palais, 75001 Paris',
            'notes' => 'Tribunal principal - Affaires civiles et pénales',
            'created_at' => '2024-01-15',
        ];

        return view('pages.contact-details', compact('contact'));
    }

    /**
     * Afficher le formulaire d'édition d'un contact
     */
    public function edit($id)
    {
        // Simulation d'un contact
        $contact = [
            'id' => $id,
            'nom' => 'Tribunal de Grande Instance',
            'categorie' => 'Justice',
            'telephone' => '01 23 45 67 89',
            'email' => 'contact@tgi-paris.justice.fr',
            'adresse' => '4 Boulevard du Palais, 75001 Paris',
            'notes' => 'Tribunal principal - Affaires civiles et pénales',
        ];

        $categories = [
            'Justice',
            'Administration',
            'Professionnel',
            'Expert',
            'Immobilier',
            'Banque',
            'Assurance',
            'Autres',
        ];

        return view('pages.contact-edit', compact('contact', 'categories'));
    }

    /**
     * Mettre à jour un contact
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'nom' => 'required|string|max:255',
            'categorie' => 'required|string|max:100',
            'telephone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'adresse' => 'nullable|string|max:500',
            'notes' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                           ->withErrors($validator)
                           ->withInput();
        }

        // Pour l'instant, nous simulons la mise à jour
        // À terme, cela sera mis à jour en base de données

        return redirect()->route('contacts.index')
                        ->with('success', 'Contact mis à jour avec succès.');
    }

    /**
     * Supprimer un contact
     */
    public function destroy($id)
    {
        // Pour l'instant, nous simulons la suppression
        // À terme, cela sera supprimé de la base de données

        return redirect()->route('contacts.index')
                        ->with('success', 'Contact supprimé avec succès.');
    }

    /**
     * Rechercher des contacts
     */
    public function search(Request $request)
    {
        $query = $request->get('q');
        $categorie = $request->get('categorie');

        // Simulation de recherche
        $contacts = [
            [
                'id' => 1,
                'nom' => 'Tribunal de Grande Instance',
                'categorie' => 'Justice',
                'telephone' => '01 23 45 67 89',
                'email' => 'contact@tgi-paris.justice.fr',
                'adresse' => '4 Boulevard du Palais, 75001 Paris',
                'notes' => 'Tribunal principal - Affaires civiles et pénales',
            ],
        ];

        return view('pages.contacts', compact('contacts', 'query', 'categorie'));
    }

    /**
     * Exporter l'annuaire (CSV, PDF, etc.)
     */
    public function export($format = 'csv')
    {
        // Simulation d'export
        // À terme, générer le fichier selon le format demandé
        
        return redirect()->back()
                        ->with('success', 'Annuaire exporté avec succès.');
    }
}
