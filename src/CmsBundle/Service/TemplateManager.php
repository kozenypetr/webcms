<?php

namespace CmsBundle\Service;

use CmsBundle\Entity\Widget;

use Symfony\Component\DependencyInjection\ContainerInterface;

class TemplateManager {

    private $em;

    private $container;

    private $host;

    private $templateDir;

    private $widgetFiles = [];

    private $defaultTemplate;

    const DEFAULT_TEMPLATE_DIR = 'default';

    const TEMPLATE_WIDGET_PATH = 'CmsBundle:Templates/%s/Widget:';

    const TEMPLATE_LINK_PATH = 'CmsBundle:Templates/%s/Link:';

    const TEMPLATE_DOCUMENT_PATH = 'CmsBundle:Templates/%s/Document:';

    const TEMPLATE_REGION_PATH = 'CmsBundle:Templates/%s/Region:';

    const DEFAULT_REGION_FILE = 'region.html.twig';

    public function __construct($em, ContainerInterface $container) {
        $this->em = $em;

        $this->container = $container;

        $this->host = $this->container->get('cms.manager.domain')->getHost();

        $this->templateDir = realpath($this->container->get('kernel')
             ->getRootDir() . '/../src/CmsBundle/Resources/views/Templates') . '/';


        $this->widgetFiles['default'] = array_slice(scandir($this->templateDir . self::DEFAULT_TEMPLATE_DIR . '/Widget'), 2);
        $this->widgetFiles['host'] = [];
        if (is_dir($this->templateDir . $this->host . '/Widget')) {
            $this->widgetFiles['host'] = array_slice(scandir($this->templateDir . $this->host . '/Widget'), 2);
        }
    }

    /**
     * @param string $defaultTemplate
     * @param \CmsBundle\Entity\Widget $widget
     *
     * @return string
     */
    public function getWidgetTemplate(string $defaultTemplate, Widget $widget) {
        $templates = $widget->getSuggestionTemplateFiles($defaultTemplate);

        foreach ($templates as $key => $file) {
            foreach ([$this->host, self::DEFAULT_TEMPLATE_DIR] as $dir) {
                if (file_exists($this->templateDir . $dir . '/Widget/' . $file)) {
                    return sprintf(self::TEMPLATE_WIDGET_PATH, $dir) . $file;
                }
            }
        }

        // @TODO: vyjimka, ze neexistuje zadna sablona
    }

    /**
     * @param string $defaultTemplate
     * @param \CmsBundle\Entity\Widget $widget
     *
     * @return string
     */
    public function getWidgetLinkTemplate(string $defaultTemplate, Widget $widget) {
        $templates = $widget->getSuggestionTemplateFiles($defaultTemplate);

        foreach ($templates as $key => $file) {
            foreach ([$this->host, self::DEFAULT_TEMPLATE_DIR] as $dir) {
                if (file_exists($this->templateDir . $dir . '/Link/' . $file)) {
                    return sprintf(self::TEMPLATE_LINK_PATH, $dir) . $file;
                }
            }
        }

        // @TODO: vyjimka, ze neexistuje zadna sablona
    }

    /**
     * @param $region
     * @return string
     */
    public function getRegionTemplate($region)
    {
        $templates = [];
        if ($region && $region->getSid())
        {
            $templates[] = str_replace(".html.twig", "-{$region->getSid()}.html.twig", self::DEFAULT_REGION_FILE);
        }

        $templates[] = self::DEFAULT_REGION_FILE;
        foreach ($templates as $key => $file) {
            foreach ([$this->host, self::DEFAULT_TEMPLATE_DIR] as $dir) {
                if (file_exists($this->templateDir . $dir . '/Region/' . $file)) {
                    return sprintf(self::TEMPLATE_REGION_PATH, $dir) . $file;
                }
            }
        }
    }

    /**
     * Načtení aktuálních šablon pro dokumenty
     * @return array
     */
    public function getDocumentTemplates()
    {
        $domainManager = $this->container->get('cms.manager.domain');

        $defaultTemplate = $domainManager->getParameter('default_template', 'main.html.twig');

        $domainTemplatesList = $domainManager->getParameter('templates');

        $dir = $this->templateDir . self::DEFAULT_TEMPLATE_DIR . '/Document';
        $path = sprintf(self::TEMPLATE_DOCUMENT_PATH, self::DEFAULT_TEMPLATE_DIR );

        if (is_dir($this->templateDir . $this->host . '/Document'))
        {
            $dir = $this->templateDir . $this->host . '/Document';
            $path = sprintf(self::TEMPLATE_DOCUMENT_PATH, $this->host  );
        }

        $templates = [];

        foreach (array_slice(scandir($dir), 2) as $template)
        {
            if (isset($domainTemplatesList[$template]))
            {
                $templates[$domainTemplatesList[$template]] = $path . $template;

                if ($template == $defaultTemplate) {
                    $this->defaultTemplate = $path . $template;
                }
            }
        }

        return $templates;
    }


    public function getDefaultTemplate()
    {
        return $this->defaultTemplate;
    }
}