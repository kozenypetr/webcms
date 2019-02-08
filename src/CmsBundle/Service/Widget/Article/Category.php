<?php

namespace CmsBundle\Service\Widget\Article;

use CmsBundle\Service\Widget\Base;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

use Symfony\Component\Validator\Constraints as Assert;

class Category extends Base
{

    protected $type = 'Článek kategorie';

    protected $title = 'Článek kategorie';

    protected $template = 'article_category.html.twig';

    // protected $editorTemplate = 'widget.image.html.twig';

    protected $icon = 'fa-file-text-o';

    protected $group = 'Články';

    protected $default = array(
        'width'  => 'col-md-4',
        'classImage'  => '',
        'categoryId' => null,
        'tagIds' => null
    );

    protected function configureForm($form)
    {
        return $form
            ->add('width', ChoiceType::class, array(
                'label' => 'Šířka položky',
                'choices'  => array('1/3' => 'col-md-4', '1/4' => 'col-md-3', '1/2' => 'col-md-6', '100%' => 'col-md-12'),
            ))
            ->add('classImage', ChoiceType::class, array(
                'label' => 'Dekorace',
                'choices'  => array('nevybráno' => '', 'Zaoblený okraj' => 'img-rounded', 'V kruhu' => 'img-circle', 'Rámeček' => 'img-thumbnail'),
            ))
            ->add('categoryId', EntityType::class, array(
                // looks for choices from this entity
                'class' => 'CmsBundle:Article\Category',
                'label' => 'Kategorie',

                // uses the User.username property as the visible option string
                'choice_label' => 'name',

                // used to render a select box, check boxes or radios
                'multiple' => false
                // 'expanded' => true,
            ))
            ->add('tagIds', EntityType::class, array(
                // looks for choices from this entity
                'class' => 'CmsBundle:Article\Tag',
                'label' => 'Podkategorie',

                // uses the User.username property as the visible option string
                'choice_label' => 'name',

                // used to render a select box, check boxes or radios
                'multiple' => true,
                'expanded' => true,
            ));

    }

    /**
     * Moznost upravy parametru pred editaci
     * @return mixed
     */
    public function getEditParameters()
    {
        $parameters = $this->getParameters();

        if ($parameters['categoryId'])
        {
            $categoryId = $parameters['categoryId'];
            // najdeme galerii
            $category = $this->em->getRepository('CmsBundle:Article\Category')->find($categoryId);

            $parameters['categoryId'] = $category;
        }

        if ($parameters['tagIds'])
        {
            $ids = $parameters['tagIds'];
            // najdeme galerii

            $criteria = array('id' => $ids);

            $objects = $this->em->getRepository('CmsBundle:Article\Tag')->findBy($criteria);

            $parameters['tagIds'] = $objects;
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

        $parameters['category'] = null;
        $criteria = array();
        if ($parameters['categoryId'])
        {
            $categoryId = $parameters['categoryId'];
            // najdeme kategorii
            $category = $this->em->getRepository('CmsBundle:Article\Category')->find($categoryId);
            $parameters['category'] = $category;
            $criteria['category'] = $category;
        }

        if ($parameters['tagIds'])
        {
            $ids = $parameters['tagIds'];

            $c = array('id' => $ids);
            // najdeme galerii
            $objects = $this->em->getRepository('CmsBundle:Article\Tag')->findBy($c);
            $parameters['tags'] = $objects;

            $criteria['tags'] = $objects;
        }

        // $parameters['items'] = $this->em->getRepository('CmsBundle:Article\Item')->findBy($criteria);

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
        $parameters['categoryId'] = $parameters['categoryId']->getId();

        if (is_array($parameters['tagIds']))
        {
            $ids = array();
            foreach ($parameters['tagIds'] as $tag)
            {
                $ids[] = $tag->getId();
            }

            $parameters['tagIds'] = $ids;
        }

        return $parameters;
    }



    public function getDefault()
    {
        return array_merge(parent::getDefault(), $this->default);
    }
}