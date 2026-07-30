<?php

namespace App\Tests\Entity;

use App\Entity\Advert;
use App\Entity\User;
use App\Enum\AdvertStatus;
use PHPUnit\Framework\TestCase;

class UserMetricsTest extends TestCase
{
    public function testUserMetricsCalculation(): void
    {
        $user = new User();

        // 1. Published advert
        $ad1 = new Advert();
        $ad1->setPrice(100.00);
        $ad1->setStatus(AdvertStatus::PUBLISHED);
        $user->addAdvert($ad1);

        // 2. Sold advert #1
        $ad2 = new Advert();
        $ad2->setPrice(250.00);
        $ad2->setStatus(AdvertStatus::SOLD);
        $user->addAdvert($ad2);

        // 3. Sold advert #2
        $ad3 = new Advert();
        $ad3->setPrice(150.00);
        $ad3->setStatus(AdvertStatus::SOLD);
        $user->addAdvert($ad3);

        $this->assertEquals(2, $user->getSoldAdvertsCount());
        $this->assertEquals(400.00, $user->getTotalEarnings());
        $this->assertEquals(1, $user->getActiveAdvertsCount());
        $this->assertEquals(0, $user->getBoughtAdvertsCount());
    }
}
