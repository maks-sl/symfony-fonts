<?php

declare(strict_types=1);

namespace App\Model\Font\UseCase\Files\Unzip;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class Form extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('zip_file', Type\FileType::class, [
                'mapped' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please select Zip File',
                    ]),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(array(
            'data_class' => Command::class,
        ));
    }
}

