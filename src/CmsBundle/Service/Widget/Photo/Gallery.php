<?php

namespace CmsBundle\Service\Widget\Photo;

use CmsBundle\Service\Widget\Base;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

use Symfony\Component\Validator\Constraints as Assert;

class Gallery extends Base
{

    protected $type = 'Fotogalerie';

    protected $title = 'Fotogalerie';

    protected $template = 'gallery.html.twig';

    // protected $editorTemplate = 'widget.image.html.twig';

    protected $icon = 'fa-image';

    protected $default = array(
        'width'  => 'col-md-4',
        'classImage'  => '',
        'galleryId' => null,
        'image' => null

    );

    protected function configureForm($form)
    {
        return $form
            ->add('width', ChoiceType::class, array(
                'label' => 'Šířka',
                'choices'  => array('1/3' => 'col-md-4', '1/4' => 'col-md-3', '1/2' => 'col-md-6', '100%' => 'col-md-12'),
            ))
            ->add('classImage', ChoiceType::class, array(
                'label' => 'Dekorace',
                'choices'  => array('nevybráno' => '', 'Zaoblený okraj' => 'img-rounded', 'V kruhu' => 'img-circle', 'Rámeček' => 'img-thumbnail'),
            ))
            ->add('galleryId', EntityType::class, array(
                // looks for choices from this entity
                'class' => 'CmsBundle:Gallery',
                'label' => 'Galerie',

                // uses the User.username property as the visible option string
                'choice_label' => 'name',

                // used to render a select box, check boxes or radios
                // 'multiple' => true,
                // 'expanded' => true,
            ));

    }

    public function getWidgetParameters($editor = false)
    {
        $parameters = parent::getWidgetParameters();
        $parameters['gallery'] = null;

        if ($parameters['galleryId'])
        {
                $galleryId = $parameters['galleryId'];
                // najdeme galerii
                $gallery = $this->em->getRepository('CmsBundle:Gallery')->find($galleryId);

                $parameters['gallery'] = $gallery;
                $images = $gallery->getImages();
                if ($images->count())
                {
                    $parameters['image'] = $images[0]->getPath();
                }
        }

        return $parameters;
    }

    /**
     * Moznost upravit parametry pro urcity widget
     *
     * @param array $parameters
     *
     * @return array
     */
    public function processParameters($parameters)
    {
        if (is_object($parameters['galleryId']))
        {
            $images = $parameters['galleryId']->getImages();
            $parameters['galleryId'] = $parameters['galleryId']->getId();
            if ($images->count())
            {
                $parameters['image'] = $images[0]->getPath();
            }
        }

        return $parameters;
    }



    public function getDefault()
    {
        return array_merge(parent::getDefault(), $this->default);
    }
}