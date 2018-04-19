<?php

namespace CmsBundle\Controller\Frontend;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;


class DocumentController extends Controller
{
    /**
     * @Route("/", name="cms_homepage", defaults={"url": ""},)
     * @Route("/{url}", name="cms_page",
     *     requirements={"url" = ".*"})
     */
    public function showAction($url)
    {
        $document = $this->getDoctrine()
                ->getRepository('CmsBundle:Document')
                ->findOneByUrl($url);

        if (!$document) {
            throw $this->createNotFoundException('Dokument neexistuje');
        }

        $this->get('cms.manager.content')->setDocument($document);

        return $this->render('CmsBundle:Editor:page.base.html.twig', array(
            'document' => $document,
        ));
    }
}
