<?php

namespace CmsBundle\Service\Widget;

use CmsBundle\Service\Widget\Base;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;


use Symfony\Component\Validator\Constraints as Assert;

class Menu extends Base
{

  protected $type = 'Menu';

  protected $title = 'Menu';

  protected $template = 'menu.html.twig';

  protected $editorTemplate = 'widgetMenu.html.twig';

  protected $icon = 'fa-bars';

    protected $default = array(
        'title' => '',
        'tree' => array()
    );

    protected function configureForm($form)
    {
        return $form
            ->add('title', TextType::class, array(
                'label'=>'Název'
            ))
            ->add('tree', HiddenType::class, array(
            ));
    }

    public function getDefault()
    {
        return array_merge(parent::getDefault(), $this->default);
    }

    public function getWidgetParameters($editor = false)
    {
        $parameters = parent::getWidgetParameters();

        $parameters['tree'] = json_decode($parameters['tree'], true);


        if (!$editor && $parameters['tree'])
        {
            $parameters['tree'] = $this->prepareTree($parameters['tree']);
        }

        return $parameters;
    }

    /**
     * Příprava stromu pro zobrazení
     * @param $tree
     * @return mixed
     */
    protected function prepareTree($tree)
    {
        foreach ($tree as $key => $item)
        {
            if (preg_match('/\[(.+):([0-9]+)\]/', $item['link'], $matches))
            {
                $documentId = $matches[2];
                // najdeme dokument
                $document = $this->em->getRepository('CmsBundle:Document')->find($documentId);
                $tree[$key]['link'] = $document->getUrl();
            }
            elseif (empty($tree[$key]['link']))
            {
                $tree[$key]['link'] = null;
            }

            if ($item['children'])
            {
                $tree[$key]['children'] = $this->prepareTree($tree[$key]['children']);
            }
        }

        return $tree;
    }


    /**
     * Ulozeni stromu
     * @param array $parameters
     * @return array
     */
    public function processParameters($parameters) {

        $tree = json_decode($parameters['tree'], true);

        $parameters['tree'] = json_encode($this->readMenu($tree[0]['children']));

        return $parameters;
    }


    /**
     * Rekurzivni funkce pro cteni stromu
     * @param $children
     * @return array
     */
    protected function readMenu($children)
    {
        $menu = array();
        foreach ($children as $child)
        {
            $menu[] = array(
                'title' => $child['text'],
                'link'  => (isset($child['li_attr']['cmslink']))?$child['li_attr']['cmslink']:null,
                'class' => (isset($child['li_attr']['cmsclass']))?$child['li_attr']['cmsclass']:null,
                'children' => $this->readMenu($child['children'])
            );
        }

        return $menu;
    }
}