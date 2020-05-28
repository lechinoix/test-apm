<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Controller\GetMe;
use App\Controller\Logout;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ApiResource(
 *     normalizationContext={"groups"={User::SERIALIZATION_GROUP_ITEM_READ}},
 *     itemOperations={
 *         "get",
 *         "me"={
 *             "method"=Request::METHOD_GET,
 *             "path"="/auth/me",
 *             "controller"=GetMe::class,
 *             "read"=false,
 *             "swagger_context"={
 *                 "summary"="Retrieves current User resource based on request authorization header.",
 *                 "parameters"={},
 *             },
 *             "openapi_context"={
 *                 "summary"="Retrieves current User resource based on request authorization header.",
 *                 "parameters"={},
 *             },
 *         },
 *         "logout"={
 *             "method"=Request::METHOD_POST,
 *             "path"="/auth/jwt/logout",
 *             "controller"=Logout::class,
 *             "read"=false,
 *             "swagger_context"={
 *                 "summary"="Clear browser stored cookie.",
 *                 "parameters"={},
 *                 "responses"={Response::HTTP_OK={"description"="OK"}},
 *             },
 *             "openapi_context"={
 *                 "summary"="Clear browser stored cookie.",
 *                 "parameters"={},
 *                 "responses"={Response::HTTP_OK={"description"="OK"}},
 *             },
 *         },
 *     },
 *     collectionOperations={
 *         "get"={
 *             "normalization_context"={"groups"={User::SERIALIZATION_GROUP_COLLECTION_READ}}
 *         }
 *     }
 * )
 * @ORM\Table(name="app_users")
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */
class User implements UserInterface
{
    public const SERIALIZATION_GROUP_ITEM_READ = 'user_read';
    public const SERIALIZATION_GROUP_COLLECTION_READ = 'users_read';

    public const ROLE_USER = 'ROLE_USER';
    public const ROLE_ADMIN = 'ROLE_ADMIN';

    /**
     * @var UuidInterface
     *
     * @ORM\Id
     * @ORM\Column(type="uuid", unique=true)
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="Ramsey\Uuid\Doctrine\UuidGenerator")
     * @Groups({User::SERIALIZATION_GROUP_COLLECTION_READ})
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(type="string", length=320, unique=true)
     * @Groups({User::SERIALIZATION_GROUP_ITEM_READ, User::SERIALIZATION_GROUP_COLLECTION_READ})
     */
    private $email;

    /**
     * @var array<string>
     * @ORM\Column(type="json")
     * @Groups({User::SERIALIZATION_GROUP_ITEM_READ, User::SERIALIZATION_GROUP_COLLECTION_READ})
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return (string) $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return $this->getEmail();
    }

    /**
     * @return array<string>
     *
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = self::ROLE_USER;

        return array_unique($roles);
    }

    /**
     * @param array<string> $roles
     */
    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     *
     * @return null
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }
}
