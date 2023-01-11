<?php

namespace App\Form;

use App\Entity\Rating;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Range;

class RatingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('score',NumberType::class,[
                'label' => false,
                'attr' => [
                    'placeholder' => 'Entrez une note entre 1 et 5'
                ],
                'constraints' => [
                    new Range([
                        'min' => 1,
                        'max' => 5,
                        'minMessage' => 'La note doit être au moins de {{ limit }}',
                        'maxMessage' => 'La note ne peut pas être supérieure à {{ limit }}',
                    ]),
                ],
            ])
            ->add('user',EntityType::class, [
                'class' => User::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Rating::class,
        ]);
    }
}
