<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\Tests\AuthenticationTrait;
use App\Tests\FixtureAwareCaseTrait;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class UserControllerTest extends WebTestCase
{
    use AuthenticationTrait;
    use FixtureAwareCaseTrait;

    /** @var KernelBrowser */
    private $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        static::loadFixtures('user_controller.yaml');
    }

    public function testUnauthenticatedMeRoute(): void
    {
        // Route should be forbidden when not authenticated

        $this->client->request(
            'GET',
            '/auth/me'
        );
        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $this->client->getResponse()->getStatusCode());
    }

    public function testAuthenticatedMeRoute(): void
    {
        // Route should return the user when authenticated

        $authenticatedClient = self::authenticateClient($this->client, 'jean_moustique', 'lolilol');
        $authenticatedClient->request(
            'GET',
            '/auth/me'
        );
        $this->assertTrue($authenticatedClient->getResponse()->isOk());

        $user = json_decode($authenticatedClient->getResponse()->getContent() ?: '', true);

        $this->assertEquals('jean_moustique', $user['email']);
    }
}
