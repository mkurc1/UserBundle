<?php

namespace UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use UserBundle\Doctrine\UserManager;
use UserBundle\Entity\User;
use UserBundle\Form\Handler\RegistrationHandler;
use UserBundle\Form\Type\RegistrationType;
use UserBundle\Service\Mailer;

class RegistrationController extends AbstractController
{
    /**
     * @Route("/register", name="user_register", methods={"GET", "POST"})
     */
    public function register(UserManager $userManager, RegistrationHandler $handler, TranslatorInterface $translator, Mailer $mailer): Response
    {
        /** @var User $user */
        $user = $userManager->create();
        $handler->buildForm(RegistrationType::class, $user);

        if ($handler->isPostMethod() && $handler->process()) {
            $mailer->sendConfirmationEmail($user);

            $message = $translator->trans('registration.register.successfully', [], 'UserBundle');
            $this->addFlash('success', $message);

            return $this->redirectToRoute('user_login');
        }

        return $this->render('@User/registration/register.html.twig', [
            'form' => $handler->createView()
        ]);
    }

    /**
     * @Route("/confirm/{token}", name="user_confirm", methods={"GET"})
     */
    public function confirm(string $token, UserManager $userManager, TranslatorInterface $translator): Response
    {
        $user = $userManager->findByConfirmationToken($token);
        if (!$user) {
            throw $this->createNotFoundException();
        }

        $userManager->activateAccount($user);

        $message = $translator->trans('registration.confirm.successfully', [], 'UserBundle');
        $this->addFlash('success', $message);

        return $this->redirectToRoute('user_login');
    }
}
