<?php

namespace CmsBundle\Controller\Editor;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

use Symfony\Component\HttpFoundation\JsonResponse;

use CmsBundle\Entity\Widget;
use CmsBundle\Entity\Document;
use CmsBundle\Entity\Region;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;


/**
 * @Route("/cms/region")
 */
class RegionController extends Controller
{

    /**
     * Editace regionu
     * @Route("/edit/{id}/{name}", name="cms_region_edit")
     * @Route("/edit/{name}", name="cms_region_edit_global")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Document $document, $name)
    {
        $findOneBy = ['name' => $name];

        if (preg_match('/^page\./', $name))
        {
            $findOneBy['document'] = $document;
        }

        $region = $this->getDoctrine()
                ->getRepository(Region::class)
                ->findOneBy($findOneBy);

        if (!$region)
        {
            $region = new Region();
        }
        $region->setName($name);

        if (preg_match('/^page\./', $name))
        {
            $region->setDocument($document);
        }

        $form = $this->createFormBuilder($region)
                     ->add('name', HiddenType::class)
                     ->add('class_md', TextType::class, array('label' => 'Šířka'))
                     ->add('htmlId', TextType::class, array('label' => 'ID'))
                     ->add('class', TextType::class, array('label' => 'CSS třída'))
                     ->add('tag', TextType::class, array('label' => 'Hlavní tag regionu (div, section, header, footer)'))
                     ->add('attributes', TextType::class, array('label' => 'Atributy'))
                     ->add('sid', TextType::class, array('label' => 'Šablona'))
                     ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->persist($region);
            $em->flush();

            return new JsonResponse(array('region' => $region->getIdentificator(), 'tag' => $region->getTag(), 'class' => 'region ' . $region->getFullClass()));
        }

        $statusCode = 200;

        if ($form->isSubmitted() && !$form->isValid()){
            $statusCode = 400;
            // dump($form->getErrors(true, false));
        }

        return $this->render('CmsBundle:Editor/Form:region.html.twig', array(
            'region' => $region,
            'document' => $document,
            'form' => $form->createView()
        ));
    }


}
