<?php

namespace App\DataFixtures;

use App\Entity\Events;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker;

class EventsFixtures extends Fixture
{
    private $counter = 0;

    public function load(ObjectManager $manager): void
    {
        $faker = Faker\Factory::create('fr_FR');

        $this->createEvent('football', $faker->text(),$manager, 0, $faker->text(8), '' );
        
        
        $this->createEvent('Concert', $faker->text(),$manager, 1, $faker->text(8), '',);

        $manager->flush();
    }

    public function createEvent(string $name, string $description, ObjectManager $manager, int $catId = null, string $city = '', string $quater = '')
    {
        $event = new Events();
        $event->setName($name);
        $event->setDescription($description);
        $event->setCity($city);
        $event->setQuater($quater);

        $categorie = $this->getReference('cat-'.$catId);
        $event->setCategorie($categorie);

        $manager->persist($event);
        
        $this->addReference('eve-'.$this->counter, $event);
        $this->counter++;

        return $event;
        
    }
}
