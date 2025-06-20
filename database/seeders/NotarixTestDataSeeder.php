<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Entreprise;
use App\Models\User;
use App\Models\Section;
use App\Models\Dossier;
use App\Models\Document;
use App\Models\Person;
use Illuminate\Support\Facades\Hash;

class NotarixTestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 1. Créer l'entreprise (Étude notariale)
        $entreprise = Entreprise::create([
            'nom' => 'Étude Notariale Maître Dupont',
            'telephone' => '+33 1 23 45 67 89',
            'email' => 'contact@etude-dupont.fr',
            'pays' => 'France',
            'industrie' => 'Services juridiques - Notariat',
            'password' => Hash::make('entreprise123'),
            'path_logo' => null
        ]);

        // 2. Créer l'administrateur principal
        $admin = User::create([
            'nom' => 'Maître Jean Dupont',
            'telephone' => '+33 1 23 45 67 89',
            'email' => 'admin@etude-dupont.fr',
            'logo_path' => null,
            'est_admin' => true,
            'password' => Hash::make('admin123'),
            'entreprise_id' => $entreprise->id
        ]);

        // 3. Créer des utilisateurs (clercs, assistants)
        $clerc1 = User::create([
            'nom' => 'Marie Martin - Clerc Principal',
            'telephone' => '+33 1 23 45 67 90',
            'email' => 'marie.martin@etude-dupont.fr',
            'logo_path' => null,
            'est_admin' => false,
            'password' => Hash::make('clerc123'),
            'entreprise_id' => $entreprise->id
        ]);

        $clerc2 = User::create([
            'nom' => 'Pierre Durand - Assistant',
            'telephone' => '+33 1 23 45 67 91',
            'email' => 'pierre.durand@etude-dupont.fr',
            'logo_path' => null,
            'est_admin' => false,
            'password' => Hash::make('assistant123'),
            'entreprise_id' => $entreprise->id
        ]);

        // 4. Créer les sections (champs d'activités selon votre cahier des charges)
        $sections = [
            'Vente Particulier',
            'Vente Société',
            'Droit des Personnes et de la Famille',
            'Héritage et Succession',
            'Droit des Affaires',
            'Donations',
            'Testaments',
            'Contrats de Mariage'
        ];

        $sectionModels = [];
        foreach ($sections as $sectionName) {
            $sectionModels[] = Section::create([
                'nom' => $sectionName,
                'user_id' => $admin->id
            ]);
        }

        // 5. Créer des personnes (clients, contacts)
        $personnes = [
            [
                'nom' => 'Dubois',
                'prenom' => 'François',
                'fonction' => 'Acheteur',
                'motif' => 'Achat immobilier',
                'contact' => '+33 6 12 34 56 78',
                'email' => 'francois.dubois@email.com'
            ],
            [
                'nom' => 'Bernard',
                'prenom' => 'Sophie',
                'fonction' => 'Vendeur',
                'motif' => 'Vente maison familiale',
                'contact' => '+33 6 98 76 54 32',
                'email' => 'sophie.bernard@email.com'
            ],
            [
                'nom' => 'Lefebvre',
                'prenom' => 'Michel',
                'fonction' => 'Héritier',
                'motif' => 'Succession',
                'contact' => '+33 6 11 22 33 44',
                'email' => 'michel.lefebvre@email.com'
            ],
            [
                'nom' => 'Moreau',
                'prenom' => 'Catherine',
                'fonction' => 'Donatrice',
                'motif' => 'Donation aux enfants',
                'contact' => '+33 6 55 66 77 88',
                'email' => 'catherine.moreau@email.com'
            ]
        ];

        foreach ($personnes as $personne) {
            Person::create($personne);
        }

        // 6. Créer des dossiers types
        $dossiers = [
            [
                'nom' => 'Dossier_2025_001_DUBOIS_Achat_Appartement',
                'chemin' => '/archives/2025/vente-particulier/',
                'taille' => 2.5,
                'section_id' => $sectionModels[0]->id // Vente Particulier
            ],
            [
                'nom' => 'Dossier_2025_002_BERNARD_Vente_Maison',
                'chemin' => '/archives/2025/vente-particulier/',
                'taille' => 3.8,
                'section_id' => $sectionModels[0]->id // Vente Particulier
            ],
            [
                'nom' => 'Dossier_2025_003_LEFEBVRE_Succession',
                'chemin' => '/archives/2025/succession/',
                'taille' => 5.2,
                'section_id' => $sectionModels[3]->id // Héritage et Succession
            ],
            [
                'nom' => 'Dossier_2025_004_MOREAU_Donation',
                'chemin' => '/archives/2025/donation/',
                'taille' => 1.9,
                'section_id' => $sectionModels[5]->id // Donations
            ]
        ];

        $dossierModels = [];
        foreach ($dossiers as $dossier) {
            $dossierModels[] = Dossier::create($dossier);
        }

        // 7. Créer des documents types
        $documents = [
            [
                'nom' => 'Compromis_de_vente_DUBOIS.pdf',
                'chemin' => '/documents/2025/compromis/',
                'taille' => 0.8,
                'type' => 'PDF',
                'section_id' => $sectionModels[0]->id,
                'dossier_id' => $dossierModels[0]->id,
                'user_id' => $admin->id
            ],
            [
                'nom' => 'Acte_authentique_BERNARD.pdf',
                'chemin' => '/documents/2025/actes/',
                'taille' => 1.2,
                'type' => 'PDF',
                'section_id' => $sectionModels[0]->id,
                'dossier_id' => $dossierModels[1]->id,
                'user_id' => $clerc1->id
            ],
            [
                'nom' => 'Testament_LEFEBVRE.pdf',
                'chemin' => '/documents/2025/testaments/',
                'taille' => 0.6,
                'type' => 'PDF',
                'section_id' => $sectionModels[3]->id,
                'dossier_id' => $dossierModels[2]->id,
                'user_id' => $admin->id
            ],
            [
                'nom' => 'Acte_donation_MOREAU.pdf',
                'chemin' => '/documents/2025/donations/',
                'taille' => 0.9,
                'type' => 'PDF',
                'section_id' => $sectionModels[5]->id,
                'dossier_id' => $dossierModels[3]->id,
                'user_id' => $clerc2->id
            ],
            [
                'nom' => 'Piece_identite_DUBOIS.jpg',
                'chemin' => '/documents/2025/pieces-jointes/',
                'taille' => 0.3,
                'type' => 'JPEG',
                'section_id' => $sectionModels[0]->id,
                'dossier_id' => $dossierModels[0]->id,
                'user_id' => $clerc1->id
            ]
        ];

        foreach ($documents as $document) {
            Document::create($document);
        }

        echo "\n=== DONNÉES DE TEST CRÉÉES AVEC SUCCÈS ===\n";
        echo "Entreprise : " . $entreprise->nom . "\n";
        echo "Administrateur : " . $admin->email . " (mot de passe: admin123)\n";
        echo "Clerc 1 : " . $clerc1->email . " (mot de passe: clerc123)\n";
        echo "Clerc 2 : " . $clerc2->email . " (mot de passe: assistant123)\n";
        echo "Sections créées : " . count($sections) . "\n";
        echo "Personnes créées : " . count($personnes) . "\n";
        echo "Dossiers créés : " . count($dossiers) . "\n";
        echo "Documents créés : " . count($documents) . "\n";
        echo "==========================================\n";
    }
}
