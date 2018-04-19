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

    // protected $editorTemplate = 'widget.image.html.twig';

    protected $icon = 'fa-image';

    protected $default = array(
        'image'  => '',
        'width'  => '100%',
        'height' => 'auto',
        'alt'    => '',
        'annotation' => '',
        'classImage'  => '',
        'link' => '',
        'gallery' => ''
    );

    protected function configureForm($form)
    {
        return $form
            ->add('image', TextType::class, array(
                'label' => 'Obrázek',
                'attr' => ['class' => 'dropimage'],
            ))
            ->add('width', TextType::class,  array('label' => 'Šířka'))
            ->add('height', TextType::class, array('label' => 'Výška'))
            ->add('alt', TextType::class, array('label' => 'Alt'))
            ->add('annotation', TextareaType::class, array('label' => 'Popisek'))
            ->add('classImage', ChoiceType::class, array(
                'label' => 'Dekorace',
                'choices'  => array('nevybráno' => '', 'Zaoblený okraj' => 'img-rounded', 'V kruhu' => 'img-circle', 'Rámeček' => 'img-thumbnail'),
            ))
            ->add('gallery', ChoiceType::class, array(
                'label' => 'Galerie',
                'choices'  => array('nevybráno' => '', 'Galerie 1' => 'photogallery1', 'Galerie 2' => 'photogallery2', 'Galerie 3' => 'photogallery3'),
            ))
            ->add('link', TextType::class, ['label' => 'Odkaz', 'attr' => ['class' => 'droplink']])
            ;
    }

    public function getWidgetParameters($editor = false)
    {
        $parameters = parent::getWidgetParameters();

        if ($parameters['link'])
        {
            if (preg_match('/\[(.+):([0-9]+)\]/', $parameters['link'], $matches))
            {
                $documentId = $matches[2];
                // najdeme dokument
                $document = $this->em->getRepository('CmsBundle:Document')->find($documentId);
                $parameters['link'] = '/' . $document->getUrl();
            }
        }

        return $parameters;
    }



    public function getDefault()
    {
        return array_merge(parent::getDefault(), $this->default);
    }
}