<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class MessageControllerTest extends WebTestCase
{
    public function testInboxRedirectsAnonymousUser(): void
    {
        $client = static::createClient();
        $client->request('GET', '/messages');

        $this->assertResponseRedirects('/login');
    }

    public function testSendMessageRedirectsAnonymousUser(): void
    {
        $client = static::createClient();
        $client->request('GET', '/messages/send/1');

        $this->assertResponseRedirects('/login');
    }
}
