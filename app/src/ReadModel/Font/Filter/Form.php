<?php

namespace App\ReadModel\Font\Filter;

use App\Model\Font\Entity\Status;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Form extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('slug', Type\TextType::class, ['required' => false, 'attr' => [
                'placeholder' => 'Search slug...',
                'onchange' => 'this.form.submit()',
            ]])
            ->add('name', Type\TextType::class, ['required' => false, 'attr' => [
                'placeholder' => 'Search name...',
                'onchange' => 'this.form.submit()',
            ]])
            ->add('status', Type\ChoiceType::class, ['choices' => Status::choices(), 'required' => false, 'placeholder' => 'All statuses', 'attr' => ['onchange' => 'this.form.submit()']]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Data::class,
            'method' => 'GET',
            'csrf_protection' => false,
        ]);
    }
}
