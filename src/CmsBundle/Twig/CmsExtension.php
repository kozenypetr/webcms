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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

class CmsExtension extends \Twig_Extension
{
    private $em;
    private $wm;
    private $securityContext;
    private $requestStack;

    public function __construct(EntityManager $em, WidgetManager $wm, RequestStack $requestStack, $securityContext)
    {
        $this->em = $em;
        $this->wm = $wm;
        $this->securityContext = $securityContext;
        $this->requestStack = $requestStack;
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
            new \Twig_SimpleFunction('cms_body_class', array($this, 'cmsBodyClass'), array(
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
    public function cmsBodyClass(\Twig_Environment $twig)
    {
        if (!$this->securityContext->isGranted('ROLE_ADMIN'))
        {
            return '';
        }

        $classes = [];
        if ($this->requestStack->getCurrentRequest()->cookies->get('cms_editmod') == 'true')
        {
            $classes[] = 'edit-mode';
        }

        if ($this->requestStack->getCurrentRequest()->cookies->get('cms_editmod_region') == 'true')
        {
            $classes[] = 'edit-mode-region';
        }

        return join(' ', $classes);
    }


    /**
     * @param \Twig_Environment $twig
     * @param Document $document
     * @param $name
     * @param string $type
     * @return string HTMl regionu
     */
    public function cmsRegionFunction(\Twig_Environment $twig, Document $document, $name)
    {
        if (preg_match('/^global\./', $name))
        {
            // budeme hleda radky podle nazvu regionu
            $widgets = $this->em->getRepository('CmsBundle:Widget')->findByRegion($name, array('sort' => 'ASC'));
            $region  = $this->em->getRepository('CmsBundle:Region')->findOneByName($name);
        }
        else
        {
            // budeme hledat radky pro stranku
            $widgets = $document->getRegionWidgets($name);
            $region  = $this->em->getRepository('CmsBundle:Region')->findOneBy(['document' => $document, 'name' => $name]);
        }

        return $twig->render('CmsBundle:Templates/default/Region:region.html.twig',
                             array('name' => $name,
                                   'id' => 'region-' . str_replace('.', '-', $name),
                                   'class' => $region?$region->getFullClass():'col-md-12',
                                   'document' => $document,
                                   'region'   => $region,
                                   'widgets'  => $widgets)
        );

    }


    public function cmsWidgetFunction(\Twig_Environment $twig, Widget $widget)
    {
        $service = $this->wm->getWidget($widget->getService());

        return $service->setEntity($widget)->getHtml();
    }

}