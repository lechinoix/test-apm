<?php

namespace App\Security;

use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\Security\Core\User\UserInterface;

class RefreshTokenManager
{
    const REFRESH_TOKEN = 'refreshToken';
    const REFRESH_TOKEN_LIFETIME = 2592000; // 30 days
    const ID_FIELD = 'username';
    const CREATION_DATE_FIELD = 'iat';
    const EXPIRATION_DATE_FIELD = 'exp';

    /**
     * @var JWTEncoderInterface
     */
    private $jwtEncoder;

    public function __construct(JWTEncoderInterface $jwtEncoder)
    {
        $this->jwtEncoder = $jwtEncoder;
    }

    /**
     * @return Cookie
     *
     * @throws \Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTEncodeFailureException
     */
    public function createCookie(UserInterface $user, bool $secure)
    {
        $refreshToken = $this->jwtEncoder->encode([
            self::ID_FIELD => $user->getUsername(),
            self::CREATION_DATE_FIELD => time(),
            self::EXPIRATION_DATE_FIELD => time() + self::REFRESH_TOKEN_LIFETIME,
        ]);

        return new Cookie(self::REFRESH_TOKEN, $refreshToken, 0, '/', null, $secure);
    }
}
