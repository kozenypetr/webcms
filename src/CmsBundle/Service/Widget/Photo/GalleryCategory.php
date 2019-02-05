<?php

namespace CmsBundle\Service\Widget\Photo;

use CmsBundle\Service\Widget\Base;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

use Symfony\Component\Validator\Constraints as Assert;

class GalleryCategory extends Base
{

    protected $type = 'Fotokategorie';

    protected $title = 'Fotokategorie';

    protected $template = 'gallery_category.html.twig';

    // protected $editorTemplate = 'widget.image.html.twig';

    protected $icon = 'fa-image';

    protected $default = array(
        'width'  => 'col-md-4',
        'classImage'  => '',
        'galleryCategoryId' => null

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
            ->add('galleryCategoryId', EntityType::class, array(
                // looks for choices from this entity
                'class' => 'CmsBundle:GalleryCategory',
                'label' => 'Kategorie',

                // uses the User.username property as the visible option string
                'choice_label' => 'name',

                // used to render a select box, check boxes or radios
                'multiple' => true,
                // 'expanded' => true,
            ));

    }

    /**
     * Moznost upravy parametru pred editaci
     * @return mixed
     */
    public function getEditParameters()
    {
        $parameters = $this->getParameters();

        if ($parameters['galleryCategoryId'])
        {
            $galleryCategoryId = $parameters['galleryCategoryId'];
            // najdeme galerii
            $galleryCategories = $this->em->getRepository('CmsBundle:GalleryCategory')->findById($galleryCategoryId);

            $parameters['galleryCategoryId'] = $galleryCategories;
        }

        return $parameters;
    }

    /**
     * Uprava parametru pro zobrazeni
     *
     * @param bool $editor
     *
     * @return mixed
     */
    public function getWidgetParameters($editor = false)
    {
        $parameters = parent::getWidgetParameters();

        if ($parameters['galleryCategoryId'])
        {
                $galleryCategoryId = $parameters['galleryCategoryId'];
                // najdeme galerii
                $galleryCategories = $this->em->getRepository('CmsBundle:GalleryCategory')->findById($galleryCategoryId);

                $parameters['categories'] = $galleryCategories;
        }

        return $parameters;
    }


    /**
     * Moznost upravit parametry pro pred ulozenim do databaze
     *
     * @param array $parameters
     *
     * @return array
     */
    public function processParameters($parameters)
    {
        if (is_array($parameters['galleryCategoryId']))
        {
            $ids = array();
            foreach ($parameters['galleryCategoryId'] as $category)
            {
                $ids[] = $category->getId();
            }

            $parameters['galleryCategoryId'] = $ids;
        }

        return $parameters;
    }



    public function getDefault()
    {
        return array_merge(parent::getDefault(), $this->default);
    }
}