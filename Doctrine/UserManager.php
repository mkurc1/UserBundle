<?php

namespace UserBundle\Doctrine;

use CoreBundle\Doctrine\AbstractManager;
use UserBundle\Entity\User;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserManager extends AbstractManager
{
    /**
     * @var UserPasswordEncoderInterface
     */
    protected $encoder;

    public function setEncoder(UserPasswordEncoderInterface $encoder): void
    {
        $this->encoder = $encoder;
    }

    public function findByConfirmationToken(string $token): ?User
    {
        return $this->findOneBy([
            'confirmationToken' => $token
        ]);
    }

    public function findByResettingRequestToken(string $token): ?User
    {
        return $this->findOneBy([
            'resettingRequestToken' => $token,
            'enabled'               => true
        ]);
    }

    public function findByUsername(string $username): ?User
    {
        return $this->findOneBy([
            'username' => $username,
            'enabled'  => true
        ]);
    }

    public function activateAccount(User $user): void
    {
        $user->setEnabled(true);
        $user->setConfirmationToken(null);

        $this->update($user);
    }

    public function createUser(string $username, string $email, string $password): User
    {
        /** @var User $user */
        $user = $this->create();

        $user->setUsername($username);
        $user->setEmail($email);

        $encodedPassword = $this->encodePassword($user, $password);
        $user->setPassword($encodedPassword);

        $this->update($user);

        return $user;
    }

    public function encodePassword(UserInterface $user, string $password): string
    {
        return $this->encoder->encodePassword($user, $password);
    }
}
