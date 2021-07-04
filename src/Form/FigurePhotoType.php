<?php


namespace App\Form;


use App\Entity\Category;
use App\Entity\Figure;
use App\Entity\FigureImage;
use App\Entity\FigureVideo;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\Validator\Constraints\All;

use Symfony\Component\Validator\Constraints\Composite;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\Image;

class FigurePhotoType extends AbstractType

{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('file', FileType::class, [
                'label' => 'Image',
                'mapped' => false,
                'required' => false]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {

    }
}