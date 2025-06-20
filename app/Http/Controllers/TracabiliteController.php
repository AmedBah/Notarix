<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Document;
use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TracabiliteController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Afficher le tableau de bord de traçabilité
     */
    public function index()
    {
        // Statistiques générales
        $stats = [
            'total_consultations' => ActivityLog::where('action', 'view')->count(),
            'total_modifications' => ActivityLog::where('action', 'modify')->count(),
            'total_telechargements' => ActivityLog::where('action', 'download')->count(),
            'utilisateurs_actifs' => ActivityLog::distinct('user_id')->count('user_id'),
        ];

        // Activités récentes
        $activites_recentes = ActivityLog::with(['user', 'document'])
                                       ->orderBy('created_at', 'desc')
                                       ->take(20)
                                       ->get();

        // Graphiques d'activité par jour (7 derniers jours)
        $activites_par_jour = ActivityLog::select(
                                    DB::raw('DATE(created_at) as date'),
                                    DB::raw('COUNT(*) as count'),
                                    'action'
                                )
                                ->where('created_at', '>=', now()->subDays(7))
                                ->groupBy('date', 'action')
                                ->orderBy('date')
                                ->get();

        return view('pages.tracabilite.index', compact('stats', 'activites_recentes', 'activites_par_jour'));
    }

    /**
     * Journal d'activité détaillé
     */
    public function journal(Request $request)
    {
        $query = ActivityLog::with(['user', 'document']);

        // Filtres
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        if ($request->filled('document_id')) {
            $query->where('document_id', $request->document_id);
        }

        if ($request->filled('date_debut')) {
            $query->whereDate('created_at', '>=', $request->date_debut);
        }

        if ($request->filled('date_fin')) {
            $query->whereDate('created_at', '<=', $request->date_fin);
        }

        if ($request->filled('ip_address')) {
            $query->where('ip_address', 'LIKE', '%' . $request->ip_address . '%');
        }

        $logs = $query->orderBy('created_at', 'desc')->paginate(50);

        // Données pour les filtres
        $utilisateurs = User::all();
        $actions = ActivityLog::distinct('action')->pluck('action');

        return view('pages.tracabilite.journal', compact('logs', 'utilisateurs', 'actions'));
    }

    /**
     * Suivi des consultations d'un document spécifique
     */
    public function documentHistory($document_id)
    {
        $document = Document::findOrFail($document_id);
        
        $historique = ActivityLog::where('document_id', $document_id)
                                ->with('user')
                                ->orderBy('created_at', 'desc')
                                ->paginate(30);

        // Statistiques du document
        $stats_document = [
            'nb_consultations' => ActivityLog::where('document_id', $document_id)
                                            ->where('action', 'view')
                                            ->count(),
            'nb_telechargements' => ActivityLog::where('document_id', $document_id)
                                              ->where('action', 'download')
                                              ->count(),
            'nb_modifications' => ActivityLog::where('document_id', $document_id)
                                            ->where('action', 'modify')
                                            ->count(),
            'premiere_consultation' => ActivityLog::where('document_id', $document_id)
                                                 ->where('action', 'view')
                                                 ->oldest()
                                                 ->first(),
            'derniere_consultation' => ActivityLog::where('document_id', $document_id)
                                                 ->where('action', 'view')
                                                 ->latest()
                                                 ->first(),
        ];

        return view('pages.tracabilite.document', compact('document', 'historique', 'stats_document'));
    }

    /**
     * Suivi des activités d'un utilisateur spécifique
     */
    public function userActivity($user_id)
    {
        $utilisateur = User::findOrFail($user_id);
        
        $activites = ActivityLog::where('user_id', $user_id)
                               ->with('document')
                               ->orderBy('created_at', 'desc')
                               ->paginate(30);

        // Statistiques de l'utilisateur
        $stats_user = [
            'nb_consultations' => ActivityLog::where('user_id', $user_id)
                                            ->where('action', 'view')
                                            ->count(),
            'nb_telechargements' => ActivityLog::where('user_id', $user_id)
                                              ->where('action', 'download')
                                              ->count(),
            'nb_modifications' => ActivityLog::where('user_id', $user_id)
                                            ->where('action', 'modify')
                                            ->count(),
            'documents_consultes' => ActivityLog::where('user_id', $user_id)
                                               ->where('action', 'view')
                                               ->distinct('document_id')
                                               ->count('document_id'),
            'derniere_activite' => ActivityLog::where('user_id', $user_id)
                                             ->latest()
                                             ->first(),
        ];

        return view('pages.tracabilite.user', compact('utilisateur', 'activites', 'stats_user'));
    }

    /**
     * Enregistrer une activité
     */
    public function logActivity($document_id, $action, $details = null)
    {
        ActivityLog::create([
            'user_id' => Auth::id(),
            'document_id' => $document_id,
            'action' => $action,
            'details' => $details ? json_encode($details) : null,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'restaurable' => in_array($action, ['modify', 'delete']) // Actions restaurables
        ]);
    }

    /**
     * Restaurer un document (admin uniquement)
     */
    public function restore($log_id)
    {
        // Vérifier les permissions admin
        if (!Auth::user()->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Accès non autorisé.'
            ], 403);
        }

        try {
            $log = ActivityLog::findOrFail($log_id);
            
            if (!$log->restaurable) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cette action n\'est pas restaurable.'
                ]);
            }

            // Logique de restauration selon le type d'action
            switch ($log->action) {
                case 'delete':
                    // Restaurer un document supprimé
                    $this->restoreDeletedDocument($log);
                    break;
                    
                case 'modify':
                    // Restaurer une version antérieure
                    $this->restorePreviousVersion($log);
                    break;
                    
                default:
                    return response()->json([
                        'success' => false,
                        'message' => 'Type de restauration non supporté.'
                    ]);
            }

            // Enregistrer l'action de restauration
            $this->logActivity($log->document_id, 'restore', [
                'restored_log_id' => $log_id,
                'restored_action' => $log->action
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Restauration effectuée avec succès.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la restauration : ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Exporter le journal d'activité
     */
    public function exportJournal(Request $request)
    {
        $format = $request->get('format', 'csv');
        
        $query = ActivityLog::with(['user', 'document']);

        // Appliquer les mêmes filtres que pour l'affichage
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        if ($request->filled('date_debut')) {
            $query->whereDate('created_at', '>=', $request->date_debut);
        }

        if ($request->filled('date_fin')) {
            $query->whereDate('created_at', '<=', $request->date_fin);
        }

        $logs = $query->orderBy('created_at', 'desc')->get();

        // Générer l'export selon le format
        switch ($format) {
            case 'csv':
                return $this->exportToCsv($logs);
            case 'pdf':
                return $this->exportToPdf($logs);
            case 'excel':
                return $this->exportToExcel($logs);
            default:
                return redirect()->back()->with('error', 'Format non supporté.');
        }
    }

    /**
     * Statistiques avancées de traçabilité
     */
    public function statistiques()
    {
        // Documents les plus consultés
        $documents_populaires = DB::table('activity_logs')
            ->select('document_id', DB::raw('COUNT(*) as consultations'))
            ->where('action', 'view')
            ->groupBy('document_id')
            ->orderBy('consultations', 'desc')
            ->limit(10)
            ->get();

        // Utilisateurs les plus actifs
        $utilisateurs_actifs = DB::table('activity_logs')
            ->select('user_id', DB::raw('COUNT(*) as activites'))
            ->groupBy('user_id')
            ->orderBy('activites', 'desc')
            ->limit(10)
            ->get();

        // Répartition des actions
        $repartition_actions = DB::table('activity_logs')
            ->select('action', DB::raw('COUNT(*) as count'))
            ->groupBy('action')
            ->get();

        // Activité par heure de la journée
        $activite_par_heure = DB::table('activity_logs')
            ->select(DB::raw('HOUR(created_at) as heure'), DB::raw('COUNT(*) as count'))
            ->groupBy('heure')
            ->orderBy('heure')
            ->get();

        return view('pages.tracabilite.statistiques', compact(
            'documents_populaires',
            'utilisateurs_actifs',
            'repartition_actions',
            'activite_par_heure'
        ));
    }

    /**
     * Méthodes privées pour la restauration
     */
    private function restoreDeletedDocument($log)
    {
        // Implémentation de la restauration d'un document supprimé
        // À adapter selon la logique de soft delete utilisée
    }

    private function restorePreviousVersion($log)
    {
        // Implémentation de la restauration d'une version antérieure
        // À adapter selon le système de versioning
    }

    /**
     * Méthodes d'export
     */
    private function exportToCsv($logs)
    {
        // Implémentation de l'export CSV
    }

    private function exportToPdf($logs)
    {
        // Implémentation de l'export PDF
    }

    private function exportToExcel($logs)
    {
        // Implémentation de l'export Excel
    }
}
