<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UpdateUsersWithRolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Mettre à jour les utilisateurs existants avec les nouveaux rôles
        
        // Admin principal
        User::where('email', 'admin@etude-dupont.fr')->update([
            'role' => User::ROLE_ADMIN,
            'permissions' => [
                User::PERMISSION_READ,
                User::PERMISSION_WRITE,
                User::PERMISSION_DELETE,
                User::PERMISSION_DOWNLOAD
            ]
        ]);

        // Notaire - permissions étendues
        User::where('email', 'martin@etude-dupont.fr')->update([
            'role' => User::ROLE_UTILISATEUR,
            'permissions' => [
                User::PERMISSION_READ,
                User::PERMISSION_WRITE,
                User::PERMISSION_DOWNLOAD
            ]
        ]);

        // Clerc - permissions limitées
        User::where('email', 'clerc@etude-dupont.fr')->update([
            'role' => User::ROLE_UTILISATEUR,
            'permissions' => [
                User::PERMISSION_READ,
                User::PERMISSION_DOWNLOAD
            ]
        ]);

        // Créer un utilisateur visiteur pour démonstration
        if (!User::where('email', 'visiteur@etude-dupont.fr')->exists()) {
            $entreprise = \App\Models\Entreprise::first();
            
            User::create([
                'nom' => 'Visiteur Test',
                'telephone' => '01.42.78.90.16',
                'email' => 'visiteur@etude-dupont.fr',
                'password' => Hash::make('visiteur123'),
                'role' => User::ROLE_VISITEUR,
                'permissions' => [User::PERMISSION_READ], // Lecture seule
                'est_admin' => false,
                'logo_path' => 'default_avatar.png',
                'entreprise_id' => $entreprise->id
            ]);
        }

        $this->command->info('✅ Rôles et permissions mis à jour avec succès !');
        $this->command->info('👥 Comptes utilisateurs avec rôles :');
        $this->command->info('   🔑 Admin: admin@etude-dupont.fr / admin123');
        $this->command->info('   📝 Notaire: martin@etude-dupont.fr / notaire123');
        $this->command->info('   👨‍💼 Clerc: clerc@etude-dupont.fr / clerc123');
        $this->command->info('   👁️ Visiteur: visiteur@etude-dupont.fr / visiteur123');
    }
}
