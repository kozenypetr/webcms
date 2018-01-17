<?php

namespace CmsBundle\Controller\Backend;


use CmsBundle\Entity\Document;
use CmsBundle\Form\WidgetType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

use Symfony\Component\HttpFoundation\JsonResponse;

use CmsBundle\Entity\Widget;
use CmsBundle\Entity\Box;
use CmsBundle\Form\BoxType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/cms/document")
 */
class DocumentController extends Controller
{
    /**
     * Vytvoreni dokumentu
     * @Route("/add", name="cms_document_add")
     * @Method({"GET", "POST"})
     */
    public function addAction(Request $request)
    {
        $document = new Document();

        $form = $this->createFormBuilder($document)
            ->add('name', TextType::class)
            ->add('url', TextType::class)
            ->getForm();

        $form->handleRequest($request);

        return $this->render('CmsBundle:Backend/Document:document.html.twig', array(
            'form' => $form->createView()
        ));
    }
}
