<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        echo "🚀 Lancement du seeding pour l'architecture mono-entreprise Notarix GED\n\n";
          // Seeder pour l'administrateur principal
        $this->call(AdminSeeder::class);
          // Seeder pour les champs d'activité
        $this->call(ChampsActiviteSeeder::class);
        
        // Seeder pour l'annuaire de contacts (réactivé avec corrections)
        $this->call(PersonnesSeeder::class);
        
        echo "\n✅ Seeding terminé avec succès !\n";
        echo "🎯 Votre application Notarix GED est prête pour l'utilisation.\n\n";
        echo "📋 Informations de connexion administrateur :\n";
        echo "   URL : http://localhost:8000\n";
        echo "   Email : admin@notarix.com\n";
        echo "   Mot de passe : admin123\n\n";
    }
}
