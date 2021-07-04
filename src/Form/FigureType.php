<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Figure;
use App\Entity\FigureVideo;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
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
            ->add('description', TextType::class, [
            ])
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'title',

            ])
            ->add('files', FileType::class, [
                'label' => 'Images',
                'mapped' => false,
                'required' => false,
                'multiple' => true,
                'constraints' => new All([new Image()])])
            ->add('figureVideos', CollectionType::class, [
                'label' => false,
                'by_reference' => false,
                'entry_type' => FigureVideoType::class,
                'entry_options' => ['label' => false],
                'allow_add' => true,
                'allow_delete' => true
            ]);

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Figure::class,

        ]);
    }

    public function configureOptionsVideos(OptionsResolver $resolver)
    {
        $resolver->setDefaults([

            'data_class' => FigureVideo::class,
        ]);
    }

}