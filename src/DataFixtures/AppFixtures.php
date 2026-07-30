<?php

namespace App\DataFixtures;

use App\Entity\Advert;
use App\Entity\Categories;
use App\Entity\User;
use App\Enum\AdvertStatus;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $categories = ['Electronics', 'Vehicles', 'Real Estate', 'Fashion', 'Home & Garden'];
        $categoryEntities = [];

        foreach ($categories as $catName) {
            $category = new Categories();
            $category->setCategoryName($catName);
            $manager->persist($category);
            $categoryEntities[] = $category;
        }

        $admin = new User();
        $admin->setUsername('admin');
        $admin->setEmail('admin@marketplace.com');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setPassword($this->passwordHasher->hashPassword($admin, 'admin123'));
        $manager->persist($admin);

        $user = new User();
        $user->setUsername('johndoe');
        $user->setEmail('john@example.com');
        $user->setRoles(['ROLE_USER']);
        $user->setPassword($this->passwordHasher->hashPassword($user, 'password123'));
        $manager->persist($user);

        $adverts = [
            [
                'title' => 'iPhone 15 Pro Max 256GB - Like New',
                'description' => 'Mint condition iPhone 15 Pro Max with original box and cable.',
                'price' => 999.00,
                'location' => 'London, UK',
                'category' => $categoryEntities[0],
                'status' => AdvertStatus::PUBLISHED,
            ],
            [
                'title' => '2021 BMW 3 Series M Sport',
                'description' => 'Low mileage, full service history, pristine condition.',
                'price' => 24500.00,
                'location' => 'Manchester, UK',
                'category' => $categoryEntities[1],
                'status' => AdvertStatus::PUBLISHED,
            ],
            [
                'title' => 'Modern 2 Bedroom Apartment in City Centre',
                'description' => 'Fully furnished luxury apartment with balcony and underground parking.',
                'price' => 1200.00,
                'location' => 'Birmingham, UK',
                'category' => $categoryEntities[2],
                'status' => AdvertStatus::PUBLISHED,
            ],
        ];

        foreach ($adverts as $data) {
            $advert = new Advert();
            $advert->setTitle($data['title']);
            $advert->setDescription($data['description']);
            $advert->setPrice($data['price']);
            $advert->setLocation($data['location']);
            $advert->setCategory($data['category']);
            $advert->setStatus($data['status']);
            $advert->setUser($user);
            $advert->setTimeStamp(new \DateTime());
            $manager->persist($advert);
        }

        $manager->flush();
    }
}
