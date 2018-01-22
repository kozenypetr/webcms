<?php

namespace CmsBundle\Controller;

use CmsBundle\Entity\Document;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use CmsBundle\Entity\Page;

class DefaultController extends Controller
{
    /**
     * @Route("/cms")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $page = new Document();
        $page->setName('Homepage');
        $page->setUrl('');

        $em->persist($page);

        $em->flush();

        return $this->render('CmsBundle:Default:index.html.twig', array('page' => $page));
    }
}
