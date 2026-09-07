<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Factory\AuthorFactory;
use App\Factory\BookFactory;
use App\Factory\SubscriberFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use function Zenstruck\Foundry\faker;

class AppFixtures extends Fixture
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher
    ) {}

    
    public function load(ObjectManager $manager): void
    {
        $admin = new User();
        $admin->setEmail('admin@example.com');
        $admin->setRoles(['ROLE_ADMIN']);
        $hashedPassword = $this->passwordHasher->hashPassword($admin, 'adminTs12T72');
        $admin->setPassword($hashedPassword);
        
        $manager->persist($admin);
        $manager->flush();

        // Создаем 20 авторов 
        $authors = AuthorFactory::createMany(20);

        // Создаем 20 книг
        BookFactory::createMany(20, function() use ($authors) {
            return [
                // Для каждой книги случайно выбираем от 1 до 2 авторов из ранее созданных
                'authors' => faker()->randomElements($authors, faker()->numberBetween(1, 2))
            ];
        });

        // Создаем 20 подписчиков
        SubscriberFactory::createMany(50, function() use ($authors) {
            return [
                'author' => faker()->randomElement($authors)
            ];
        });
    }
}
