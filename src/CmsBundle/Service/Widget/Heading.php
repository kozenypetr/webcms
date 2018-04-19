<?php

namespace CmsBundle\Service\Widget;

use CmsBundle\Service\Widget\Base;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

use Symfony\Component\Validator\Constraints as Assert;

class Heading extends Base
{

    protected $type = 'Nadpis';

    protected $title = 'Nadpis';

    protected $template = 'heading.html.twig';

    protected $icon = 'fa-header';

    protected $predefinedClasses = array(

    );

    protected $default = array(
        'title' => "Nadpis",
        'title2' => 'Nadpis 2',
        'level' => 'h1'
    );

    protected function configureForm($form)
    {
        return $form
            ->add('title', TextType::class, array(
                'label' => 'Nadpis'
            ))
            ->add('title2', TextType::class, array(
                'label' => 'Nadpis 2'
            ))
            ->add('level', ChoiceType::class, array(
                'label' => 'Úroveň nadpisu',
                'choices'  => array('h1' => 'h1', 'h2' => 'h2', 'h3' => 'h3', 'h4' => 'h4', 'h5' => 'h5', 'h6' => 'h6'),
            ))
            ;
    }


    public function getDefault()
    {
        return array_merge(parent::getDefault(), $this->default);
    }
}