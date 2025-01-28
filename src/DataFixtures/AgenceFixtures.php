<?php

namespace App\DataFixtures;

use App\Entity\Agence;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AgenceFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // Créer 5 agences de test
        for ($i = 1; $i <= 5; $i++) {
            $agence = new Agence();
            $agence->setNumero('AG00' . $i);
            $agence->setAdresse('Adresse ' . $i);
            $agence->setTelephone('777777777' . $i);

            $manager->persist($agence);
        }

        
        $manager->flush();
    }
}