<?php

namespace App\Tests;

class DefaultControllerTest extends LoggedInWebTestCase
{
    public function testSomething()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');

        $this->assertResponseRedirects('/login');
    }
    public function testConnection()
    {
        $this->logIn('admin');

        $crawler = $this->client->request('GET', '/');

        $this->assertResponseIsSuccessful();
    }
}
