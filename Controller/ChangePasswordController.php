<?php

namespace UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use UserBundle\Form\Handler\ChangePasswordHandler;
use UserBundle\Form\Model\ChangePasswordModel;
use UserBundle\Form\Type\ChangePasswordType;

/**
 * @Route("/profile/change-password")
 */
class ChangePasswordController extends AbstractController
{
    /**
     * @Route("", name="user_profile_change_password", methods={"GET", "POST"})
     */
    public function change(ChangePasswordHandler $handler, TranslatorInterface $translator): Response
    {
        $handler->buildForm(ChangePasswordType::class, new ChangePasswordModel($this->getUser()));

        if ($handler->isPostMethod() && $handler->process()) {
            $message = $translator->trans('change_password.change.successfully', [], 'UserBundle');
            $this->addFlash('success', $message);

            return $this->redirectToRoute('user_profile_change_password');
        }

        return $this->render('@User/change_password/change.html.twig', [
            'form' => $handler->createView()
        ]);
    }
}
