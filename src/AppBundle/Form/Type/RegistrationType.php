<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class RegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'name',
                null,
                [
                    'position' => 'first',
                    'help_block' => "Your full name. If you don't type anything here, your username will be used instead.",
                ]
            )
            ->add(
                'username',
                null,
                [
                    'help_block' => "Your username. This will reserve a space for you on <code>/your-username</code>. Only lowercase a-z characters and the digits 0-9 are allowed. You can also use hyphens (-) in between the characters and numbers.",
                ]
            )
        ;
    }

    public function getParent(): string
    {
        return 'FOS\UserBundle\Form\Type\RegistrationFormType';
    }

    public function getName(): string
    {
        return 'app_user_registration';
    }
}
