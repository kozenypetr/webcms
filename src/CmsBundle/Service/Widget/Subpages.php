<?php

namespace CmsBundle\Service\Widget;

use CmsBundle\Service\Widget\Base;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

use CmsBundle\Entity\DocumentCategory;


use Symfony\Component\Validator\Constraints as Assert;

class Subpages extends Base
{

    protected $type = 'Podstránky';

    protected $title = 'Podstránky';

    protected $template = 'subpages.html.twig';

    protected $icon = 'fa-list-alt';

    protected $predefinedClasses = array(

    );

    protected $default = array(
        'document' => '',
        'limit'    => 3

    );

    protected function configureForm($form)
    {
        return $form
            ->add('document', TextType::class, ['label' => 'Stránka', 'attr' => ['class' => 'droplink']])
            ->add('limit', TextType::class)
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

            $children = $this->em->getRepository('CmsBundle:Document')->getChildrenWithSystemWidget($document, $parameters['limit']?$parameters['limit']:3);

            $links = array();

            foreach ($children as $document)
            {
                $widget = $document->getFirstWidget();

                if ($widget)
                {
                    $service = $this->wm->getWidget($widget->getService());

                    $links[] = $service->setEntity($widget)->getLinkHtml($document);
                }
            }

            $parameters['links'] = $links;
        }

        return $parameters;
    }

}