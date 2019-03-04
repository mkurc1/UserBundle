<?php

namespace UserBundle\Form\Model;

use UserBundle\Entity\User;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\Validator\Constraints as SecurityAssert;

class ChangePasswordModel
{
    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @Assert\Length(min="6", max="100")
     * @SecurityAssert\UserPassword()
     */
    private $oldPassword = '';

    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @Assert\Length(min="6", max="100")
     */
    private $newPassword = '';

    /**
     * @var User
     */
    private $user;


    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * @return string
     */
    public function getOldPassword(): string
    {
        return $this->oldPassword;
    }

    /**
     * @param string $oldPassword
     */
    public function setOldPassword(string $oldPassword): void
    {
        $this->oldPassword = $oldPassword;
    }

    /**
     * @return string
     */
    public function getNewPassword(): string
    {
        return $this->newPassword;
    }

    /**
     * @param string $newPassword
     */
    public function setNewPassword(string $newPassword): void
    {
        $this->newPassword = $newPassword;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }
}
