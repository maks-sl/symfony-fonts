<?php

namespace App\Model\User\UseCase\Create;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Form extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', Type\EmailType::class, ['label' => 'Email'])
            ->add('firstName', Type\TextType::class, ['label' => 'First Name'])
            ->add('lastName', Type\TextType::class, ['label' => 'Last Name'])
            ->add('password', Type\PasswordType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(array(
            'data_class' => Command::class,
        ));
    }
}
