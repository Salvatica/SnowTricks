<?php


namespace App\Form;

use App\Entity\FigureVideo;
use App\Service\VideoLinkSanitizer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FigureVideoType extends AbstractType
{
    public function __construct(private VideoLinkSanitizer $sanitizer)
    {

    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('fileName', null, [
            'setter' => function (FigureVideo $video, ?string $input, FormInterface $form) {
                $video->setFileName($this->sanitizer->clean($input));

            },

        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => FigureVideo::class,
        ]);
    }

}