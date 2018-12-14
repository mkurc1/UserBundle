<?php

namespace UserBundle\Form\Handler;

use CoreBundle\Form\Handler\AbstractFormHandler;
use UserBundle\Doctrine\UserManager;
use UserBundle\Entity\User;
use UserBundle\Utility\TokenGenerator;

class RegistrationHandler extends AbstractFormHandler
{
    /**
     * @var UserManager
     */
    private $userManager;

    public function __construct(UserManager $userManager)
    {
        $this->userManager = $userManager;
    }

    protected function doProcessForm(): bool
    {
        /** @var User $user */
        $user = $this->getForm()->getData();

        $user->setPassword($this->userManager->encodePassword($user, $user->getPassword()));
        $user->setConfirmationToken((new TokenGenerator())->generate());

        $this->userManager->update($user);

        return true;
    }
}
