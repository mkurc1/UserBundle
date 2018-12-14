<?php

namespace UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use CoreBundle\Entity\Traits\PrimaryKeyEntityTrait;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use UserBundle\Utility\TokenGenerator;

/**
 * @ORM\MappedSuperclass()
 *
 * @UniqueEntity(fields={"username"})
 * @UniqueEntity(fields={"email"})
 */
abstract class User implements UserInterface, EquatableInterface
{
    use PrimaryKeyEntityTrait;
    use TimestampableEntity;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=50, unique=true)
     *
     * @Assert\NotBlank()
     * @Assert\Length(min="2", max="50")
     */
    private $username = '';

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=100, unique=true)
     *
     * @Assert\NotBlank()
     * @Assert\Length(min="5", max="100")
     * @Assert\Email(strict="true")
     */
    private $email = '';

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     *
     * @Assert\NotBlank()
     */
    private $password = '';

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=100)
     */
    private $salt;

    /**
     * @var array
     *
     * @ORM\Column(type="array")
     */
    private $roles = [];

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean")
     */
    private $enabled = false;

    /**
     * @var null|string
     *
     * @ORM\Column(type="string", nullable=true, length=100)
     */
    private $confirmationToken;

    /**
     * @var bool
     *
     * @Assert\IsTrue()
     */
    private $acceptRegulation = false;


    public function __construct()
    {
        $this->salt = (new TokenGenerator())->generate();
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @param string $username
     * @return User
     */
    public function setUsername(string $username): User
    {
        $this->username = $username;
        return $this;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     * @return User
     */
    public function setEmail(string $email): User
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @param string $password
     * @return User
     */
    public function setPassword(?string $password): User
    {
        $this->password = $password ?: '';
        return $this;
    }

    /**
     * @return string
     */
    public function getSalt(): string
    {
        return $this->salt;
    }

    /**
     * @return array
     */
    public function getRoles(): array
    {
        return $this->roles;
    }

    /**
     * @param array $roles
     * @return User
     */
    public function setRoles(array $roles): User
    {
        $this->roles = $roles;
        return $this;
    }

    /**
     * @param string $role
     * @return User
     */
    public function addRole(string $role): User
    {
        $role = strtoupper($role);

        if (!in_array($role, $this->roles, true)) {
            $this->roles[] = $role;
        }

        return $this;
    }

    /**
     * @param string $role
     * @return bool
     */
    public function hasRole(string $role): bool
    {
        $role = strtoupper($role);

        return in_array($role, $this->getRoles(), true);
    }

    /**
     * @param string $role
     * @return User
     */
    public function removeRole(string $role): User
    {
        $role = strtoupper($role);

        if (false !== $key = array_search($role, $this->roles, true)) {
            unset($this->roles[$key]);
            $this->roles = array_values($this->roles);
        }

        return $this;
    }

    /**
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * @param bool $enabled
     */
    public function setEnabled(bool $enabled): void
    {
        $this->enabled = $enabled;
    }

    /**
     * @param UserInterface $user
     * @return bool
     */
    public function isEqualTo(UserInterface $user): bool
    {
        if (!$user instanceof User) {
            return false;
        }

        if ($this->password !== $user->getPassword()) {
            return false;
        }

        if ($this->salt !== $user->getSalt()) {
            return false;
        }

        if ($this->username !== $user->getUsername()) {
            return false;
        }

        return true;
    }

    public function eraseCredentials()
    {
    }

    /**
     * @return null|string
     */
    public function getConfirmationToken(): ?string
    {
        return $this->confirmationToken;
    }

    /**
     * @param null|string $confirmationToken
     */
    public function setConfirmationToken(?string $confirmationToken): void
    {
        $this->confirmationToken = $confirmationToken;
    }

    /**
     * @return bool
     */
    public function isAcceptRegulation(): bool
    {
        return $this->acceptRegulation;
    }

    /**
     * @param bool $acceptRegulation
     */
    public function setAcceptRegulation(bool $acceptRegulation): void
    {
        $this->acceptRegulation = $acceptRegulation;
    }
}
