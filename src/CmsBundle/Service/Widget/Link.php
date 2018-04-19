<?php

namespace CmsBundle\Service\Widget;

use CmsBundle\Service\Widget\Base;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

use CmsBundle\Entity\DocumentCategory;


use Symfony\Component\Validator\Constraints as Assert;

class Link extends Base
{

    protected $type = 'Odkaz';

    protected $title = 'Odkaz';

    protected $template = 'link.html.twig';

    protected $icon = 'fa-link';

    protected $predefinedClasses = array(

    );

    protected $default = array(
        'document'    => '',
        'title'       => '',
        'image'       => '',
        'annotation'  => '',
        'date'        => '',
        'subtitle'    => '',
        'link'        => '',
    );

    protected function configureForm($form)
    {
        return $form
            ->add('document', TextType::class, ['label' => 'Stránka', 'attr' => ['class' => 'droplink']])
            ->add('title', TextType::class, ['label' => 'Nadpis'])
            ->add('annotation', TextType::class, ['label' => 'Krátký popis'])
            ->add('image', TextType::class, array(
                'label' => 'Obrázek',
                'attr' => ['class' => 'dropimage'],
            ))
            ->add('date', TextType::class, ['label' => 'Datum'])
            ->add('subtitle', TextType::class, ['label' => 'Podnadpis'])
            /*->add('category', EntityType::class, array(
                'label' => 'Kategorie dokumentu',
                'class' => DocumentCategory::class,
                'choice_label' => 'name',
            ))*/;
    }


    public function getDefault()
    {
        return array_merge(parent::getDefault(), $this->default);
    }


    public function getWidgetParameters($editor = false)
    {
        $parameters = parent::getWidgetParameters();

        if (preg_match('/\[(.+):([0-9]+)\]/', $parameters['document'], $matches))
        {
            $documentId = $matches[2];

            // najdeme dokument
            $document = $this->em->getRepository('CmsBundle:Document')->find($documentId);
            $parameters['document'] = $document;

            $systemWidget = $this->em->getRepository('CmsBundle:Document')->findSystemWidget($document);

            if ($systemWidget)
            {
                $systemWidgetparameters = $systemWidget->getParameters();
                if (!$parameters['title'] && isset($systemWidgetparameters['title']))
                {
                    $parameters['title'] = $systemWidgetparameters['title'];
                }

                if (!$parameters['annotation'] && isset($systemWidgetparameters['annotation']))
                {
                    $parameters['annotation'] = $systemWidgetparameters['annotation'];
                }

                if (!$parameters['image'] && isset($systemWidgetparameters['image']))
                {
                    $parameters['image'] = $systemWidgetparameters['image'];
                }

                if (!$parameters['date'] && isset($systemWidgetparameters['date']))
                {
                    $parameters['date'] = $systemWidgetparameters['date'];
                }
            }

            $parameters['link'] = '/';

            if ($document) {
                if (!$parameters['date']) {
                    $parameters['date'] = $document->getCreated('d. m. Y');
                }
                if (!$parameters['title']) {
                    $parameters['title'] = $document->getName();
                }
                if (!$parameters['annotation']) {
                    $parameters['annotation'] = $document->getAnnotation();
                }
                if (!$parameters['image']) {
                    $parameters['image'] = $document->getImage();
                }

                $parameters['link'] = '/' . $document->getUrl();
            }


        }

        return $parameters;
    }

}