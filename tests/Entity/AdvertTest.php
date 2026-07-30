<?php

namespace App\Tests\Entity;

use App\Entity\Advert;
use App\Entity\User;
use App\Enum\AdvertStatus;
use PHPUnit\Framework\TestCase;

class AdvertTest extends TestCase
{
    public function testSlugGeneratedAutomaticallyOnTitleSet(): void
    {
        $advert = new Advert();
        $advert->setTitle('Vintage Leather Jacket 1980s');

        $this->assertEquals('vintage-leather-jacket-1980s', $advert->getSlug());
    }

    public function testStatusDefaultAndChange(): void
    {
        $advert = new Advert();
        $this->assertEquals(AdvertStatus::PUBLISHED, $advert->getStatus());
        $this->assertTrue($advert->getIsPublished());

        $advert->setStatus(AdvertStatus::DRAFT);
        $this->assertEquals(AdvertStatus::DRAFT, $advert->getStatus());
        $this->assertFalse($advert->getIsPublished());
    }

    public function testFavoriteRelationship(): void
    {
        $advert = new Advert();
        $user = new User();
        $user->setUsername('testuser');

        $advert->addFavoritedBy($user);
        $this->assertTrue($advert->isFavoritedBy($user));
        $this->assertTrue($user->getFavoriteAdverts()->contains($advert));

        $advert->removeFavoritedBy($user);
        $this->assertFalse($advert->isFavoritedBy($user));
        $this->assertFalse($user->getFavoriteAdverts()->contains($advert));
    }
}
