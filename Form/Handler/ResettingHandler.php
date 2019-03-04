<?php

namespace UserBundle\Form\Handler;

use CoreBundle\Form\Handler\AbstractFormHandler;
use Symfony\Component\Form\FormError;
use Symfony\Contracts\Translation\TranslatorInterface;
use UserBundle\Doctrine\UserManager;
use UserBundle\Entity\User;
use UserBundle\Form\Model\UsernameModel;
use UserBundle\Utility\TokenGenerator;

class ResettingHandler extends AbstractFormHandler
{
    /**
     * @var UserManager
     */
    private $userManager;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var int
     */
    private $ttl;

    public function __construct(UserManager $userManager, TranslatorInterface $translator, int $ttl)
    {
        $this->userManager = $userManager;
        $this->translator = $translator;
        $this->ttl = $ttl;
    }

    protected function doProcessForm(): bool
    {
        /** @var UsernameModel $username */
        $username = $this->getForm()->getData();

        /** @var User $user */
        $user = $this->userManager->findByUsername($username->getUsername());
        if (!$user) {
            $message = $this->translator->trans('user.not_found', [], 'validators');
            $this->getForm()->get('username')->addError(new FormError($message));

            return false;
        }

        if ($user->isResettingRequestNotExpired($this->ttl)) {
            $message = $this->translator->trans('resetting.request.ttl', [], 'validators');
            $this->getForm()->get('username')->addError(new FormError($message));

            return false;
        }

        $user->setResettingRequestAt(new \DateTime());
        $user->setResettingRequestToken((new TokenGenerator())->generate());

        $this->userManager->update($user);

        return true;
    }
}
