<?php
// Script pour créer des données de test
require_once 'vendor/autoload.php';
require_once 'bootstrap/app.php';

use App\Models\User;
use App\Models\Section;
use App\Models\Entreprise;
use Illuminate\Support\Facades\Hash;

try {
    echo "🔄 Création des données de test...\n\n";

    // Créer quelques sections de test supplémentaires
    $testSections = [
        'Documents Administratifs',
        'Contrats Commerciaux', 
        'Litiges',
        'Propriété Intellectuelle',
        'Droit du Travail'
    ];

    // Récupérer un utilisateur existant ou créer un utilisateur de test
    $user = User::first();
    if (!$user) {
        echo "⚠️  Aucun utilisateur trouvé. Création d'un utilisateur de test...\n";
        $user = User::create([
            'nom' => 'Utilisateur Test',
            'telephone' => '06 00 00 00 00',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
            'est_admin' => true,
            'logo_path' => 'default/user.png'
        ]);
        echo "✅ Utilisateur de test créé : {$user->email}\n";
    }

    // Créer les sections de test
    foreach ($testSections as $sectionNom) {
        $existingSection = Section::where('nom', $sectionNom)->where('user_id', $user->id)->first();
        if (!$existingSection) {
            Section::create([
                'nom' => $sectionNom,
                'user_id' => $user->id
            ]);
            echo "✅ Section créée : {$sectionNom}\n";
        } else {
            echo "ℹ️  Section déjà existante : {$sectionNom}\n";
        }
    }

    echo "\n📊 Statistiques finales :\n";
    echo "👥 Total utilisateurs : " . User::count() . "\n";
    echo "📁 Total sections : " . Section::count() . "\n";
    echo "🏢 Total entreprises : " . Entreprise::count() . "\n";

    echo "\n🎉 Données de test créées avec succès !\n";
    echo "🔗 Vous pouvez maintenant tester votre application.\n";

} catch (Exception $e) {
    echo "❌ Erreur : " . $e->getMessage() . "\n";
}
