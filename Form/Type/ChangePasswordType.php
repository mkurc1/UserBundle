<?php

namespace UserBundle\Form\Type;

use Symfony\Component\Form\FormEvent;
use UserBundle\Form\Model\ChangePasswordModel;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ChangePasswordType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('oldPassword', PasswordType::class, [
                'label' => 'change_password.old_password'
            ])
            ->add('newPassword', RepeatedType::class, [
                'type'            => PasswordType::class,
                'first_options'   => [
                    'label' => 'change_password.new_password'
                ],
                'second_options'  => [
                    'label' => 'change_password.new_password_confirmation'
                ],
                'invalid_message' => 'user.password.mismatch'
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class'         => ChangePasswordModel::class,
            'translation_domain' => 'UserBundle'
        ]);
    }
}
