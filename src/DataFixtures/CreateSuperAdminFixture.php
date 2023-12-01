<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class CreateSuperAdminFixture extends Fixture
{
    private UserRepository $userRepository;
    private UserPasswordHasherInterface $passwordHasher;
    /**
     * run console - php bin/console doctrine:fixtures:load --append --group=CreateSuperAdminFixture
     */

    public const SUPER_ADMIN_EMAIL = 'test@jelvix.com';

    public function __construct(
        UserRepository $userRepository,
        UserPasswordHasherInterface $passwordHasher
    ) {
        $this->userRepository = $userRepository;
        $this->passwordHasher = $passwordHasher;
    }

    /**
     * @param ObjectManager $manager
     * @return void
     */
    public function load(ObjectManager $manager): void
    {
        $superAdmin = new User(
            self::SUPER_ADMIN_EMAIL, //email
            null, //password
            ['ROLE_SUPER_ADMIN'] // role
        );

        $superAdmin->setPassword($this->passwordHasher->hashPassword($superAdmin, '123Qwerty'));

        try {
            $this->userRepository->save($superAdmin);
            $manager->persist($superAdmin);
            $manager->flush();
        } catch (\Exception $exception) {
            dd($exception->getMessage());
        }
    }
}
