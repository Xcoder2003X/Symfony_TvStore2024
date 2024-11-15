<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Product;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('description')
            ->add('image',FileType::class,["required"=>false
            ,'data_class' => null,] ) // Prevents the form from expecting a File object
            ->add('price')
            ->add('quantity')

            //Imagine you have a Category entity with multiple categories stored in your database. This form 
            //field allows users to select one of those categories when filling out the form.
            // Instead of displaying the entire entity, it shows the id of each category
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name',
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
