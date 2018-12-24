<?php

namespace UserBundle\Service;

use Symfony\Contracts\Translation\TranslatorInterface;
use UserBundle\Entity\User;

class Mailer
{
    /**
     * @var \Swift_Mailer
     */
    private $mailer;

    /**
     * @var \Twig_Environment
     */
    private $templating;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var string
     */
    private $senderEmail;


    public function __construct(\Swift_Mailer $mailer, \Twig_Environment $templating, TranslatorInterface $translator, string $senderEmail)
    {
        $this->mailer = $mailer;
        $this->templating = $templating;
        $this->translator = $translator;
        $this->senderEmail = $senderEmail;
    }

    public function sendConfirmationEmail(User $user): void
    {
        $subject = $this->translator->trans('email.confirmation.subject', [], 'UserBundle');

        $view = $this->templating->render('@User/email/confirmation.html.twig', [
            'user' => $user
        ]);

        $this->sendEmailMessage($view, $subject, $user->getEmail());
    }

    public function sendResetEmail(User $user): void
    {
        $subject = $this->translator->trans('email.resetting.subject', [], 'UserBundle');

        $view = $this->templating->render('@User/email/resetting.html.twig', [
            'user' => $user
        ]);

        $this->sendEmailMessage($view, $subject, $user->getEmail());
    }

    protected function sendEmailMessage($view, string $subject, string $toEmail): void
    {
        $message = (new \Swift_Message())
            ->setSubject($subject)
            ->setFrom($this->senderEmail)
            ->setTo($toEmail)
            ->setBody($view, 'text/html');

        $this->mailer->send($message);
    }
}
