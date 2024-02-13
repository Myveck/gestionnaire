<?php

namespace App\DataFixtures;

use App\Entity\Images;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ImagesFixtures extends Fixture
{
    private $i = 0;
    public function load(ObjectManager $manager): void
    {
        $this->createImage($manager, '19e1f1197ba26e391228a2e9530add4e.webp');
        $this->createImage($manager, 'b2d1019dceafd21b40c35848e5faeabc.webp');
        $this->i++;
        $this->createImage($manager, '4cf2c92023cf87078bca09a1bbbf9fc1.webp');
        $this->createImage($manager, '51760aba5e1581ea18d0ca7f2344b23a.webp');

        $manager->flush();
    }

    public function createImage(ObjectManager $manager, string $name)
    {   
        $image = new Images();
        $image->setName($name);

        $event = $this->getReference('eve-'. $this->i);
        $image->setEvent($event);
        $manager->persist($image);

    }
}