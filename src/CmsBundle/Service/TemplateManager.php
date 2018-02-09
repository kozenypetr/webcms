<?php

namespace CmsBundle\Service;

class TemplateManager
{
    private $em;

    private $widgets;

    private $container;

    public function __construct($em, $container)
    {
        $this->em = $em;
        $this->container = $container;
    }
}