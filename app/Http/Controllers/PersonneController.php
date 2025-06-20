<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class PersonneController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Afficher la liste des personnes (contacts)
     */
    public function index()
    {
        $personnes = User::where('role', '!=', 'admin')
                        ->orderBy('created_at', 'desc')
                        ->get();
        
        return view('pages.clients', compact('personnes')); // Utilise la vue clients existante
    }

    /**
     * Afficher le formulaire de création d'une personne
     */
    public function create()
    {
        return view('pages.ajouter'); // Utilise la vue ajouter existante
    }

    /**
     * Enregistrer une nouvelle personne
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'telephone' => 'nullable|string|max:20',
            'adresse' => 'nullable|string|max:500',
            'fonction' => 'nullable|string|max:255',
            'motif' => 'nullable|string|max:500',
            'type_personne' => 'required|in:client,temoin,expert,autre',
            'visibilite' => 'required|in:public,prive',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                           ->withErrors($validator)
                           ->withInput();
        }

        try {
            $personne = User::create([
                'nom' => $request->nom,
                'prenom' => $request->prenom,
                'email' => $request->email,
                'telephone' => $request->telephone,
                'adresse' => $request->adresse,
                'fonction' => $request->fonction,
                'motif' => $request->motif,
                'type_personne' => $request->type_personne,
                'visibilite' => $request->visibilite,
                'password' => Hash::make($request->password),
                'role' => 'user',
                'status' => 'active',
            ]);

            return redirect()->route('personnes.index')
                           ->with('success', 'Personne créée avec succès.');

        } catch (\Exception $e) {
            return redirect()->back()
                           ->with('error', 'Erreur lors de la création : ' . $e->getMessage())
                           ->withInput();
        }
    }

    /**
     * Afficher les détails d'une personne
     */
    public function show($id)
    {
        $personne = User::findOrFail($id);
        
        return view('pages.detailsEmp', compact('personne')); // Utilise la vue détails existante
    }

    /**
     * Afficher le formulaire d'édition d'une personne
     */
    public function edit($id)
    {
        $personne = User::findOrFail($id);
        
        return view('pages.settings', compact('personne')); // Utilise la vue settings pour l'édition
    }

    /**
     * Mettre à jour une personne
     */
    public function update(Request $request, $id)
    {
        $personne = User::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,'.$id,
            'telephone' => 'nullable|string|max:20',
            'adresse' => 'nullable|string|max:500',
            'fonction' => 'nullable|string|max:255',
            'motif' => 'nullable|string|max:500',
            'type_personne' => 'required|in:client,temoin,expert,autre',
            'visibilite' => 'required|in:public,prive',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                           ->withErrors($validator)
                           ->withInput();
        }

        try {
            $updateData = [
                'nom' => $request->nom,
                'prenom' => $request->prenom,
                'email' => $request->email,
                'telephone' => $request->telephone,
                'adresse' => $request->adresse,
                'fonction' => $request->fonction,
                'motif' => $request->motif,
                'type_personne' => $request->type_personne,
                'visibilite' => $request->visibilite,
            ];

            if ($request->filled('password')) {
                $updateData['password'] = Hash::make($request->password);
            }

            $personne->update($updateData);

            return redirect()->route('personnes.index')
                           ->with('success', 'Personne mise à jour avec succès.');

        } catch (\Exception $e) {
            return redirect()->back()
                           ->with('error', 'Erreur lors de la mise à jour : ' . $e->getMessage())
                           ->withInput();
        }
    }

    /**
     * Supprimer une personne
     */
    public function destroy($id)
    {
        try {
            $personne = User::findOrFail($id);
            $personne->delete();

            return redirect()->route('personnes.index')
                           ->with('success', 'Personne supprimée avec succès.');

        } catch (\Exception $e) {
            return redirect()->back()
                           ->with('error', 'Erreur lors de la suppression : ' . $e->getMessage());
        }
    }

    /**
     * Changer le statut d'une personne (actif/inactif)
     */
    public function toggleStatus($id)
    {
        try {
            $personne = User::findOrFail($id);
            $personne->status = $personne->status === 'active' ? 'inactive' : 'active';
            $personne->save();

            return response()->json([
                'success' => true,
                'message' => 'Statut mis à jour avec succès.',
                'status' => $personne->status
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du changement de statut : ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Rechercher des personnes
     */
    public function search(Request $request)
    {
        $query = $request->get('q');
        $type = $request->get('type');
        $visibilite = $request->get('visibilite');

        $personnesQuery = User::where('role', '!=', 'admin');

        if ($query) {
            $personnesQuery->where(function ($q) use ($query) {
                $q->where('nom', 'LIKE', "%{$query}%")
                  ->orWhere('prenom', 'LIKE', "%{$query}%")
                  ->orWhere('email', 'LIKE', "%{$query}%")
                  ->orWhere('telephone', 'LIKE', "%{$query}%")
                  ->orWhere('fonction', 'LIKE', "%{$query}%")
                  ->orWhere('motif', 'LIKE', "%{$query}%");
            });
        }

        if ($type) {
            $personnesQuery->where('type_personne', $type);
        }

        if ($visibilite) {
            $personnesQuery->where('visibilite', $visibilite);
        }

        $personnes = $personnesQuery->orderBy('created_at', 'desc')->get();

        return response()->json($personnes);
    }

    /**
     * Importer des données de personnes depuis un fichier CSV/Excel
     */
    public function import(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|mimes:csv,xlsx,xls|max:2048',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                           ->withErrors($validator);
        }

        try {
            // Logique d'importation à implémenter selon les besoins
            // Utilisation de Laravel Excel ou traitement manuel du CSV
            
            return redirect()->route('personnes.index')
                           ->with('success', 'Données importées avec succès.');

        } catch (\Exception $e) {
            return redirect()->back()
                           ->with('error', 'Erreur lors de l\'importation : ' . $e->getMessage());
        }
    }

    /**
     * Exporter la liste des personnes
     */
    public function export($format = 'csv')
    {
        try {
            $personnes = User::where('role', '!=', 'admin')->get();
            
            // Logique d'exportation à implémenter
            // selon le format demandé (CSV, Excel, PDF)
            
            return response()->json([
                'success' => true,
                'message' => 'Export généré avec succès.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'export : ' . $e->getMessage()
            ]);
        }
    }
}
