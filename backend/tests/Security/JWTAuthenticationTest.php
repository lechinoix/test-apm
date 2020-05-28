<?php

declare(strict_types=1);

namespace App\Tests\Security;

use App\Security\RefreshTokenManager;
use App\Tests\AuthenticationTrait;
use App\Tests\FixtureAwareCaseTrait;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class JWTAuthenticationTest extends WebTestCase
{
    use AuthenticationTrait;
    use FixtureAwareCaseTrait;

    /** @var KernelBrowser */
    private $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        static::loadFixtures('jwt_authentication.yaml');
    }

    /**
     * @test
     */
    public function itShouldAuthenticateTheUser(): void
    {
        // Without any JWT, the request should be unauthorized

        $this->client->request(
            'GET',
            '/users'
        );
        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $this->client->getResponse()->getStatusCode());

        // With a JWT, it should pass
        $authenticatedClient = $this->authenticateClient($this->client, 'jean_mousquetaire', 'lolilol');

        $this->assertNotEmpty(array_filter(
            $authenticatedClient->getResponse()->headers->getCookies(),
            function ($cookie) {
                return RefreshTokenManager::REFRESH_TOKEN === $cookie->getName() && null !== $cookie->getValue();
            }
        ));

        $authenticatedClient->request(
            'GET',
            '/users'
        );
        $this->assertTrue($authenticatedClient->getResponse()->isOk());
        $this->assertFalse($authenticatedClient->getResponse()->isEmpty());
    }

    /**
     * @test
     */
    public function itShouldRefreshAccessToken(): void
    {
        // Without refreshToken, the request should be unauthorized
        $this->client->request(
            'POST',
            '/auth/jwt/refresh'
        );
        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $this->client->getResponse()->getStatusCode());

        // With a refreshToken, it should pass
        $authenticatedClient = $this->authenticateClient($this->client, 'jean_mousquetaire', 'lolilol');

        $authenticatedClient->request(
            'POST',
            '/auth/jwt/refresh'
        );

        $this->assertTrue($authenticatedClient->getResponse()->isOk());
        $this->assertStringContainsString('access', $authenticatedClient->getResponse()->getContent() ?: '');
    }

    /**
     * @test
     */
    public function itShouldLogoutTheUser(): void
    {
        $authenticatedClient = $this->authenticateClient($this->client, 'jean_mousquetaire', 'lolilol');

        $this->assertNotEmpty(array_filter(
            $authenticatedClient->getResponse()->headers->getCookies(),
            function ($cookie) {
                return RefreshTokenManager::REFRESH_TOKEN === $cookie->getName() && null !== $cookie->getValue();
            }
        ));

        $authenticatedClient->request(
            'POST',
            '/auth/jwt/logout'
        );

        $this->assertEmpty(array_filter(
            $authenticatedClient->getResponse()->headers->getCookies(),
            function ($cookie) {
                return RefreshTokenManager::REFRESH_TOKEN === $cookie->getName() && null !== $cookie->getValue();
            }
        ));
    }
}
