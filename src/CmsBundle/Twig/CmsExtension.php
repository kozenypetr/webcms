<?php
/**
 * Created by PhpStorm.
 * User: petr
 * Date: 10.8.17
 * Time: 8:31
 */

namespace CmsBundle\Twig;

use CmsBundle\Entity\Box;
use CmsBundle\Entity\Document;
use CmsBundle\Entity\Widget;
use Doctrine\ORM\EntityManager;
use CmsBundle\Service\WidgetManager;

class CmsExtension extends \Twig_Extension
{
    private $em;
    private $wm;

    public function __construct(EntityManager $em, WidgetManager $wm)
    {
        $this->em = $em;
        $this->wm = $wm;
    }

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('cms_region', array($this, 'cmsRegionFunction'), array(
                'needs_environment' => true,
                'is_safe'=> array('html')
            )),
            new \Twig_SimpleFunction('cms_box', array($this, 'cmsBoxFunction'), array(
                'needs_environment' => true,
                'is_safe'=> array('html')
            )),
            new \Twig_SimpleFunction('cms_widget', array($this, 'cmsWidgetFunction'), array(
                'needs_environment' => true,
                'is_safe'=> array('html')
            )),
        );
    }

    /**
     * @param \Twig_Environment $twig
     * @param Document $document
     * @param $name
     * @param string $type
     * @return string HTMl regionu
     */
    public function cmsRegionFunction(\Twig_Environment $twig, Document $document, $name, $type = 'page')
    {
        // return $page->getName() . ' - ' . $page->getId();
        if ($type == 'global')
        {
            // budeme hleda radky podle nazvu regionu
            $widgets = $this->em->getRepository('CmsBundle:Widget')->findByRegion($name);
        }
        else
        {
            // budeme hledat radky pro stranku
            $widgets = $document->getRegionWidgets($name);
        }

        return $twig->render('CmsBundle:Frontend/Document:region.html.twig', array('name' => $name, 'type' => $type, 'document' => $document, 'widgets' => $widgets));
    }


    public function cmsWidgetFunction(\Twig_Environment $twig, Widget $widget)
    {
        $service = $this->wm->getWidget($widget->getService());

        return $twig->render($service->getTemplate(), array('widget' => $widget));
    }

}