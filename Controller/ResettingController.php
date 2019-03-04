<?php

namespace UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use UserBundle\Doctrine\UserManager;
use UserBundle\Entity\User;
use UserBundle\Form\Handler\ResetPasswordHandler;
use UserBundle\Form\Handler\ResettingHandler;
use UserBundle\Form\Model\UsernameModel;
use UserBundle\Form\Type\ResetPasswordType;
use UserBundle\Form\Type\UsernameType;
use UserBundle\Service\Mailer;

/**
 * @Route("/reset")
 */
class ResettingController extends AbstractController
{
    /**
     * @Route("/request", name="user_reset_request", methods={"GET", "POST"})
     */
    public function request(ResettingHandler $resettingHandler, TranslatorInterface $translator, Mailer $mailer, UserManager $userManager): Response
    {
        $username = new UsernameModel();
        $resettingHandler->buildForm(UsernameType::class, $username);

        if ($resettingHandler->isPostMethod() && $resettingHandler->process()) {
            /** @var User $user */
            $user = $userManager->findByUsername($username->getUsername());
            $mailer->sendResetEmail($user);

            $message = $translator->trans('resetting.request.successfully', [], 'UserBundle');
            $this->addFlash('success', $message);

            return $this->redirectToRoute('user_login');
        }

        return $this->render('@User/resetting/request.html.twig', [
            'form' => $resettingHandler->createView()
        ]);
    }

    /**
     * @Route("/password/{token}", name="user_change_password", methods={"GET", "POST"})
     */
    public function resetPassword(string $token, UserManager $userManager, TranslatorInterface $translator, ResetPasswordHandler $resetPasswordHandler): Response
    {
        $user = $userManager->findByResettingRequestToken($token);
        if (!$user) {
            throw $this->createNotFoundException();
        }

        $resetPasswordHandler->buildForm(ResetPasswordType::class, $user);

        if ($resetPasswordHandler->isPostMethod() && $resetPasswordHandler->process()) {
            $message = $translator->trans('resetting.reset_password.successfully', [], 'UserBundle');
            $this->addFlash('success', $message);

            return $this->redirectToRoute('user_login');
        }

        return $this->render('@User/resetting/reset_password.html.twig', [
            'form' => $resetPasswordHandler->createView()
        ]);
    }
}
