<?php

namespace CmsBundle\Twig\Globals;

use CmsBundle\Service\WidgetManager;

class Widgets
{
    private $widgetManager;

    private $widgets;

    public function __construct(WidgetManager $widgetManager)
    {
        $this->widgetManager = $widgetManager;

        $this->widgets = $widgetManager->getWidgets();
    }

    public function list()
    {
        return $this->widgets;
    }
}