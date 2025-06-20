<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Entreprise;
use App\Models\Section;
use App\Models\Dossier;
use App\Models\Mission;
use App\Models\Document;
use App\Models\Person;
use Illuminate\Support\Facades\Hash;

class NotarialTestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {        // 1. Créer une entreprise notariale
        $entreprise = Entreprise::create([
            'nom' => 'Étude Notariale DUPONT & Associés',
            'telephone' => '01.42.78.90.12',
            'email' => 'contact@etude-dupont.fr',
            'pays' => 'France',
            'industrie' => 'Services juridiques - Notariat',
            'password' => Hash::make('entreprise123'),
            'path_logo' => 'logoEntrepise.png'
        ]);        // 2. Créer des utilisateurs avec différents rôles
        $admin = User::create([
            'nom' => 'Maître DUPONT',
            'telephone' => '01.42.78.90.13',
            'email' => 'admin@etude-dupont.fr',
            'password' => Hash::make('admin123'),
            'est_admin' => true,
            'logo_path' => 'default_avatar.png',
            'entreprise_id' => $entreprise->id
        ]);

        $notaire = User::create([
            'nom' => 'Maître MARTIN',
            'telephone' => '01.42.78.90.14',
            'email' => 'martin@etude-dupont.fr',
            'password' => Hash::make('notaire123'),
            'est_admin' => false,
            'logo_path' => 'default_avatar.png',
            'entreprise_id' => $entreprise->id
        ]);

        $clerc = User::create([
            'nom' => 'Jean BERNARD',
            'telephone' => '01.42.78.90.15',
            'email' => 'clerc@etude-dupont.fr',
            'password' => Hash::make('clerc123'),
            'est_admin' => false,
            'logo_path' => 'default_avatar.png',
            'entreprise_id' => $entreprise->id
        ]);

        // 3. Créer les sections selon le cahier des charges
        $sections = [
            'Vente particulier',
            'Vente société', 
            'Droit de personnes et de Famille',
            'Héritage et succession',
            'Droit des affaires',
            'Donations',
            'Testaments',
            'Contrats de mariage'
        ];

        $sectionObjects = [];
        foreach ($sections as $sectionName) {
            $sectionObjects[] = Section::create([
                'nom' => $sectionName,
                'user_id' => $admin->id
            ]);
        }

        // 4. Créer des personnes (clients/contacts)
        $personnes = [
            [
                'nom' => 'DURAND',
                'prenom' => 'Pierre',
                'fonction' => 'Client',
                'motif' => 'Achat immobilier',
                'contact' => '06.12.34.56.78',
                'email' => 'pierre.durand@email.fr'
            ],
            [
                'nom' => 'MOREAU',
                'prenom' => 'Marie',
                'fonction' => 'Cliente',
                'motif' => 'Succession',
                'contact' => '06.98.76.54.32',
                'email' => 'marie.moreau@email.fr'
            ],
            [
                'nom' => 'ROBERT',
                'prenom' => 'Jean',
                'fonction' => 'Client',
                'motif' => 'Donation',
                'contact' => '06.11.22.33.44',
                'email' => 'jean.robert@email.fr'
            ],
            [
                'nom' => 'LEFEBVRE',
                'prenom' => 'Sophie',
                'fonction' => 'Représentante légale',
                'motif' => 'Vente société',
                'contact' => '06.55.66.77.88',
                'email' => 'sophie.lefebvre@entreprise.com'
            ]
        ];

        foreach ($personnes as $personne) {
            Person::create($personne);
        }

        // 5. Créer des dossiers pour chaque section
        $dossierTypes = [
            'Vente particulier' => [
                'Dossier 2025-VP-001 - Vente Maison DURAND',
                'Dossier 2025-VP-002 - Vente Appartement MARTIN'
            ],
            'Héritage et succession' => [
                'Dossier 2025-HS-001 - Succession MOREAU',
                'Dossier 2025-HS-002 - Partage succession BERNARD'
            ],
            'Droit des affaires' => [
                'Dossier 2025-DA-001 - Cession parts sociales LEFEBVRE',
                'Dossier 2025-DA-002 - Constitution SARL TECH'
            ]
        ];

        foreach ($dossierTypes as $sectionName => $dossiers) {
            $section = collect($sectionObjects)->firstWhere('nom', $sectionName);
            if ($section) {
                foreach ($dossiers as $dossierName) {
                    $dossier = Dossier::create([
                        'nom' => $dossierName,
                        'chemin' => '/archives/' . strtolower(str_replace(' ', '_', $dossierName)),
                        'taille' => rand(1000, 5000), // Taille en Ko
                        'section_id' => $section->id
                    ]);

                    // 6. Créer des missions pour chaque dossier
                    Mission::create([
                        'nom' => 'Mission - ' . $dossierName,
                        'description' => 'Mission notariale pour ' . $dossierName,
                        'etat' => 'en_cours',
                        'date_debut' => now()->subDays(rand(1, 30)),
                        'date_fin_prevue' => now()->addDays(rand(30, 90)),
                        'user_id' => $notaire->id,
                        'dossier_id' => $dossier->id
                    ]);

                    // 7. Créer des documents pour chaque dossier
                    $documentTypes = [
                        'Compromis de vente',
                        'Acte authentique',
                        'Pièces d\'identité',
                        'Justificatifs domicile',
                        'Diagnostics techniques'
                    ];

                    foreach ($documentTypes as $index => $docType) {
                        if ($index < 3) { // Limiter à 3 documents par dossier
                            Document::create([
                                'nom' => $docType . ' - ' . $dossierName,
                                'chemin' => '/documents/' . strtolower(str_replace(' ', '_', $docType)) . '_' . $dossier->id . '.pdf',
                                'taille' => rand(100, 2000), // Taille en Ko
                                'type' => 'pdf',
                                'date_creation' => now()->subDays(rand(1, 15)),
                                'user_id' => $clerc->id,
                                'dossier_id' => $dossier->id
                            ]);
                        }
                    }
                }
            }
        }

        $this->command->info('✅ Données de test notariales créées avec succès !');
        $this->command->info('👤 Comptes utilisateurs créés :');
        $this->command->info('   Admin: admin@etude-dupont.fr / admin123');
        $this->command->info('   Notaire: martin@etude-dupont.fr / notaire123');
        $this->command->info('   Clerc: clerc@etude-dupont.fr / clerc123');
    }
}
