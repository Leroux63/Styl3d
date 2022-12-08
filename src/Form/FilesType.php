<?php

namespace App\Form;

use App\Entity\File;
use App\Entity\Product;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FilesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
           // ->add('name')
               ->add('fileLink',FileType::class, [
               'mapped' => false,])
            ->add('product', EntityType::class, [
                'class' => Product::class,
                'choice_label' => 'title',
                'multiple' => false,
                'expanded' => true,

            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => File::class,
        ]);
    }
}
