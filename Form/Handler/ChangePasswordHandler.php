<?php

namespace UserBundle\Form\Handler;

use CoreBundle\Form\Handler\AbstractFormHandler;
use UserBundle\Doctrine\UserManager;
use UserBundle\Form\Model\ChangePasswordModel;
use UserBundle\Utility\TokenGenerator;

class ChangePasswordHandler extends AbstractFormHandler
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
        /** @var ChangePasswordModel $changePasswordModel */
        $changePasswordModel = $this->getForm()->getData();
        $user = $changePasswordModel->getUser();

        $user->setPassword($this->userManager->encodePassword($user, $changePasswordModel->getNewPassword()));
        $user->setConfirmationToken((new TokenGenerator())->generate());

        $this->userManager->update($user);

        return true;
    }
}
