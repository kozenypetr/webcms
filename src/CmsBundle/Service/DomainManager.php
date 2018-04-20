<?php

namespace CmsBundle\Service;

class DomainManager
{
    private $em;

    private $container;

    private $host = 'default';

    private $domainSetting;

    public function __construct($em, $container)
    {
        $this->em = $em;
        $this->container = $container;


        if ($this->container->getParameter('rewriteHost'))
        {
            $this->host = $this->container->getParameter('rewriteHost');
        }
        elseif ($this->container->get('request_stack')->getCurrentRequest())
        {
            $this->host = $this->container->get('request_stack')
                ->getCurrentRequest()
                ->getHost();
        }

        $domainSettings = $this->container->getParameter('domains');

        if (isset($domainSettings[$this->getHost()]))
        {
            $this->domainSetting = $domainSettings[$this->getHost()];
        }
    }

    public function getParameter($key, $default = null)
    {
        $value = $default;
        if (isset($this->domainSetting[$key]))
        {
            $value = $this->domainSetting[$key];
        }
        return $value;
    }

    public function getHost()
    {
        return $this->host;
    }

}