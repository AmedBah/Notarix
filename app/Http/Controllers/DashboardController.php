<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Document;
use App\Models\Dossier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }    /**
     * Afficher le dashboard principal de l'architecture mono-entreprise
     */
    public function index()
    {
        $user = Auth::user();
        
        // Vérifier si l'utilisateur est admin pour afficher les statistiques complètes
        if ($user->role === 'admin') {
            return $this->adminDashboard();
        }
        
        return $this->userDashboard();
    }

    /**
     * Dashboard pour l'administrateur principal
     */
    private function adminDashboard()
    {        // Statistiques générales
        $stats = [
            'total_users' => User::where('status', 'active')->count(),
            'total_documents' => Document::count(),
            'total_dossiers' => Dossier::count(),
            'documents_this_month' => Document::whereMonth('created_at', now()->month)->count(),
        ];        // Documents récents
        $recentDocuments = Document::with(['createdBy'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();        // Activité récente des utilisateurs (sans derniere_connexion pour l'instant)
        $recentActivity = User::select('id', 'nom', 'prenom', 'created_at')
            ->where('status', 'active')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();// Utilisateurs par rôle
        $usersByRole = User::select('role', DB::raw('count(*) as count'))
            ->where('status', 'active')
            ->groupBy('role')
            ->get()
            ->pluck('count', 'role')
            ->toArray();        return view('pages.dashboard', compact(
            'stats',
            'recentDocuments',
            'recentActivity',
            'usersByRole'
        ));
    }

    /**
     * Dashboard pour les utilisateurs standards
     */
    private function userDashboard()
    {
        $user = Auth::user();        // Documents accessibles à l'utilisateur
        $myDocuments = Document::where('created_by', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Dossiers accessibles
        $myDossiers = Dossier::orderBy('created_at', 'desc')
            ->limit(10)
            ->get();        return view('pages.dashboard', compact(
            'myDocuments',
            'myDossiers'
        ));
    }    /**
     * Obtenir les statistiques pour l'API
     */
    public function statistics()
    {
        if (Auth::user()->role !== 'admin') {
            return response()->json(['error' => 'Accès non autorisé'], 403);
        }

        $stats = [
            'users' => [
                'total' => User::where('status', 'active')->count(),
                'active_today' => 0, // Temporairement désactivé jusqu'à migration de derniere_connexion
                'by_role' => User::select('role', DB::raw('count(*) as count'))
                    ->where('status', 'active')
                    ->groupBy('role')
                    ->get()
                    ->pluck('count', 'role'),
            ],
            'documents' => [
                'total' => Document::count(),
                'this_month' => Document::whereMonth('created_at', now()->month)->count(),
                'archived' => Document::where('statut', 'archive_auto')->orWhere('statut', 'archive_manuel')->count(),
            ],
            'dossiers' => [
                'total' => Dossier::count(),
                'active' => Dossier::where('statut', 'ouvert')->count(),
                'closed' => Dossier::where('statut', 'ferme')->count(),
            ]
        ];

        return response()->json($stats);
    }    /**
     * Obtenir l'activité récente
     */
    public function recentActivity()
    {
        if (Auth::user()->role !== 'admin') {
            return response()->json(['error' => 'Accès non autorisé'], 403);
        }

        $activities = collect();

        // Documents récents
        $recentDocs = Document::with('createdBy')
            ->select('id', 'nom', 'created_by', 'created_at')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($doc) {
                return [
                    'type' => 'document',
                    'action' => 'Ajout de document',
                    'details' => $doc->nom,
                    'user' => $doc->createdBy->nom ?? 'Utilisateur supprimé',
                    'date' => $doc->created_at,
                ];
            });        // Connexions récentes (temporairement désactivé)
        $recentLogins = collect(); // Vide pour l'instant

        $activities = $activities->concat($recentDocs)
            ->concat($recentLogins)
            ->sortByDesc('date')
            ->take(10)
            ->values();

        return response()->json($activities);
    }
}
