<?php

namespace App\DataFixtures;

use App\Entity\Campus;
use App\Entity\Lieu;
use App\Entity\Sortie;
use App\Entity\Ville;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create('fr_FR');


        // on crée 3 campus
        $campus = Array();
        for ($i = 0; $i< 3; $i++){
            $campus[$i] = new Campus();
            $campus[$i]->setNom($faker->city);
            $manager->persist($campus[$i]);
        }

        // on crée 3 villes
        $villes = Array();
        for ($i = 0; $i< 3; $i++){
            $villes[$i] = new Ville();
            $villes[$i]->setNom($faker->city);
            $villes[$i]->setCodePostal($faker->postcode);
            $manager->persist($villes[$i]);
        }

        // on crée 10 lieux
        $lieux = Array();
        for ($i = 0; $i< 10; $i++){
            $lieux[$i] = new Lieu();
            $lieux[$i]->setNom($faker->company);
            $lieux[$i]->setRue($faker->streetAddress);
            $lieux[$i]->setVille($villes[$i % 3]);
            $lieux[$i]->setLatitude($faker->latitude);
            $lieux[$i]->setLongitude($faker->longitude);
            $manager->persist($lieux[$i]);
        }

        $sorties = Array();
        for ($i = 0; $i< 10; $i++){
            $sorties[$i] = new Sortie();
            $sorties[$i]->setNom($faker->sentence);
            $sorties[$i]->setDateFin($faker->dateTimeBetween('-2 years',
            '2 years'));
            $sorties[$i]->setDateDebut($faker->dateTimeBetween('-2 years',
                $sorties[$i]->getDateFin()));
            $sorties[$i]->setDescription($faker->text);
            $sorties[$i]->setNbInscriptionsMax($faker->numberBetween(2,12));
            $sorties[$i]->setUrlPhoto($faker->url);
            $manager->persist($sorties[$i]);
        }
        $manager->flush();
    }
}
