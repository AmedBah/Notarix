<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\DossierController;
use App\Http\Controllers\PersonneController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RechercheController;
use App\Http\Controllers\TracabiliteController;
use App\Http\Controllers\ChampActiviteController;
use App\Http\Controllers\ActeController;
use App\Http\Controllers\TemplateController;
use App\Http\Controllers\ContactController;

/*
|--------------------------------------------------------------------------
| Routes Web - Application Notarix GED (Mono-entreprise)
|--------------------------------------------------------------------------
|
| Routes conformes au cahier des charges pour l'architecture mono-entreprise
|
*/

// Page d'accueil - Redirige vers le dashboard si connecté, sinon vers la page d'accueil
Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }
    return view('acceuil');
})->name('home');

// Route d'accueil explicite
Route::get('/accueil', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }
    return view('acceuil');
})->name('accueil');

// Routes d'authentification (utilise les routes par défaut de Laravel)
require __DIR__.'/auth.php';

// Route publique pour le guide de navigation
Route::get('/guide-navigation', function () {
    return view('guide-navigation');
})->name('guide.navigation');

// Route publique pour la documentation d'accès
Route::get('/documentation-acces', function () {
    $content = file_get_contents(base_path('documentation-acces.md'));
    return view('documentation', ['content' => $content, 'title' => 'Documentation d\'accès']);
})->name('documentation.acces');

// Routes protégées par authentification
Route::middleware(['auth', 'verified'])->group(function () {
    
    // Dashboard principal
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // ==========================================
    // GESTION DES DOCUMENTS (CDC)
    // ==========================================
    Route::prefix('documents')->name('documents.')->group(function () {
        Route::get('/', [DocumentController::class, 'index'])->name('index');
        Route::get('/create', [DocumentController::class, 'create'])->name('create');
        Route::post('/', [DocumentController::class, 'store'])->name('store');
        Route::get('/{id}', [DocumentController::class, 'show'])->name('show');
        Route::get('/{id}/download', [DocumentController::class, 'download'])->name('download');
        Route::post('/{id}/archiver-auto', [DocumentController::class, 'archiverAuto'])->name('archiver-auto');
        Route::post('/{id}/archiver-manuel', [DocumentController::class, 'archiverManuel'])->name('archiver-manuel');
        Route::post('/{id}/restore', [DocumentController::class, 'restore'])->name('restore');
        Route::delete('/{id}', [DocumentController::class, 'destroy'])->name('destroy');
        
        // Fonctionnalité de scan (CDC)
        Route::get('/scan/interface', [DocumentController::class, 'scan'])->name('scan');
        Route::post('/scan/process', [DocumentController::class, 'processScan'])->name('scan.process');
    });
    
    // ==========================================
    // GESTION DES DOSSIERS (CDC)
    // ==========================================
    Route::prefix('dossiers')->name('dossiers.')->group(function () {
        Route::get('/', [DossierController::class, 'index'])->name('index');
        Route::get('/create', [DossierController::class, 'create'])->name('create');
        Route::post('/', [DossierController::class, 'store'])->name('store');
        Route::get('/{id}', [DossierController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [DossierController::class, 'edit'])->name('edit');
        Route::put('/{id}', [DossierController::class, 'update'])->name('update');
        Route::post('/{id}/fermer', [DossierController::class, 'fermer'])->name('fermer');
        Route::post('/{id}/archiver', [DossierController::class, 'archiver'])->name('archiver');
        Route::delete('/{id}', [DossierController::class, 'destroy'])->name('destroy');
        Route::get('/recherche/avancee', [DossierController::class, 'rechercher'])->name('rechercher');
    });
    
    // ==========================================
    // GESTION DES PERSONNES/CONTACTS (CDC)
    // ==========================================
    Route::prefix('personnes')->name('personnes.')->group(function () {
        Route::get('/', [PersonneController::class, 'index'])->name('index');
        Route::get('/create', [PersonneController::class, 'create'])->name('create');
        Route::post('/', [PersonneController::class, 'store'])->name('store');
        Route::get('/{id}', [PersonneController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [PersonneController::class, 'edit'])->name('edit');
        Route::put('/{id}', [PersonneController::class, 'update'])->name('update');
        Route::delete('/{id}', [PersonneController::class, 'destroy'])->name('destroy');
        Route::get('/recherche/annuaire', [PersonneController::class, 'rechercher'])->name('rechercher');
    });
    
    // ==========================================
    // GESTION DES UTILISATEURS SYSTÈME
    // ==========================================
    Route::prefix('users')->name('users.')->group(function () {
        // Profil utilisateur
        Route::get('/profile', [UserController::class, 'profile'])->name('profile');
        Route::get('/profile/edit', [UserController::class, 'editProfile'])->name('edit-profile');
        Route::put('/profile', [UserController::class, 'updateProfile'])->name('update-profile');
        Route::get('/password/change', [UserController::class, 'changePasswordForm'])->name('change-password-form');
        Route::post('/password/change', [UserController::class, 'changePassword'])->name('change-password');
        
        // Gestion des utilisateurs (admin seulement)
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::get('/create', [UserController::class, 'create'])->name('create');
        Route::post('/', [UserController::class, 'store'])->name('store');
        Route::get('/{id}', [UserController::class, 'show'])->name('show');
        Route::post('/{id}/toggle-status', [UserController::class, 'toggleStatus'])->name('toggle-status');
        Route::delete('/{id}', [UserController::class, 'destroy'])->name('destroy');
        Route::get('/recherche/utilisateurs', [UserController::class, 'search'])->name('search');
    });
    
    // ==========================================
    // MOTEUR DE RECHERCHE MULTICRITÈRES (CDC)
    // ==========================================
    Route::prefix('recherche')->name('recherche.')->group(function () {
        Route::get('/', [RechercheController::class, 'index'])->name('index');
        Route::post('/documents', [RechercheController::class, 'searchDocuments'])->name('documents');
        Route::post('/dossiers', [RechercheController::class, 'searchDossiers'])->name('dossiers');
        Route::post('/personnes', [RechercheController::class, 'searchPersonnes'])->name('personnes');
        Route::post('/globale', [RechercheController::class, 'searchGlobale'])->name('globale');
        Route::get('/historique', [RechercheController::class, 'historique'])->name('historique');
    });
    
    // ==========================================
    // TRAÇABILITÉ ET JOURNALISATION (CDC)
    // ==========================================
    Route::prefix('tracabilite')->name('tracabilite.')->group(function () {
        Route::get('/', [TracabiliteController::class, 'index'])->name('index');
        Route::get('/utilisateur/{user_id}', [TracabiliteController::class, 'parUtilisateur'])->name('par-utilisateur');
        Route::get('/document/{document_id}', [TracabiliteController::class, 'parDocument'])->name('par-document');
        Route::get('/dossier/{dossier_id}', [TracabiliteController::class, 'parDossier'])->name('par-dossier');
        Route::get('/export', [TracabiliteController::class, 'export'])->name('export');
    });
    
    // ==========================================
    // GESTION DES CHAMPS D'ACTIVITÉ (CDC)
    // ==========================================
    Route::prefix('champs-activite')->name('champs-activite.')->group(function () {
        Route::get('/', [ChampActiviteController::class, 'index'])->name('index');
        Route::get('/create', [ChampActiviteController::class, 'create'])->name('create');
        Route::post('/', [ChampActiviteController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [ChampActiviteController::class, 'edit'])->name('edit');
        Route::put('/{id}', [ChampActiviteController::class, 'update'])->name('update');
        Route::post('/{id}/toggle', [ChampActiviteController::class, 'toggle'])->name('toggle');
        Route::delete('/{id}', [ChampActiviteController::class, 'destroy'])->name('destroy');
    });
    
    // ==========================================
    // RÉDACTION D'ACTES ET COURRIERS (CDC)
    // ==========================================
    Route::prefix('actes')->name('actes.')->group(function () {
        Route::get('/', [ActeController::class, 'index'])->name('index');
        Route::get('/create', [ActeController::class, 'create'])->name('create');
        Route::post('/', [ActeController::class, 'store'])->name('store');
        Route::get('/{id}', [ActeController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [ActeController::class, 'edit'])->name('edit');
        Route::put('/{id}', [ActeController::class, 'update'])->name('update');
        Route::get('/{id}/preview', [ActeController::class, 'preview'])->name('preview');
        Route::get('/{id}/pdf', [ActeController::class, 'generatePdf'])->name('pdf');
        Route::delete('/{id}', [ActeController::class, 'destroy'])->name('destroy');
    });
    
    // ==========================================
    // GESTION DES TEMPLATES (CDC)
    // ==========================================
    Route::prefix('templates')->name('templates.')->group(function () {
        Route::get('/', [TemplateController::class, 'index'])->name('index');
        Route::get('/create', [TemplateController::class, 'create'])->name('create');
        Route::post('/', [TemplateController::class, 'store'])->name('store');
        Route::get('/{id}', [TemplateController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [TemplateController::class, 'edit'])->name('edit');
        Route::put('/{id}', [TemplateController::class, 'update'])->name('update');
        Route::post('/{id}/duplicate', [TemplateController::class, 'duplicate'])->name('duplicate');
        Route::delete('/{id}', [TemplateController::class, 'destroy'])->name('destroy');
    });
    
    // ==========================================
    // GESTION DES CONTACTS PROFESSIONNELS (CDC)
    // ==========================================
    Route::prefix('contacts')->name('contacts.')->group(function () {
        Route::get('/', [ContactController::class, 'index'])->name('index');
        Route::get('/create', [ContactController::class, 'create'])->name('create');
        Route::post('/', [ContactController::class, 'store'])->name('store');
        Route::get('/{id}', [ContactController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [ContactController::class, 'edit'])->name('edit');
        Route::put('/{id}', [ContactController::class, 'update'])->name('update');
        Route::post('/{id}/toggle-visibility', [ContactController::class, 'toggleVisibility'])->name('toggle-visibility');
        Route::delete('/{id}', [ContactController::class, 'destroy'])->name('destroy');
        Route::get('/export/csv', [ContactController::class, 'exportCsv'])->name('export-csv');
        Route::post('/import/csv', [ContactController::class, 'importCsv'])->name('import-csv');
    });
    
});
