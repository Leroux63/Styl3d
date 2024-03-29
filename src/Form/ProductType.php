<?php

namespace App\Form;

use App\Entity\Product;
use App\Entity\ProductCategory;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Image;


class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('user', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'id',
            ])
            ->add('title')
            ->add('description')
            ->add('productCategories', EntityType::class, [
                'class' => ProductCategory::class,
                'by_reference' => false,
                'choice_label' => 'name',
                'mapped' => true,
                'multiple' => true,
                'expanded' => true,
            ])
            ->add('createdAt')
            ->add('fileZip', FileType::class, [
                'mapped' => false,
                'label'=>false,
                'constraints' => [
                    new File([
                        'mimeTypes' => [
                            'application/zip',
                        ],
                        'maxSize'=> '30M',
                        'mimeTypesMessage' => "Le fichier n'est pas valide.",
                    ]),
                ],
            ])
            ->add('images', FileType::class, [
                'multiple' => true,
                'mapped' => false,
                'required' => false,
                'label'=>false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
