<?php

namespace App\Tests;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\Security\Guard\Token\PostAuthenticationGuardToken;

abstract class LoggedInWebTestCase extends WebTestCase
{
    /** @var KernelBrowser|null  */
    protected $client = null;
    /**
     * @var \Doctrine\ORM\EntityManager|null
     */
    protected $entityManager = null;

    /** @var User|null $user */
    protected $user = null;

    public function setUp()
    {
        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $this->client = static::createClient();
    }

    protected function logIn($username = "yo")
    {
        $session = $this->client->getContainer()->get('session');

        $firewallName = 'main';
        $firewallContext = 'main';

        /** @var User $user */
        $this->user = $this->entityManager->getRepository(User::class)->findOneBy(['username' => $username]);

        $token = new PostAuthenticationGuardToken($this->user, $firewallName, $this->user->getRoles());
        $session->set('_security_'.$firewallContext, serialize($token));
        $session->save();

        $cookie = new Cookie($session->getName(), $session->getId());
        $this->client->getCookieJar()->set($cookie);
    }
}