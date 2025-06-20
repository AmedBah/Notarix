<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Afficher le profil de l'utilisateur connecté
     */
    public function profile()
    {
        $user = Auth::user();
        return view('pages.user', compact('user')); // Utilise la vue existante pour le profil
    }

    /**
     * Afficher le formulaire d'édition du profil
     */
    public function editProfile()
    {
        $user = Auth::user();
        return view('pages.settings', compact('user')); // Utilise la vue settings pour l'édition
    }

    /**
     * Mettre à jour le profil de l'utilisateur
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        
        $validator = Validator::make($request->all(), [
            'prenom' => 'required|string|max:255',
            'nom' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'telephone' => 'nullable|string|max:20',
            'adresse' => 'nullable|string|max:500',
            'preferences' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $user->update([
                'prenom' => $request->prenom,
                'nom' => $request->nom,
                'email' => $request->email,
                'telephone' => $request->telephone,
                'adresse' => $request->adresse,
                'preferences' => $request->preferences ?? [],
            ]);

            // Enregistrement de l'activité
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'update_profile',
                'description' => "Mise à jour du profil utilisateur",
                'model_type' => 'User',
                'model_id' => $user->id,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            return redirect()->route('users.profile')
                ->with('success', 'Profil mis à jour avec succès.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Erreur lors de la mise à jour du profil : ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Afficher le formulaire de changement de mot de passe
     */
    public function changePasswordForm()
    {
        return view('pages.settings'); // Utilise la vue settings pour changer le mot de passe
    }

    /**
     * Changer le mot de passe de l'utilisateur
     */
    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator);
        }

        $user = Auth::user();

        // Vérifier le mot de passe actuel
        if (!Hash::check($request->current_password, $user->password)) {
            return redirect()->back()
                ->withErrors(['current_password' => 'Le mot de passe actuel est incorrect.']);
        }

        try {
            $user->update([
                'password' => Hash::make($request->new_password),
            ]);

            // Enregistrement de l'activité
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'change_password',
                'description' => "Changement du mot de passe",
                'model_type' => 'User',
                'model_id' => $user->id,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            return redirect()->route('users.profile')
                ->with('success', 'Mot de passe changé avec succès.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Erreur lors du changement de mot de passe : ' . $e->getMessage());
        }
    }

    /**
     * Afficher la liste des utilisateurs (admin seulement)
     */
    public function index(Request $request)
    {
        $this->checkAdminPermission(); // Vérification des permissions admin
        
        $query = User::query();
        
        // Filtrage par statut
        if ($request->has('statut') && $request->statut !== '') {
            $query->where('statut', $request->statut);
        }
        
        // Filtrage par rôle
        if ($request->has('role') && $request->role !== '') {
            $query->where('role', $request->role);
        }
        
        // Recherche textuelle
        if ($request->has('search') && $request->search !== '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nom', 'like', "%{$search}%")
                  ->orWhere('prenom', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
          $users = $query->orderBy('nom')->orderBy('prenom')->paginate(20);
        
        return view('pages.listeUsers', compact('users'));
    }

    /**
     * Afficher le formulaire de création d'utilisateur (admin seulement)
     */
    public function create()
    {
        $this->checkAdminPermission();
        return view('pages.ajouter'); // Utilise la vue ajouter pour créer un utilisateur
    }

    /**
     * Enregistrer un nouveau utilisateur (admin seulement)
     */
    public function store(Request $request)
    {
        $this->checkAdminPermission();
        
        $validator = Validator::make($request->all(), [
            'prenom' => 'required|string|max:255',
            'nom' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:admin,utilisateur,lecteur',
            'telephone' => 'nullable|string|max:20',
            'adresse' => 'nullable|string|max:500',
            'statut' => 'required|in:actif,inactif,suspendu',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $user = User::create([
                'prenom' => $request->prenom,
                'nom' => $request->nom,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => $request->role,
                'telephone' => $request->telephone,
                'adresse' => $request->adresse,
                'statut' => $request->statut,
                'email_verified_at' => now(), // Auto-vérifier pour les comptes créés par admin
            ]);

            // Enregistrement de l'activité
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'create',
                'description' => "Création de l'utilisateur : {$user->prenom} {$user->nom}",
                'model_type' => 'User',
                'model_id' => $user->id,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            return redirect()->route('users.show', $user->id)
                ->with('success', 'Utilisateur créé avec succès.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Erreur lors de la création de l\'utilisateur : ' . $e->getMessage())                ->withInput();
        }
    }

    /**
     * Afficher un utilisateur spécifique (admin seulement)
     */
    public function show($id)
    {
        $this->checkAdminPermission();
        
        $user = User::findOrFail($id);
        
        // Statistiques de l'utilisateur
        $stats = [
            'documents_crees' => $user->documentsCreated()->count(),
            'dossiers_crees' => $user->dossiersCreated()->count(),
            'derniere_connexion' => $user->created_at, // Temporairement utilisé created_at
        ];
          return view('pages.detailsEmp', compact('user', 'stats')); // Utilise la vue détails employé
    }

    /**
     * Basculer le statut d'un utilisateur (admin seulement)
     */
    public function toggleStatus(Request $request, $id)
    {
        $this->checkAdminPermission();
        
        $user = User::findOrFail($id);
        
        // Ne pas permettre de désactiver le dernier admin
        if ($user->role === 'admin' && $user->statut === 'actif') {
            $activeAdmins = User::where('role', 'admin')->where('status', 'active')->count();
            if ($activeAdmins <= 1) {
                return redirect()->back()
                    ->with('error', 'Impossible de désactiver le dernier administrateur actif.');
            }
        }
        
        $nouveauStatut = $user->statut === 'actif' ? 'inactif' : 'actif';
        
        $user->update(['statut' => $nouveauStatut]);

        // Enregistrement de l'activité
        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'toggle_status',
            'description' => "Changement de statut de l'utilisateur {$user->prenom} {$user->nom} : {$nouveauStatut}",
            'model_type' => 'User',
            'model_id' => $user->id,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->back()            ->with('success', "Statut de l'utilisateur mis à jour : {$nouveauStatut}.");
    }

    /**
     * Supprimer un utilisateur (admin seulement)
     */
    public function destroy(Request $request, $id)
    {
        $this->checkAdminPermission();
        
        $user = User::findOrFail($id);
        
        // Ne pas permettre de supprimer le dernier admin
        if ($user->role === 'admin') {
            $activeAdmins = User::where('role', 'admin')->count();
            if ($activeAdmins <= 1) {
                return redirect()->back()
                    ->with('error', 'Impossible de supprimer le dernier administrateur.');
            }
        }
        
        // Ne pas permettre de se supprimer soi-même
        if ($user->id === Auth::id()) {
            return redirect()->back()
                ->with('error', 'Vous ne pouvez pas supprimer votre propre compte.');
        }

        // Enregistrement de l'activité avant suppression
        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'delete',
            'description' => "Suppression de l'utilisateur : {$user->prenom} {$user->nom}",
            'model_type' => 'User',
            'model_id' => $user->id,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        $user->delete();

        return redirect()->route('users.index')            ->with('success', 'Utilisateur supprimé avec succès.');
    }

    /**
     * Rechercher des utilisateurs (admin seulement)
     */
    public function search(Request $request)
    {
        $this->checkAdminPermission();
        
        $query = $request->get('q', '');
        
        if (empty($query)) {
            return response()->json([]);
        }
        
        $users = User::where('nom', 'like', "%{$query}%")
            ->orWhere('prenom', 'like', "%{$query}%")
            ->orWhere('email', 'like', "%{$query}%")
            ->limit(10)
            ->get(['id', 'nom', 'prenom', 'email', 'role', 'statut'])
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'nom_complet' => $user->prenom . ' ' . $user->nom,
                    'email' => $user->email,
                    'role' => $user->role,                    'statut' => $user->statut,
                ];
            });
        
        return response()->json($users);
    }

    /**
     * Méthode de vérification des permissions admin
     */
    private function checkAdminPermission()
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Accès non autorisé. Permissions administrateur requises.');
        }
    }
}
