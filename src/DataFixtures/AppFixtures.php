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
        $categories = [
            'Electronics',
            'Vehicles',
            'Real Estate',
            'Fashion',
            'Home & Garden',
            'Jobs & Services',
            'Sports & Leisure',
            'Books & Media',
            'Toys & Hobbies',
            'Health & Beauty',
            'Pets & Animals',
            'Baby & Kids',
            'Art & Collectibles',
            'Business & Industrial',
        ];
        $categoryMap = [];

        foreach ($categories as $catName) {
            $category = new Categories();
            $category->setCategoryName($catName);
            $manager->persist($category);
            $categoryMap[$catName] = $category;
        }

        $admin = new User();
        $admin->setUsername('admin');
        $admin->setEmail('admin@marketplace.com');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setPassword($this->passwordHasher->hashPassword($admin, 'admin123'));
        $admin->setImageFileName('user_admin.png');
        $manager->persist($admin);

        $user = new User();
        $user->setUsername('johndoe');
        $user->setEmail('john@example.com');
        $user->setRoles(['ROLE_USER']);
        $user->setPassword($this->passwordHasher->hashPassword($user, 'password123'));
        $user->setImageFileName('user_john_doe.png');
        $manager->persist($user);

        $user2 = new User();
        $user2->setUsername('sarah_smith');
        $user2->setEmail('sarah@example.com');
        $user2->setRoles(['ROLE_USER']);
        $user2->setPassword($this->passwordHasher->hashPassword($user2, 'password123'));
        $user2->setImageFileName('user_sarah_smith.png');
        $manager->persist($user2);

        $adverts = [
            [
                'title' => 'iPhone 15 Pro Max 256GB - Like New',
                'description' => 'Mint condition iPhone 15 Pro Max in Natural Titanium. Comes with original box, braided USB-C cable, and battery health at 99%.',
                'price' => 999.00,
                'location' => 'London, UK',
                'category' => 'Electronics',
                'status' => AdvertStatus::PUBLISHED,
                'days_ago' => 1,
                'user' => $user,
                'image' => 'item_iphone_15_pro.png',
            ],
            [
                'title' => '2021 BMW 3 Series M Sport',
                'description' => 'Low mileage (22,000 miles), full main dealer service history, pristine condition. Heated leather seats, widescreen iDrive, ambient lighting.',
                'price' => 24500.00,
                'location' => 'Manchester, UK',
                'category' => 'Vehicles',
                'status' => AdvertStatus::PUBLISHED,
                'days_ago' => 2,
                'user' => $user,
                'image' => 'item_bmw_3_series.png',
            ],
            [
                'title' => 'Modern 2 Bedroom Apartment in City Centre',
                'description' => 'Fully furnished luxury apartment with private balcony, floor-to-ceiling windows, hyperfast fiber broadband, and underground parking space.',
                'price' => 1200.00,
                'location' => 'Birmingham, UK',
                'category' => 'Real Estate',
                'status' => AdvertStatus::PUBLISHED,
                'days_ago' => 3,
                'user' => $user2,
                'image' => 'item_city_apartment.png',
            ],
            [
                'title' => 'MacBook Pro 16" M3 Max 36GB RAM 1TB SSD',
                'description' => 'Space Black M3 Max MacBook Pro. Purchased 3 months ago for video editing project. Zero scratches, includes AppleCare+ valid until 2027.',
                'price' => 2850.00,
                'location' => 'Edinburgh, UK',
                'category' => 'Electronics',
                'status' => AdvertStatus::PUBLISHED,
                'days_ago' => 4,
                'user' => $user2,
                'image' => 'item_macbook_pro.png',
            ],
            [
                'title' => 'Sony Alpha A7 IV Mirrorless Camera Body',
                'description' => 'Shutter count only 4,500. Includes 2 original Sony NP-FZ100 batteries, dual charger, strap, and box. Fantastic hybrid camera.',
                'price' => 1750.00,
                'location' => 'Bristol, UK',
                'category' => 'Electronics',
                'status' => AdvertStatus::PUBLISHED,
                'days_ago' => 5,
                'user' => $user,
                'image' => 'item_sony_camera.png',
            ],
            [
                'title' => 'Vintage Leather Chesterfield Sofa 3-Seater',
                'description' => 'Authentic antique oxblood red leather Chesterfield. Deep buttoned upholstery with beautiful natural patina. Very comfortable and sturdy.',
                'price' => 650.00,
                'location' => 'Leeds, UK',
                'category' => 'Home & Garden',
                'status' => AdvertStatus::PUBLISHED,
                'days_ago' => 6,
                'user' => $user2,
                'image' => 'item_chesterfield_sofa.png',
            ],
            [
                'title' => 'Specialized Tarmac SL7 Expert Road Bike 56cm',
                'description' => 'Shimano Ultegra Di2 12-speed electronic shifting, Roval C38 carbon wheels. Serviced regularly, exceptional lightweight road bicycle.',
                'price' => 3400.00,
                'location' => 'Cambridge, UK',
                'category' => 'Sports & Leisure',
                'status' => AdvertStatus::PUBLISHED,
                'days_ago' => 7,
                'user' => $user,
                'image' => 'bike.png',
            ],
            [
                'title' => 'Designer Gucci Canvas Tote Bag (Authentic)',
                'description' => 'GG Supreme canvas medium tote with leather trim. Used twice, condition 9.5/10. Comes with original dustbag and authenticity card.',
                'price' => 520.00,
                'location' => 'London, UK',
                'category' => 'Fashion',
                'status' => AdvertStatus::PUBLISHED,
                'days_ago' => 8,
                'user' => $user2,
                'image' => 'tote.png',
            ],
            [
                'title' => 'Rolex Submariner Date 126610LN (2023 Full Set)',
                'description' => 'Unworn condition 41mm Steel Submariner Date. Includes green box, warranty card dated November 2023, manuals, and swing tags.',
                'price' => 11200.00,
                'location' => 'London, UK',
                'category' => 'Fashion',
                'status' => AdvertStatus::PUBLISHED,
                'days_ago' => 9,
                'user' => $user,
                'image' => 'item_rolex_watch.png',
            ],
            [
                'title' => 'Dyson V15 Detect Absolute Cordless Vacuum',
                'description' => 'Laser Slim Fluffy cleaner head, Digital Motorbar, extra mini motorized tool, wall dock, and charger. Excellent suction performance.',
                'price' => 380.00,
                'location' => 'Glasgow, UK',
                'category' => 'Home & Garden',
                'status' => AdvertStatus::PUBLISHED,
                'days_ago' => 10,
                'user' => $user2,
                'image' => 'dyson.png',
            ],
            [
                'title' => 'PS5 Digital Edition Console + 2 DualSense Controllers',
                'description' => 'PlayStation 5 Console in pristine condition. Includes Midnight Black and White DualSense controllers, HDMI 2.1 cable, and power lead.',
                'price' => 320.00,
                'location' => 'Newcastle, UK',
                'category' => 'Electronics',
                'status' => AdvertStatus::PUBLISHED,
                'days_ago' => 11,
                'user' => $user,
                'image' => 'item_ps5_console.png',
            ],
            [
                'title' => '2019 Honda Civic 1.5 VTEC Turbo SR',
                'description' => 'Manual transmission, 38,000 miles, metallic blue finish. Adaptive cruise control, lane keep assist, Apple CarPlay & Android Auto.',
                'price' => 13800.00,
                'location' => 'Nottingham, UK',
                'category' => 'Vehicles',
                'status' => AdvertStatus::PUBLISHED,
                'days_ago' => 12,
                'user' => $user2,
                'image' => 'honda.png',
            ],
            [
                'title' => 'Herman Miller Aeron Ergonomic Office Chair (Size B)',
                'description' => 'Fully specified Aeron chair with posturefit SL, fully adjustable arms, tilt limiter, and forward tilt. Perfect condition for home office.',
                'price' => 750.00,
                'location' => 'London, UK',
                'category' => 'Business & Industrial',
                'status' => AdvertStatus::PUBLISHED,
                'days_ago' => 13,
                'user' => $user,
                'image' => 'aeron.png',
            ],
            [
                'title' => 'LEGO Star Wars Millennium Falcon 75192 Ultimate Collector',
                'description' => '100% complete 7,541 piece set. Built once and displayed in smoke-free room. Includes original box, instruction manual, and figures.',
                'price' => 580.00,
                'location' => 'Sheffield, UK',
                'category' => 'Toys & Hobbies',
                'status' => AdvertStatus::PUBLISHED,
                'days_ago' => 14,
                'user' => $user2,
                'image' => 'lego.png',
            ],
            [
                'title' => 'Gibson Les Paul Standard 60s Bourbon Burst',
                'description' => 'Solid mahogany body, AA figured maple top, SlimTaper mahogany neck, Burstbucker 61 pickups. Includes hard case and paperwork.',
                'price' => 1950.00,
                'location' => 'Liverpool, UK',
                'category' => 'Art & Collectibles',
                'status' => AdvertStatus::PUBLISHED,
                'days_ago' => 15,
                'user' => $user,
                'image' => 'item_gibson_guitar.png',
            ],
            [
                'title' => '2022 Tesla Model 3 Long Range Dual Motor',
                'description' => 'Pristine Pearl White Tesla Model 3. Full self-driving capability computer, premium black interior, 19" sport wheels, 24,000 miles.',
                'price' => 28900.00,
                'location' => 'Manchester, UK',
                'category' => 'Vehicles',
                'status' => AdvertStatus::PUBLISHED,
                'days_ago' => 16,
                'user' => $user,
                'image' => 'item_tesla_model3.png',
            ],
            [
                'title' => 'Samsung 65" Neo QLED 4K Smart TV (QN90B)',
                'description' => 'Quantum Matrix Technology with Mini LEDs, Dolby Atmos sound, 120Hz refresh rate for gaming. Wall mount and solar cell remote included.',
                'price' => 850.00,
                'location' => 'London, UK',
                'category' => 'Electronics',
                'status' => AdvertStatus::PUBLISHED,
                'days_ago' => 17,
                'user' => $user2,
                'image' => 'item_samsung_tv.png',
            ],
            [
                'title' => 'Canon EOS R6 Mark II Mirrorless Camera Body',
                'description' => '24.2 MP full-frame sensor, 40 fps electronic shutter, 4K 60p uncropped video. Shutter count under 2,000, boxed like new.',
                'price' => 1990.00,
                'location' => 'Bristol, UK',
                'category' => 'Electronics',
                'status' => AdvertStatus::PUBLISHED,
                'days_ago' => 18,
                'user' => $user,
                'image' => 'item_canon_camera.png',
            ],
            [
                'title' => 'Mid-Century Scandinavian Oak Dining Table & 6 Chairs',
                'description' => 'Solid natural oak extendable dining table with 6 matching upholstered chairs. Timeless Danish modern aesthetic in brilliant condition.',
                'price' => 480.00,
                'location' => 'Leeds, UK',
                'category' => 'Home & Garden',
                'status' => AdvertStatus::PUBLISHED,
                'days_ago' => 19,
                'user' => $user2,
                'image' => 'item_dining_table.png',
            ],
            [
                'title' => 'Nike Air Jordan 1 Retro High OG Chicago (Size UK 9.5)',
                'description' => 'Deadstock 100% authentic Jordan 1s in iconic Chicago colourway. Includes extra laces, receipt, and original box.',
                'price' => 290.00,
                'location' => 'London, UK',
                'category' => 'Fashion',
                'status' => AdvertStatus::PUBLISHED,
                'days_ago' => 20,
                'user' => $user,
                'image' => 'item_air_jordans.png',
            ],
            [
                'title' => 'Apple iPad Pro 12.9" M2 256GB Wi-Fi (Space Grey)',
                'description' => 'Liquid Retina XDR display with ProMotion technology. Comes with Apple Pencil 2nd gen and Smart Keyboard Folio cover.',
                'price' => 820.00,
                'location' => 'Edinburgh, UK',
                'category' => 'Electronics',
                'status' => AdvertStatus::PUBLISHED,
                'days_ago' => 21,
                'user' => $user2,
                'image' => 'item_ipad_pro.png',
            ],
            [
                'title' => 'Charming 2 Bedroom Country Cottage with Garden',
                'description' => 'Picturesque stone cottage featuring exposed oak beams, log burner fireplace, south-facing garden, and modern refurbished kitchen.',
                'price' => 1450.00,
                'location' => 'Cotswolds, UK',
                'category' => 'Real Estate',
                'status' => AdvertStatus::PUBLISHED,
                'days_ago' => 22,
                'user' => $user,
                'image' => 'item_cottage.png',
            ],
            [
                'title' => 'Yamaha U1 Upright Acoustic Piano (Polished Ebony)',
                'description' => 'Professional standard Japanese-made Yamaha U1 upright piano. Tuned regularly, rich resonance and clear responsive action.',
                'price' => 3200.00,
                'location' => 'Cambridge, UK',
                'category' => 'Art & Collectibles',
                'status' => AdvertStatus::PUBLISHED,
                'days_ago' => 23,
                'user' => $user2,
                'image' => 'item_yamaha_piano.png',
            ],
            [
                'title' => 'DeLonghi Magnifica S Bean-to-Cup Espresso Machine',
                'description' => 'Integrated milk frother, adjustable coffee strength, twin shot function. Regularly descaled and meticulously maintained.',
                'price' => 240.00,
                'location' => 'Glasgow, UK',
                'category' => 'Home & Garden',
                'status' => AdvertStatus::PUBLISHED,
                'days_ago' => 24,
                'user' => $user,
                'image' => 'item_coffee_machine.png',
            ],
            [
                'title' => 'Peloton Bike+ Interactive Home Exercise Bike',
                'description' => 'Features 23.8" rotating HD touchscreen, auto-follow resistance, and Apple GymKit integration. Includes Peloton shoes size 9.',
                'price' => 1100.00,
                'location' => 'London, UK',
                'category' => 'Sports & Leisure',
                'status' => AdvertStatus::PUBLISHED,
                'days_ago' => 25,
                'user' => $user2,
                'image' => 'item_peloton_bike.png',
            ],
            [
                'title' => 'Steam Deck OLED 512GB Handheld Gaming Console',
                'description' => '7.4" 90Hz HDR OLED display, faster Wi-Fi 6E, improved battery life. Boxed with official carrying case and charger.',
                'price' => 420.00,
                'location' => 'Newcastle, UK',
                'category' => 'Electronics',
                'status' => AdvertStatus::PUBLISHED,
                'days_ago' => 26,
                'user' => $user,
                'image' => 'item_steam_deck.png',
            ],
            [
                'title' => '2020 Audi A4 Avant S Line 2.0 TFSI Quattro',
                'description' => 'Automatic S Tronic, 31,000 miles, Daytona Grey pearl effect. Virtual cockpit, Matrix LED headlights, Bang & Olufsen 3D sound system.',
                'price' => 21500.00,
                'location' => 'Birmingham, UK',
                'category' => 'Vehicles',
                'status' => AdvertStatus::PUBLISHED,
                'days_ago' => 27,
                'user' => $user2,
                'image' => 'item_audi_a4.png',
            ],
            [
                'title' => 'Secretlab TITAN Evo 2022 Ergonomic Gaming Chair',
                'description' => 'SoftWeave Plus fabric in Black3. 4D armrests, magnetic memory foam head pillow, 4-way L-ADAPT lumbar support.',
                'price' => 310.00,
                'location' => 'Sheffield, UK',
                'category' => 'Toys & Hobbies',
                'status' => AdvertStatus::PUBLISHED,
                'days_ago' => 28,
                'user' => $user,
                'image' => 'item_secretlab_chair.png',
            ],
            [
                'title' => 'Breitling Navitimer B01 Chronograph 43 (Black Dial)',
                'description' => 'Iconic aviation chronograph with slide rule bezel. Manufacture Breitling Calibre 01, sapphire crystal caseback, full box and papers.',
                'price' => 4650.00,
                'location' => 'London, UK',
                'category' => 'Fashion',
                'status' => AdvertStatus::PUBLISHED,
                'days_ago' => 29,
                'user' => $user2,
                'image' => 'item_breitling_watch.png',
            ],
            [
                'title' => 'KC Registered Golden Retriever Puppies (Health Tested)',
                'description' => 'Beautiful litter of golden retriever puppies raised in loving family home. Microchipped, vaccinated, with 5 weeks free insurance.',
                'price' => 950.00,
                'location' => 'Oxford, UK',
                'category' => 'Pets & Animals',
                'status' => AdvertStatus::PUBLISHED,
                'days_ago' => 30,
                'user' => $user,
                'image' => 'item_golden_retriever.png',
            ]
        ];

        foreach ($adverts as $data) {
            $advert = new Advert();
            $advert->setTitle($data['title']);
            $advert->setDescription($data['description']);
            $advert->setPrice($data['price']);
            $advert->setLocation($data['location']);
            if (isset($categoryMap[$data['category']])) {
                $advert->setCategory($categoryMap[$data['category']]);
            }
            $advert->setStatus($data['status']);
            $advert->setUser($data['user']);
            $advert->setImageFileName($data['image']);
            
            $date = new \DateTime();
            $date->modify('-' . $data['days_ago'] . ' days');
            $advert->setTimeStamp($date);

            $manager->persist($advert);
        }

        $manager->flush();
    }
}
