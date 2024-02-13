<?php

namespace App\DataFixtures;

use App\Entity\Users;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UsersFixtures extends Fixture
{
    public function __construct(private UserPasswordHasherInterface $passwordEncoder)
    {}

    public function load(ObjectManager $manager): void
    {
        $admin = new Users();
        $admin->setEmail('admin@gmail.com');
        $admin->setName('admin');
        $admin->setSurname('Pambou');
        $admin->setTelephone('44 14 38 75');
        $admin->setPassword(
            $this->passwordEncoder->hashPassword($admin, 'qwerty')
        );
        $admin->setRoles(['ROLE_ADMIN']);

        $manager->persist($admin);

        
        $adminGestion = new Users();
        $adminGestion->setEmail('admingestion@gmail.com');
        $adminGestion->setName('Gestionnaire');
        $adminGestion->setSurname('Admin');
        $adminGestion->setTelephone('44 14 38 75');
        $adminGestion->setPassword(
            $this->passwordEncoder->hashPassword($adminGestion, 'gestion')
        );
        $adminGestion->setRoles(['ROLE_GESTION_ADMIN']);

        $manager->persist($adminGestion);

        $manager->flush();
    }
}
