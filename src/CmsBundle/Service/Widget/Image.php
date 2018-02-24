<?php

namespace CmsBundle\Service\Widget;

use CmsBundle\Service\Widget\Base;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

use Symfony\Component\Validator\Constraints as Assert;

class Image extends Base
{

    protected $type = 'Obrázek';

    protected $title = 'Obrázek';

    protected $template = 'image.html.twig';

    protected $editorTemplate = 'widget.image.html.twig';

    protected $icon = 'fa-image';

    protected $default = array(
        'width'  => '100%',
        'height' => 'auto',
        'alt'    => '',
        'annotation' => '',
        'classImage'  => '',
        'link' => ''
    );

    protected function configureForm($form)
    {
        return $form
            ->add('width', TextType::class,  array('label' => 'Šířka'))
            ->add('height', TextType::class, array('label' => 'Výška'))
            ->add('alt', TextType::class, array('label' => 'Alt'))
            ->add('annotation', TextareaType::class, array('label' => 'Popisek'))
            ->add('classImage', ChoiceType::class, array(
                'label' => 'Dekorace',
                'choices'  => array('nevybráno' => '', 'Zaoblený okraj' => 'img-rounded', 'V kruhu' => 'img-circle', 'Rámeček' => 'img-thumbnail'),
            ))
            ->add('link', TextType::class, ['label' => 'Odkaz', 'attr' => ['class' => 'droplink']])
            ;
    }


    public function getDefault()
    {
        return array_merge(parent::getDefault(), $this->default);
    }
}