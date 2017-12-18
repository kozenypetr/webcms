<?php

namespace CmsBundle\Service;

class WidgetManager
{
    private $em;

    private $widgets;

    private $container;

    public function __construct($em, $container)
    {
        $this->em = $em;
        $this->container = $container;
        $this->widgets = array();
    }

    public function addWidget($key, $widget)
    {
        $this->widgets[$key] = $widget;
    }

    public function getWidgets()
    {
        return $this->widgets;
    }

    public function getWidget($key)
    {
        return $this->widgets[$key];
    }
}