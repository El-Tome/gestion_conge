<?php

namespace App\DataFixtures;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Factory\CongeFactory;
use App\Factory\UserFactory;
use Faker\Factory;
use App\Entity\Services;


class AppFixtures extends Fixture
{
    /**
     * Cette méthode charge des données fictives pour la gestion des congés.
     */
    public function load(ObjectManager $manager): void
    {
        // Créer d'abord des services
        $servicesEntities = [];
        for ($i = 1; $i <= 2; $i++) {
            $service = new Services();
            $service->setNom('Service ' . $i);
            $manager->persist($service);
            $servicesEntities[] = $service;
        }
        $manager->flush();

        // Crée des utilisateurs fictifs
        $users = UserFactory::new()->createMany(10, function () use ($servicesEntities) {
            $faker = Factory::create();
            return [
                'nom' => $faker->lastName('fr_FR'),
                'prenom' => $faker->firstName('fr_FR'),
                'email' => $faker->unique()->safeEmail,
                'password' => password_hash('password', PASSWORD_BCRYPT),
                'roles' => rand(0,10) != 0 ? ['ROLE_USER'] : ['ROLE_USER', 'ROLE_ADMIN'], // Role par défaut
                'services' => $servicesEntities[rand(0, 1)] // Utiliser services (au pluriel)
            ];
        });

        // Crée des types de congés
        $typesDeConges = [
            'Congé annuel',
            'Congé maladie',
            'Congé sans solde',
            'Congé maternité/paternité',
            'RTT',
            'Congé sabbatique'
        ];

        // Associe chaque utilisateur à des congés fictifs
        foreach ($users as $user) {
            $totalDays = 0; // Initialise le total des jours de congé pour l'utilisateur
            foreach ($typesDeConges as $typeDeConge) {
                $days = rand(1, 30); // Génère un nombre aléatoire de jours de congé entre 1 et 30
                if ($totalDays + $days > 30) {
                    break; // Arrête d'ajouter des congés si le total dépasse 30 jours
                }
                $dateDebut = new \DateTime(sprintf('-%d days', rand(1, 30))); // Génère une date de début aléatoire
                CongeFactory::new()->create([
                    'type' => $typeDeConge, // Type de congé
                    'dateDebut' => $dateDebut, // Date de début du congé
                    'dateFin' => (clone $dateDebut)->modify(sprintf('+%d days', $days)), // Date de fin du congé
                    'statut' => rand(0, 1) ? 'approuvé' : 'en attente', // Statut aléatoire du congé
                    'user' => $user // Associe le congé à l'utilisateur
                ]);
                $totalDays += $days; // Ajoute les jours de congé au total
            }
        }
    }
}
