<?php

namespace App\User\Entity\Security;

use App\User\Entity\Security\Exception\PasswordNotMatchedException;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="credentials")
 * @Assert\UniqueEntity(
 *     fields={"email.email"},
 *     errorPath="email"
 * )
 */
class Credential implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\Column(type="guid")
     */
    private $id;

    /**
     * @var string
     * @ORM\Embedded(class="App\User\Entity\Security\Email", columnPrefix=false)
     */
    private $email;

    /**
     * @var Password
     * @ORM\Embedded(class="App\User\Entity\Security\Password", columnPrefix=false)
     */
    private $password;

    /**
     * @var array
     * @ORM\Column(type="json_array", options={"jsonb": true})
     */
    private $roles;


    public function __construct(string $id, Email $email, Password $password, array $roles)
    {
        $this->id = $id;
        $this->email = $email;
        $this->password = $password;
        $this->roles = $roles;
    }

    /**
     * @return Email
     */
    public function email(): Email
    {
        return $this->email;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function getPassword()
    {
        return $this->password->secret();
    }

    public function getSalt()
    {
        return $this->password->salt();
    }

    public function getUsername()
    {
        return (string) $this->email;
    }

    public function eraseCredentials()
    {
    }

    /**
     * Validate password
     *
     * @param string $password
     * @param UserPasswordEncoderInterface $encoder
     * @throws PasswordNotMatchedException
     */
    public function validatePassword(string $password, UserPasswordEncoderInterface $encoder): void
    {
        if (!$encoder->isPasswordValid($this, $password)) {
            throw new PasswordNotMatchedException();
        }
    }
}