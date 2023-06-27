<?php

declare(strict_types=1);

namespace App\Form;

use App\Form\Type\TabletUpdateType;
use App\Models\Area;
use App\Models\TabletUpdate;
use App\Models\Update;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints;

class UpdateType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $area_id = $options['area_id'];

        $builder
            ->add('area', EntityType::class, [
                'required' => true,
                'label' => 'Area Name',
                'expanded' => false,
                'multiple' => false,
                'class' => Area::class,
                'query_builder' => !is_null($area_id) ? function (EntityRepository $repository) use ($area_id): QueryBuilder {
                    return $repository->createQueryBuilder('a')->where('a.id = :area_id')->setParameter('area_id', $area_id);
                } : null,
                'choice_label' => 'name',
                'placeholder' => 'Select an Area',
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
            ]);

        $formModifier = static function (FormInterface $form, Update $update, ?Area $area) {
            if ($area and $update->getTabletUpdates()->count() != $area->getExpected()) {
                $update->getTabletUpdates()->clear();
                foreach ($area->getTablets() as $i => $tablet) {
                    $tabletUpdate = new TabletUpdate();
                    $tabletUpdate->setTablet($tablet);
                    $tabletUpdate->setUpdate($update);
                    if (null !== $tablet->getLastUpdate()) {
                        $tabletUpdate->setState($tablet->getLastUpdate()->getState());
                    }
                    $update->getTabletUpdates()->set($i, $tabletUpdate);
                }
            }

            $form->add('tablet_updates', CollectionType::class, [
                'entry_type' => TabletUpdateType::class,
                'label' => 'Tablet Status'
            ]);
        };


        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) use ($formModifier) {
                // Sets the update's TabletUpdate array, if the area was defined on form creation.
                /** @var Update $update */
                $update = $event->getData();
                $area = $update->getArea();

                $formModifier($event->getForm(), $update, $area);
            }
        );
        $builder->get('area')->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) use ($formModifier) {
                // Sets the update's TabletUpdate array, during form submission, if the area was only added after creation.
                /** @var Update $update */
                $update = $event->getForm()->getParent()->getData();
                /** @var Area $area */
                $area = $event->getForm()->getData();

                $formModifier($event->getForm()->getParent(), $update, $area);
            }
        );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Update::class,
            'area_id' => null,
        ]);
    }
}