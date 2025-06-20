<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ChampsActiviteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {        $champsActivite = [
            [
                'nom' => 'Droit Civil',
                'description' => 'Contrats civils, successions, régimes matrimoniaux, donations',
                'actif' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nom' => 'Droit Commercial',
                'description' => 'Sociétés commerciales, fonds de commerce, baux commerciaux',
                'actif' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nom' => 'Droit Immobilier',
                'description' => 'Ventes immobilières, acquisitions, hypothèques, servitudes',
                'actif' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nom' => 'Droit des Affaires',
                'description' => 'Création d\'entreprises, fusions-acquisitions, restructurations',
                'actif' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nom' => 'Authentifications',
                'description' => 'Actes authentiques, certifications, légalisations',
                'actif' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nom' => 'Conseils Juridiques',
                'description' => 'Consultations juridiques, avis de droit, accompagnement',
                'actif' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('champs_activites')->insert($champsActivite);
        
        echo "✅ " . count($champsActivite) . " champs d'activité créés\n";
    }
}
