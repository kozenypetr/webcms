<?php

namespace AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sonata\AdminBundle\Admin\AdminInterface;

class DefaultController extends Controller
{
    /**
     * @Route("/filemanager", name="cms_admin_filemanager")
     */
    public function filemanagerAction()
    {
        return $this->render('AdminBundle:Default:filemanager.html.twig');
    }


    public function dashboardAction()
    {
        $em = $this->getDoctrine()->getManager();

        $repo = $em->getRepository('CmsBundle:Document');

        $em = $this->getDoctrine()->getManager();

        $repo = $em->getRepository('CmsBundle:Document');

        $hierarchy = $repo->childrenHierarchy(null, false, array());

        return $this->render('AdminBundle:Default:dashboard.html.twig', array('hierarchy' => $hierarchy));
    }
}
