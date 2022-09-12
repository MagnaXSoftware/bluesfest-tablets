<?php

namespace App\Form\Type;

use App\Enums\StateEnum;
use App\Models\TabletUpdate;
use Elao\Enum\Bridge\Symfony\Form\Type\EnumType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints;

class TabletUpdateType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('state', EnumType::class, [
                'enum_class' => StateEnum::class,
                'required' => true,
                'label' => 'Status',
                'constraints' => [
                    new Constraints\NotBlank(),
                ],
                'choice_filter' => static function (?StateEnum $state): bool {
                    return $state && $state !== StateEnum::NA();
                },
                'expanded' => true,
                'block_prefix' => 'tablet_state',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => TabletUpdate::class,
        ]);
    }
}