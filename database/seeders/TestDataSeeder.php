<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Section;
use App\Models\Entreprise;
use Illuminate\Support\Facades\Hash;

class TestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Créer des entreprises de test
        $entreprise1 = Entreprise::create([
            'nom' => 'Notaire & Associés',
            'telephone' => '01 23 45 67 89',
            'email' => 'contact@notaire-associes.fr',
            'pays' => 'France',
            'industrie' => 'Services Juridiques',
            'password' => Hash::make('entreprise123'),
            'path_logo' => 'logoEntrepise.png'
        ]);

        $entreprise2 = Entreprise::create([
            'nom' => 'Cabinet Juridique Moderne',
            'telephone' => '01 98 76 54 32',
            'email' => 'info@cabinet-juridique.fr',
            'pays' => 'France',
            'industrie' => 'Conseil Juridique',
            'password' => Hash::make('entreprise456'),
            'path_logo' => 'logoEntrepise.png'
        ]);

        // Créer des utilisateurs de test
        $user1 = User::create([
            'nom' => 'Jean Dupont',
            'telephone' => '06 12 34 56 78',
            'email' => 'jean.dupont@notaire.fr',
            'password' => Hash::make('password'),
            'est_admin' => true,
            'entreprise_id' => $entreprise1->id,
            'logo_path' => 'default/user.png'
        ]);

        $user2 = User::create([
            'nom' => 'Marie Martin',
            'telephone' => '06 87 65 43 21',
            'email' => 'marie.martin@notaire.fr',
            'password' => Hash::make('password'),
            'est_admin' => false,
            'entreprise_id' => $entreprise1->id,
            'logo_path' => 'default/user.png'
        ]);

        $user3 = User::create([
            'nom' => 'Pierre Durand',
            'telephone' => '06 11 22 33 44',
            'email' => 'pierre.durand@cabinet.fr',
            'password' => Hash::make('password'),
            'est_admin' => false,
            'entreprise_id' => $entreprise2->id,
            'logo_path' => 'default/user.png'
        ]);

        // Créer des sections de test
        $sections = [
            'Actes de Vente',
            'Successions',
            'Donations',
            'Contrats de Mariage',
            'Testaments',
            'Procurations',
            'Constitutions de Société',
            'Archives 2023',
            'Archives 2024',
            'Dossiers Urgents'
        ];

        foreach ($sections as $index => $sectionNom) {
            // Alternance entre les utilisateurs
            $userId = ($index % 3 == 0) ? $user1->id : (($index % 3 == 1) ? $user2->id : $user3->id);
            
            Section::create([
                'nom' => $sectionNom,
                'user_id' => $userId
            ]);
        }

        echo "✅ Données de test créées avec succès !\n";
        echo "📊 Entreprises créées : " . Entreprise::count() . "\n";
        echo "👥 Utilisateurs créés : " . User::count() . "\n";
        echo "📁 Sections créées : " . Section::count() . "\n\n";
        echo "🔑 Identifiants de connexion :\n";
        echo "Email: jean.dupont@notaire.fr | Mot de passe: password (Admin)\n";
        echo "Email: marie.martin@notaire.fr | Mot de passe: password\n";
        echo "Email: pierre.durand@cabinet.fr | Mot de passe: password\n";
    }
}
