<?php

namespace CmsBundle\Service\Widget;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;


use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bundle\TwigBundle\TwigEngine;
use CmsBundle\Service\TemplateManager;
use CmsBundle\Service\WidgetManager;
use CmsBundle\Service\ContentManager;



use Doctrine\ORM\EntityManagerInterface;

use Symfony\Component\Form\FormFactoryInterface;

use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;

use CmsBundle\Entity\Widget;

abstract class Base
{
    protected $em;

    protected $tm;

    protected $wm;

    protected $cm;

    protected $formFactory;

    protected $parameters;

    protected $type = 'Base'; // nutne prepsat v nastaveni konkretniho widgetu

    protected $title = 'Widget';

    protected $template;

    protected $entity;

    protected $basePredefinedClasses = ['Bílý box' => 'white-box', 'Horní odsazení' => 'indentation', 'Bez horního odsazení' => 'noindentation'];

    protected $predefinedClasses = [];

    protected $hasItems = false;

    protected $editorTemplate = 'widget.html.twig';

    protected $icon = 'fa-folder-o';

    protected $group = 'Základní';

    public function __construct(EntityManagerInterface $em, FormFactoryInterface $formFactory, EngineInterface $twig, TemplateManager $tm, WidgetManager $wm, ContentManager $cm)
    {
        $this->em = $em;
        $this->tm = $tm;
        $this->wm = $wm;
        $this->cm = $cm;

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
        $baseWidgetTemplate = 'CmsBundle:Templates/base:widget.base.' . ($plain?'plain.':'') . 'html.twig';

        $template = $this->tm->getWidgetTemplate($this->getTemplate(), $this->entity);

        return $this->twig->render($baseWidgetTemplate,
            array(
                'widget' => $this->entity,
                'parameters' => $this->getWidgetParameters(),
                'template' => $template,
                'title' => $this->getTitle(),
                'images' => $this->entity->getImages()
            )
        );
    }

    public function getLinkHtml($document)
    {
        $template = $this->tm->getWidgetLinkTemplate($this->getTemplate(), $this->entity);

        return $this->twig->render($template,
            array(
                'widget' => $this->entity,
                'document' => $document,
                'parameters' => $this->getWidgetParameters(),
            )
        );
    }

    public function createItemForm($item, $options)
    {
        $form = $this->formFactory->createBuilder(FormType::class, $item, $options);

        $form = $this->baseConfigureItemForm($form);

        $form = $this->configureItemForm($form);

        return $form->getForm();
    }

    protected function baseConfigureItemForm($form)
    {
        return $form
            ->add('name', TextType::class, array('label' => 'Název'));
    }

    /**
     * Vytvoreni formulare pro editaci widgetu
     * @param array $options Parametry formulare
     * @return mixed
     */
    public function createForm($options = array())
    {
        $form = $this->formFactory->createBuilder(FormType::class, $this->getEditParameters(), $options);

        $form = $this->configureForm($form);

        $form = $this->baseConfigureForm($form);

        return $form->getForm();
    }

    protected function baseConfigureForm($form)
    {
        return $form
            ->add('sid', TextType::class, array('label' => 'Strojový název'))
            ->add('id', TextType::class, array('label' => 'ID'))

            ->add('is_system', CheckboxType::class, array('label' => 'Systémový'))

            ->add('predefined_class', ChoiceType::class, array(
                'label' => 'Předdefinovaná třída',
                'choices'  => $this->getPredefinedClasses(),
            ))
            ->add('class', TextType::class, array('label' => 'Třída'))
            ->add('class_md', ChoiceType::class, array(
                'label'    => 'Šířka',
                'required' => true,
                'choices'  => $this->getBootstrapClasses('col-md-', true),
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
            ))
            ->add('tag', TextType::class, array(
                'label' => 'Tag',
            ))
            ->add('subclass', TextType::class, array(
                'label' => 'Podtřída',
            ));
    }

    protected function getPredefinedClasses()
    {
        return array_merge(['Nevybráno' => ''], $this->basePredefinedClasses, $this->predefinedClasses);
    }

    protected function getBootstrapClasses($prefix, $blank = false)
    {
        $classes = array();
        if ($blank)
        {
            $classes['Automaticky'] = '';
        }
        for ($i = 1; $i <= 12; $i++)
        {
            $classes[$i] = $prefix . $i;
        }

        $classes['Skrytý'] = 'hidden-' . $prefix;

        return $classes;
    }

    public function getWidgetParameters($editor = false)
    {
        $parameters = $this->entity->getParameters();

        foreach ($this->getDefault() as $key => $value)
        {
            if (!isset($parameters[$key]))
            {
                $parameters[$key] = $value;
            }
        }

        return $parameters;
    }

    public function getTemplate()
    {
        return $this->template;
    }

    public function getEditorTemplate()
    {
        return $this->editorTemplate;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function getIcon()
    {
        return $this->icon;
    }


    public function getEditParameters()
    {
        return $this->getParameters();
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
        return $parameters;
    }

    /**
     * @return array
     */
    public function getDefault()
    {
        return array(
            'id'       => '',
            'class'    => '',
            'class_md' => 'col-md-12',
            'class_sm' => '',
            'class_xs' => '',
            'class_lg' => '',
            'tag'      => 'div',
            'subclass' => ''
        );
    }

    public function getParameters()
    {
        return $this->parameters;
    }


    public function setParameters($parameters)
    {
        $this->parameters = $parameters;

        return $this;
    }

    /**
     * Nastaveni formulare zdedenym widgetem
     * @param $form
     * @return mixed
     */
    abstract protected function configureForm($form);

    protected function configureItemForm($form)
    {
        return $form;
    }
}