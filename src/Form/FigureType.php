<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Figure;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class FigureType extends AbstractType

{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class)
            ->add('description')
            -> add('category', EntityType::class,[
                'class'=> Category::class,
                'choice_label'=>'title'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Figure::class,
        ]);
    }
}