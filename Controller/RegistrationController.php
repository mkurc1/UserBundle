<?php

namespace UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use UserBundle\Doctrine\UserManager;
use UserBundle\Form\Handler\RegistrationHandler;
use UserBundle\Form\Type\RegistrationType;

class RegistrationController extends AbstractController
{
    /**
     * @Route("/register", name="user_register", methods={"GET", "POST"})
     */
    public function register(UserManager $userManager, RegistrationHandler $registrationHandler, TranslatorInterface $translator): Response
    {
        $user = $userManager->create();
        $registrationHandler->buildForm(RegistrationType::class, $user);

        if ($registrationHandler->isPostMethod() && $registrationHandler->process()) {
            $message = $translator->trans('registration.register.successfully', [], 'UserBundle');
            $this->addFlash('success', $message);

            return $this->redirectToRoute('user_login');
        }

        return $this->render('@User/registration/register.html.twig', [
            'form' => $registrationHandler->createView()
        ]);
    }
}
