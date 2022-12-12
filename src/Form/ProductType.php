<?php

namespace App\Form;

use App\Entity\File;
use App\Entity\Product;
use App\Entity\ProductCategory;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use function Sodium\add;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title')
            ->add('description')
            ->add('productCategories', EntityType::class, [
                'class' => ProductCategory::class,
                'by_reference' => false,
                'choice_label' => 'name',
                'mapped'=>true,
                'multiple' => true,
                'expanded' => true,
            ])
            ->add('imageFile',FileType::class,[
                'mapped'=> false,
            ])
            ->add('files',EntityType::class,[
                'class'=>File::class,
                'choice_label'=>'id',
                'multiple' => true,
                'expanded' => true,
            ]);


    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
