<?php

declare(strict_types=1);

namespace App\Form;

use App\Models\Area;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints;

class AreaType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'required' => true,
                'label' => 'Area Name',
                'constraints' => [
                    new Constraints\NotBlank(),
                    new Constraints\Type('string'),
                ],
                'attr' => [
                    'autofocus' => null
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Area::class,
        ]);
    }
}