<?php

namespace CmsBundle\Service\Widget;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

use Symfony\Component\Validator\Constraints as Assert;

use CmsBundle\Entity\Widget;

abstract class Base
{
    protected $em;

    protected $formFactory;

    protected $parameters;

    protected $type = 'Base'; // nutne prepsat v nastaveni konkretniho widgetu

    protected $title = 'Widget';

    protected $template;

    public function __construct($em, $formFactory)
    {
        $this->em = $em;
        $this->formFactory = $formFactory;
    }


    /**
     * Vytvoreni formulare pro editaci widgetu
     * @param array $options Parametry formulare
     * @return mixed
     */
    public function createForm($options = array())
    {
        $form = $this->formFactory->createBuilder(FormType::class, $this->getParameters(), $options);

        $form = $this->baseConfigureForm($form);

        $form = $this->configureForm($form);

        return $form->getForm();
    }

    protected function baseConfigureForm($form)
    {
        return $form
            ->add('class_md', ChoiceType::class, array(
                'label'    => 'Počítač',
                'required' => true,
                'choices'  => $this->getBootstrapClasses('col-md-'),
            ))
            ->add('class_sm', TextType::class, array('label' => 'Tablet'))
            ->add('class_xs', TextType::class, array('label' => 'Mobil'))
            ->add('class_lg', TextType::class, array('label' => 'Velký displej'));
    }

    protected function getBootstrapClasses($prefix)
    {
        $classes = array();
        for ($i = 1; $i <= 12; $i++)
        {
            $classes[$i . '/12'] = $prefix . $i;
        }

        $classes['Skrytý'] = 'hidden-' . $prefix;

        return $classes;
    }

    protected function getParameters()
    {
        return $this->parameters;
    }




    public function setParameters(array $parameters)
    {
        $this->parameters = $parameters;

        return $this;
    }

    public function getTemplate()
    {
        // $class = strtolower(get_class($this));

        // $template = 'CmsBundle:Widget:editor.html.twig';

        return $this->template;
    }

    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @return array
     */
    public function getDefault()
    {
        return array(
            'class_md' => 'col-md-12',
            'class_sm' => '',
            'class_xs' => '',
            'class_lg' => ''
        );
    }

    /**
     * Nastaveni formulare zdedenym widgetem
     * @param $form
     * @return mixed
     */
    abstract protected function configureForm($form);
}