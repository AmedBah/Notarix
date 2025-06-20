<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class PersonnesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */    public function run()
    {
        echo "📝 Création de l'annuaire des personnes...\n";
        
        $personnes = [
            [
                'nom' => 'Martin',
                'prenom' => 'Jean',
                'email' => 'jean.martin@clients.com',
                'password' => Hash::make('password123'),
                'adresse' => '123 Rue de la République, 75001 Paris',
                'fonction' => 'Acheteur',
                'motif' => 'Achat immobilier',
                'type_personne' => 'client',
                'visibilite' => 'public',
                'status' => 'active',
                'role' => 'utilisateur', // Conforme au CDC mono-entreprise
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nom' => 'Dupont',
                'prenom' => 'Marie',
                'email' => 'marie.dupont@clients.com',
                'password' => Hash::make('password123'),
                'adresse' => '456 Avenue des Champs, 75008 Paris',
                'fonction' => 'Vendeur',
                'motif' => 'Vente immobilière',
                'type_personne' => 'client',
                'visibilite' => 'public',
                'status' => 'active',
                'role' => 'utilisateur', // Conforme au CDC mono-entreprise
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nom' => 'Bernard',
                'prenom' => 'Pierre',
                'email' => 'pierre.bernard@notaires.fr',
                'password' => Hash::make('password123'),
                'adresse' => '12 Place du Notariat, 75004 Paris',
                'fonction' => 'Notaire associé',
                'motif' => 'Collaboration professionnelle',
                'type_personne' => 'autre',
                'visibilite' => 'public',
                'status' => 'active',
                'role' => 'utilisateur', // Conforme au CDC mono-entreprise
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nom' => 'Lefebvre',
                'prenom' => 'Sophie',
                'email' => 'sophie.lefebvre@avocats.fr',
                'password' => Hash::make('password123'),
                'adresse' => '34 Rue du Barreau, 75002 Paris',
                'fonction' => 'Avocat',
                'motif' => 'Conseil juridique',
                'type_personne' => 'expert',
                'visibilite' => 'public',
                'status' => 'active',
                'role' => 'utilisateur', // Conforme au CDC mono-entreprise
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nom' => 'Durand',
                'prenom' => 'Michel',
                'email' => 'michel.durand@experts.fr',
                'password' => Hash::make('password123'),
                'adresse' => '78 Boulevard Saint-Germain, 75005 Paris',
                'fonction' => 'Expert immobilier',
                'motif' => 'Expertise technique',
                'type_personne' => 'expert',
                'visibilite' => 'public',
                'status' => 'active',
                'role' => 'utilisateur', // Conforme au CDC mono-entreprise
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($personnes as $personne) {
            // Vérifier si l'email existe déjà avant de créer
            if (!User::where('email', $personne['email'])->exists()) {
                User::create($personne);
            }
        }
        
        echo "✅ " . count($personnes) . " personnes créées dans l'annuaire\n";
    }
}
