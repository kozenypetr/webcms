<?php

namespace CmsBundle\Controller\Backend;



use CmsBundle\Form\WidgetType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;


use Symfony\Component\HttpFoundation\JsonResponse;

use CmsBundle\Entity\Document;
use CmsBundle\Entity\DocumentCategory;

use CmsBundle\Repository\DocumentRepository;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

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
            ->add('name', TextType::class, array('label' => 'Název'))
            ->add('url', TextType::class, array('label' => 'URL'))
            ->add('parent', EntityType::class, array(
                'label' => 'Nadřazená stránka',
                'class' => Document::class,
                'query_builder' => function (DocumentRepository $er) {
                    return $er->createQueryBuilder('d')
                        ->orderBy('d.lft', 'ASC');
                },
                'choice_label' => 'treeName',
            ))
            ->add('category', EntityType::class, array(
                'label' => 'Kategorie dokumentu',
                'class' => DocumentCategory::class,
                'choice_label' => 'name',
            ))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // $form->getData() holds the submitted values
            // but, the original `$task` variable has also been updated
            $document = $form->getData();

            $em = $this->getDoctrine()->getManager();
            $em->persist($document);
            $em->flush();

            return new JsonResponse(array('url' => $document->getUrl()));
        }

        return $this->render('CmsBundle:Backend/Document:document.html.twig', array(
            'form' => $form->createView()
        ));
    }

    public function listAction()
    {
        $em = $this->getDoctrine()->getManager();

        $documents = $em->getRepository('CmsBundle:Document')->findAll();

        return $this->render('CmsBundle:Backend/Document:list.html.twig', array(
            'documents' => $documents
        ));
    }
}
