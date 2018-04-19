<?php

namespace CmsBundle\Controller\Editor;



use CmsBundle\Form\WidgetType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;


use Symfony\Component\HttpFoundation\JsonResponse;

use CmsBundle\Entity\Document;
use CmsBundle\Entity\DocumentCategory;

use CmsBundle\Repository\DocumentRepository;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;

use Symfony\Component\Finder\Finder;

/**
 * @Route("/cms/editor")
 */
class EditorController extends Controller
{
    /**
     * Seznam widgetu do panelu
     * @return Response
     */
    public function widgetPanelListAction()
    {
        return $this->render('CmsBundle:Editor/Panel:widgetsPanel.html.twig', array(
            'widgets' => $this->get('cms.manager.widget')->getWidgets()
        ));
    }

    /**
     * Seznam widgetu
     * @return Response
     */
    public function widgetListAction()
    {
        return $this->render('CmsBundle:Editor/Panel:widgets.html.twig', array(
            'widgets' => $this->get('cms.manager.widget')->getWidgets()
        ));
    }
}
