<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Figure;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\Validator\Constraints\All;

use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\Image;

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
            ])

            ->add('files', FileType::class, [
                'label' => 'Image de la figure',
                'mapped' => false,
                'multiple' => true,
                'constraints' => new All([new Image()])]);
    }



    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Figure::class,
        ]);
    }
}