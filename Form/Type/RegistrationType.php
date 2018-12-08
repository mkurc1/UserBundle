<?php

namespace UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use UserBundle\Entity\User;

class RegistrationType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'registration.email'
            ])
            ->add('username', null, [
                'label' => 'registration.username'
            ])
            ->add('password', RepeatedType::class, [
                'type'            => PasswordType::class,
                'first_options'   => [
                    'label' => 'registration.password'
                ],
                'second_options'  => [
                    'label' => 'registration.password_confirmation'
                ],
                'invalid_message' => 'registration.password.mismatch'
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class'         => User::class,
            'translation_domain' => 'UserBundle'
        ]);
    }
}
