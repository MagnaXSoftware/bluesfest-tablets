<?php

namespace App\Form;

use App\Boostrap;
use App\Enums\StateEnum;
use App\Models\Area;
use App\Models\Status;
use App\Storage;
use Elao\Enum\Bridge\Symfony\Form\Type\EnumType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\ChoiceList\ChoiceList;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints;

class StatusType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $area_id = $options['area_id'];
        /** @var Storage $db */
        $db = Boostrap::container()->get(Storage::class);

        $builder
            ->add('area', ChoiceType::class, [
                'required' => true,
                'label' => 'Area Name',
                'expanded' => false,
                'multiple' => false,
                'choice_value' => 'id',
                'choice_label' => 'name',
                'choice_attr' => ChoiceList::attr($this, static function (?Area $area): array {
                    return $area ? ['data-expected' => $area->getExpected() ?? 0] : [];
                }),
                'choice_loader' => ChoiceList::lazy($this, function () use ($db): array {
                    return $db->getAreas();
                }),
                'choice_filter' => !is_null($area_id) ? function (?Area $area) use ($area_id): bool {
                    return $area && $area->getId() == $area_id;
                } : null,
            ])
            ->add('status', EnumType::class, [
                'enum_class' => StateEnum::class,
                //'as_value' => true,
                'required' => true,
                'label' => 'Status of the Area',
                'constraints' => [
                    new Constraints\NotBlank(),
                ],
                'choice_filter' => static function (?StateEnum $state): bool {
                    return $state && $state !== StateEnum::NA();
                }
            ])
            ->add('deployed', IntegerType::class, [
                'required' => true,
                'label' => 'Tablets deployed to cashiers',
                'constraints' => [
                    new Constraints\NotBlank(),
                    new Constraints\Type('int'),
                    new Constraints\PositiveOrZero(),
                ],
                'attr' => ['class' => 'count-current'],
            ])
            ->add('stored', IntegerType::class, [
                'required' => true,
                'label' => 'Tablets stored in box',
                'constraints' => [
                    new Constraints\NotBlank(),
                    new Constraints\Type('int'),
                    new Constraints\PositiveOrZero(),
                ],
                'attr' => ['class' => 'count-current'],
            ])
            ->add('recovered', IntegerType::class, [
                'required' => true,
                'label' => 'Tablets returned to IT Trailer',
                'constraints' => [
                    new Constraints\NotBlank(),
                    new Constraints\Type('int'),
                    new Constraints\PositiveOrZero(),
                ],
                'attr' => ['class' => 'count-current'],
            ])
            ->add('actor', TextType::class, [
                'required' => true,
                'label' => 'Who did this update',
                'constraints' => [
                    new Constraints\NotBlank(),
                    new Constraints\Type('string'),
                ],
            ])
            ->add('notes', TextareaType::class, [
                'required' => false,
                'label' => 'Notes',
                'empty_data' => '',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Status::class,
            'area_id' => null,
        ]);
    }
}