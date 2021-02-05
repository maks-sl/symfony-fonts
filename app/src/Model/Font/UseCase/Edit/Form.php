<?php

namespace App\Model\Font\UseCase\Edit;

use App\Model\Font\Entity\Language;
use App\Model\Font\Entity\License;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Form extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('slug', Type\TextType::class, ['label' => 'Slug'])
            ->add('name', Type\TextType::class, ['label' => 'Name'])
            ->add('author', Type\TextType::class, ['label' => 'Author'])
            ->add('license', Type\ChoiceType::class, ['label' => 'License', 'choices' => License::choices()])
            ->add('languages', Type\ChoiceType::class, ['label' => 'Languages', 'multiple'=>true, 'choices' => Language::choices()]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(array(
            'data_class' => Command::class,
        ));
    }
}
