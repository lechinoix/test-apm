<?php

declare(strict_types=1);

namespace App\Controller;

use App\Security\RefreshTokenManager;
use Symfony\Component\HttpFoundation\Response;

class Logout
{
    public function __invoke(): Response
    {
        $response = new Response();
        $response->headers->clearCookie(RefreshTokenManager::REFRESH_TOKEN);

        return $response;
    }
}
