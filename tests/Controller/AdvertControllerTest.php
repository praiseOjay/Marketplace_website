<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AdvertControllerTest extends WebTestCase
{
    public function testHomePageIsSuccessful(): void
    {
        $client = static::createClient();
        $client->request('GET', '/advert/index');

        $this->assertResponseIsSuccessful();
    }

    public function testNewAdvertRedirectsAnonymousUser(): void
    {
        $client = static::createClient();
        $client->request('GET', '/advert/new');

        $this->assertResponseRedirects('/login');
    }
}
