<?php

namespace CmsBundle\Service\Widget;

use CmsBundle\Service\Widget\Base;
use Symfony\Component\Form\Extension\Core\Type\TextType;

use Symfony\Component\Validator\Constraints as Assert;

class Breadcrumb extends Base
{

    protected $type = 'Dobečková navigace';

    protected $title = 'Dobečková navigace';

    protected $template = 'breadcrumb.html.twig';

    protected $icon = 'fa-list-ol';

    protected $predefinedClasses = array(

    );

    protected $default = array(
        'name' => "",
    );

    protected function configureForm($form)
    {
        return $form
            ->add('name', TextType::class, array(
                'label'=>'Název'
            ))
            ;
    }

    public function getWidgetParameters($editor = false)
    {
        $parameters = parent::getWidgetParameters();

        $document = $this->cm->getDocument();

        $path = $document->getPath();

        $parameters['path'] = $path;

        $parameters['document'] = $document;

        return $parameters;
    }



    public function getDefault()
    {
        return array_merge(parent::getDefault(), $this->default);
    }
}