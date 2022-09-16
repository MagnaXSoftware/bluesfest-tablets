<?php

namespace App\Form;

use App\Models\Area;
use App\Models\Tablet;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints;

class TabletType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('code', TextType::class, [
                'required' => true,
                'label' => 'Device Code',
                'constraints' => [
                    new Constraints\NotBlank(),
                    new Constraints\Type('string'),
                    new Constraints\Regex([
                        'pattern' => '/^[a-zA-Z0-9]{4}(\s?[a-zA-Z0-9]{4}){2}$/',
                        'message' => 'Device Code must be 12 alpha-numeric characters.',
                        'normalizer' => static function (string $input): string {
                            return strtoupper(str_replace(' ', '', $input));
                        }
                    ]),
                ],
                'attr' => [
                    'autofocus' => null
                ],
            ])
            ->add('area', EntityType::class, [
                'required' => true,
                'label' => 'Assigned Area',
                'class' => Area::class,
                'choice_label' => 'name',
            ]);

        $builder->get('code')->addModelTransformer(new CallbackTransformer(
            static function ($normData) {
                return $normData;
            },
            static function ($viewData): string {
                $parts = str_split(strtoupper(str_replace(' ', '', $viewData)), 4);
                if (count($parts) != 3) {
                    $failure = new TransformationFailedException("Invalid chunk count");
                    $failure->setInvalidMessage("Device Code must be 12 characters.");

                    throw $failure;
                }

                return sprintf("%s %s %s", ...$parts);
            }));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Tablet::class,
        ]);
    }
}