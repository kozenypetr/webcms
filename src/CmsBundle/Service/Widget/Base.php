<?php

namespace CmsBundle\Service\Widget;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bundle\TwigBundle\TwigEngine;

use Doctrine\ORM\EntityManagerInterface;

use Symfony\Component\Form\FormFactoryInterface;

use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;

use CmsBundle\Entity\Widget;

abstract class Base
{
    protected $em;

    protected $formFactory;

    protected $parameters;

    protected $type = 'Base'; // nutne prepsat v nastaveni konkretniho widgetu

    protected $title = 'Widget';

    protected $template;

    protected $entity;

    public function __construct(EntityManagerInterface $em, FormFactoryInterface $formFactory, EngineInterface $twig)
    {
        $this->em = $em;
        $this->formFactory = $formFactory;
        $this->twig = $twig;
    }

    public function setEntity(Widget $widget)
    {
        $this->entity = $widget;

        return $this;
    }

    public function getHtml($plain = false)
    {
        $baseWidgetTemplate = 'CmsBundle:Templates/default/Widget:widget.base.' . ($plain?'plain.':'') . 'html.twig';

        $templateDir = 'CmsBundle:Templates/default/Widget:';

        return $this->twig->render($baseWidgetTemplate,
            array(
                'widget' => $this->entity,
                'template' => $templateDir . $this->getTemplate(),
                'title' => $this->getTitle()
            )
        );
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
            ->add('sid', TextType::class, array('label' => 'Strojový název'))
            ->add('class', TextType::class, array('label' => 'Třída'))
            ->add('class_md', ChoiceType::class, array(
                'label'    => 'Šířka',
                'required' => true,
                'choices'  => $this->getBootstrapClasses('col-md-'),
            ))
            ->add('class_sm', ChoiceType::class, array(
                 'label' => 'Tablet',
                 'choices'  => $this->getBootstrapClasses('col-sm-', true),
            ))
            ->add('class_xs', ChoiceType::class, array(
                 'label' => 'Mobil',
                 'choices'  => $this->getBootstrapClasses('col-xs-', true),
            ))
            ->add('class_lg', ChoiceType::class, array(
                  'label' => 'Velký displej',
                  'choices'  => $this->getBootstrapClasses('col-lg-', true),
            ));
    }

    protected function getBootstrapClasses($prefix, $blank = false)
    {
        $classes = array();
        if ($blank)
        {
            $classes['Nevybráno'] = '';
        }
        for ($i = 1; $i <= 12; $i++)
        {
            $classes[$i] = $prefix . $i;
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
            'class'    => '',
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