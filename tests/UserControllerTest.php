<?php

namespace App\Tests;


class UserControllerTest extends LoggedInWebTestCase
{
    public function testSomething()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/user');

        $this->assertResponseRedirects('/login');
    }
    public function testConnection()
    {
        $this->logIn('admin');

        $crawler = $this->client->request('GET', '/user');

        $this->assertSelectorExists('p');
        $this->assertSelectorExists('a');
        $this->assertSelectorExists('span');
        $this->assertResponseIsSuccessful();
    }
    public function testDuBoutonDeLaPage()
    {
        $this->logIn('admin');
        $this->client->request('GET', '/user');

        $this->client->clickLink('Modifier');
        $this->assertSelectorExists('form');
    }

    public function testUpdateDeconected()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/user/update');

        $this->assertResponseRedirects('/login');
    }
    public function testConnectionPlusTestSelector()
    {
        $this->logIn('admin');

        $crawler = $this->client->request('GET', '/user/update');

        $this->assertSelectorExists('form');
        $this->assertSelectorExists('label');
        $this->assertSelectorExists('input');
        $this->assertSelectorExists('h1');
        $this->assertSelectorExists('button');
        $this->assertResponseIsSuccessful();
    }
    public function testValideForm()
    {

    }
    public function testValideFormPassword()
    {

    }

}
