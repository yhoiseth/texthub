<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class RegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'name',
            null,
            [
                'position' => 'first',
                "help_block"=>"This is the short descriptions shown somewhere",
            ]
        );
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
