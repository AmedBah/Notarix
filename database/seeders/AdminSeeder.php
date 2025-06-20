<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {        // Créer l'administrateur principal
        User::updateOrCreate(
            ['email' => 'admin@notarix.com'],
            [
                'nom' => 'Administrateur',
                'prenom' => 'Principal',
                'email' => 'admin@notarix.com',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'status' => 'active',
                'adresse' => 'Office Notarial Notarix',
                'fonction' => 'Administrateur Principal',
                'type_personne' => 'autre',
                'email_verified_at' => now(),
            ]
        );

        echo "✅ Administrateur principal créé :\n";
        echo "   Email: admin@notarix.com\n";
        echo "   Mot de passe: admin123\n";
        echo "   Rôle: Administrateur Principal\n\n";        // Créer quelques utilisateurs de test
        User::updateOrCreate(
            ['email' => 'utilisateur@notarix.com'],
            [
                'nom' => 'Utilisateur',
                'prenom' => 'Test',
                'email' => 'utilisateur@notarix.com',
                'password' => Hash::make('password'),
                'role' => 'utilisateur',
                'status' => 'active',
                'adresse' => 'Adresse utilisateur test',
                'fonction' => 'Utilisateur',
                'type_personne' => 'autre',
                'email_verified_at' => now(),
            ]
        );

        echo "✅ Utilisateur de test créé :\n";
        echo "   Email: utilisateur@notarix.com\n";
        echo "   Mot de passe: password\n";
        echo "   Rôle: Utilisateur\n\n";
    }
}
