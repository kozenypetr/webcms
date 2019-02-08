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
        $this->groups = array();
    }

    public function addWidget($key, $widget)
    {
        $this->widgets[$key] = $widget;
        $this->groups[$widget->getGroup()][] = $key;
    }

    public function getWidgets()
    {
        return $this->widgets;
    }

    public function getWidgetsByGroup()
    {
        $widgets = array();
        foreach ($this->groups as $name => $ws)
        {
            foreach ($ws as $key) {
                $widgets[$name][$key] = $this->widgets[$key];
            }
        }
        return $widgets;
    }

    public function getWidget($key)
    {
        return $this->widgets[$key];
    }
}