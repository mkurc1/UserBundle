<?php

namespace UserBundle\Form\Model;

use Symfony\Component\Validator\Constraints as Assert;

class UsernameModel
{
    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @Assert\Length(min="2", max="50")
     */
    private $username = '';


    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @param string $username
     */
    public function setUsername(string $username): void
    {
        $this->username = $username;
    }
}
