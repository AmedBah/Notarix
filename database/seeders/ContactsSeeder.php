<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ContactsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $contacts = [
            [
                'nom' => 'Tribunal de Grande Instance de Paris',
                'categorie' => 'Justice',
                'telephone' => '01 44 32 51 51',
                'email' => 'contact@tgi-paris.justice.fr',
                'adresse' => '4 Boulevard du Palais, 75001 Paris',
                'notes' => 'Tribunal principal - Affaires civiles et pénales',
                'favori' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nom' => 'Chambre des Notaires de Paris',
                'categorie' => 'Professionnel',
                'telephone' => '01 44 82 24 00',
                'email' => 'info@chambre-notaires-paris.fr',
                'adresse' => '12 Avenue Victoria, 75001 Paris',
                'notes' => 'Organisation professionnelle des notaires de Paris',
                'favori' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nom' => 'Service de Publicité Foncière',
                'categorie' => 'Administration',
                'telephone' => '01 40 04 04 04',
                'email' => 'spf@dgfip.finances.gouv.fr',
                'adresse' => '2 Rue de la Paix, 75002 Paris',
                'notes' => 'Formalités hypothécaires et publicité foncière',
                'favori' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nom' => 'Tribunal de Commerce de Paris',
                'categorie' => 'Justice',
                'telephone' => '01 55 04 10 10',
                'email' => 'greffe@tc-paris.fr',
                'adresse' => '1 Quai de Corse, 75004 Paris',
                'notes' => 'Affaires commerciales - Registre du commerce et des sociétés',
                'favori' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nom' => 'SAFER Île-de-France',
                'categorie' => 'Immobilier',
                'telephone' => '01 40 64 17 85',
                'email' => 'contact@safer-idf.com',
                'adresse' => '8 Avenue de l\'Opéra, 75001 Paris',
                'notes' => 'Société d\'aménagement foncier et d\'établissement rural',
                'favori' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nom' => 'Cabinet d\'Expert Dupont & Associés',
                'categorie' => 'Expert',
                'telephone' => '01 42 96 12 34',
                'email' => 'contact@expert-dupont.fr',
                'adresse' => '15 Rue de Rivoli, 75001 Paris',
                'notes' => 'Expert en évaluation immobilière - Agréé près les tribunaux',
                'favori' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('contacts')->insert($contacts);
        
        echo "✅ " . count($contacts) . " contacts créés dans l'annuaire\n";
    }
}
