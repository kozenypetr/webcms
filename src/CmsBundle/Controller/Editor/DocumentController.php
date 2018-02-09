<?php

namespace CmsBundle\Controller\Editor;



use CmsBundle\Form\WidgetType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
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
            ->add('metatitle', TextType::class, array('label' => 'Metatitle'))
            ->add('metakeywords', TextType::class, array('label' => 'Metakeywords'))
            ->add('metadescription', TextType::class, array('label' => 'Metadescription'))
            ->add('url', TextType::class, array('label' => 'URL'))
            ->add('status', ChoiceType::class,
                array(
                    'label' => 'Stav',
                    'choices'  => array(
                        'Neativní' => 0,
                        'Aktivní'  => 1,
                    )
                )
            )
            ->add('parent', EntityType::class, array(
                'label' => 'Nadřazená stránka',
                'class' => Document::class,
                'query_builder' => function (DocumentRepository $er) {
                    return $er->createQueryBuilder('d')
                        ->innerJoin('d.category', 'c', 'WITH', 'c.isEnableAsParent = 1')
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
            'form' => $form->createView(),
            'action' => $this->generateUrl('cms_document_add')
        ));
    }


    /**
     * Editace dokumentu
     * @Route("/edit/{id}", name="cms_document_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Document $document)
    {
        $formBuilder = $this->createFormBuilder($document)
            ->add('name', TextType::class, array('label' => 'Název'))
            ->add('metatitle', TextType::class, array('label' => 'Metatitle'))
            ->add('metakeywords', TextType::class, array('label' => 'Metakeywords'))
            ->add('metadescription', TextType::class, array('label' => 'Metadescription'));


        if ($document->getUrl())
        {
            $formBuilder
                ->add('parent', EntityType::class, array(
                    'label' => 'Nadřazená stránka',
                    'class' => Document::class,
                    'query_builder' => function (DocumentRepository $er) use ($document) {
                        return $er->createQueryBuilder('d')
                            ->orderBy('d.lft', 'ASC');
                    },
                    'choice_label' => 'treeName',
                    'choice_attr' => function($val, $key, $index) use ($document) {
                        // adds a class like attending_yes, attending_no, etc
                        return ($document->getId() == $val->getId())?['disabled' => 'disabled']:[];
                    }
                ))
                ->add('url', TextType::class, array('label' => 'URL'))
                ->add('status', ChoiceType::class,
                    array(
                        'label' => 'Stav',
                        'choices'  => array(
                            'Neativní' => 0,
                            'Aktivní'  => 1,
                        )
                    )
                )
                ->add('category', EntityType::class, array(
                    'label' => 'Kategorie dokumentu',
                    'class' => DocumentCategory::class,
                    'choice_label' => 'name',
                ));
        }
        else
        {
            $formBuilder
                ->add('parent', HiddenType::class)
                ->add('url', TextType::class, array('label' => 'URL', 'disabled' => 'disabled'))
                ->add('status', TextType::class, array('label' => 'Stav', 'disabled' => 'disabled'))
                ->add('category', EntityType::class, array(
                    'label' => 'Kategorie dokumentu',
                    'class' => DocumentCategory::class,
                    'choice_label' => 'name',
                    'disabled' => 'disabled'
                ));
        }

        $form  = $formBuilder->getForm();

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
            'form' => $form->createView(),
            'document' => $document,
            'action' => $this->generateUrl('cms_document_edit',
                        array('id' => $document->getId()))
        ));
    }

    /**
     * Seznam dokumentu
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listAction()
    {
        $em = $this->getDoctrine()->getManager();

        $repo = $em->getRepository('CmsBundle:Document');

        $documents = $repo->children(null, false, 'lft');

            /*$em->getRepository('CmsBundle:Document')->childrenHierarchy(null, false, array(
            'decorate' => false,
            'representationField' => 'url',
            'html' => false
        ));*/
        $options = array(
            'decorate' => true,
            'rootOpen' => '<ul>',
            'rootClose' => '</ul>',
            'childOpen' => '<li>',
            'childClose' => '</li>',
            'nodeDecorator' => function($node) {
                return '<a href="/'.$node['url'].'">' . $node['name'] . '</a>';
            }
        );

        $htmlTree = $repo->childrenHierarchy(
            null, /* starting from root nodes */
            false, /* false: load all children, true: only direct */
            $options
        );


        $hierarchy = $repo->childrenHierarchy(null, false, array());

        return $this->render('CmsBundle:Backend/Document:list.html.twig', array(
            'documents' => $documents,
            'htmlTree' => $htmlTree,
            'hierarchy' => $hierarchy
        ));
    }
}
