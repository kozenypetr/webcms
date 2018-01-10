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


class DocumentController extends Controller
{
    /**
     * @Route("/cms-edit/widget/{id}", name="cms_edit_widget")
     */
    public function widgetAction(Request $request, Widget $widget)
    {
        /*$form = $this->createForm(WidgetType::class, $widget, array(
            'action' => $this->generateUrl('cms_edit_widget', array('id' => $widget->getId())),
            'method' => 'POST',
        ));*/

        $service = $this->get($widget->getService());

        $form = $service->setParameters($widget->getParameters())
                        ->createForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $widget->setParameters($form->getData());

            $em = $this->getDoctrine()->getManager();
            $em->persist($widget);
            $em->flush();

            $html = $this->get('templating')->render($service->getTemplate(), array('widget' => $widget, 'ajax' => true));

            return new JsonResponse(array('html' => $html, 'class' => $widget->getClass()));
        }

        $statusCode = 200;

        if ($form->isSubmitted() && !$form->isValid()){
            $statusCode = 400;
            dump($form->getErrors(true, false));
        }

        return $this->render('CmsBundle:Backend/Document:widget.html.twig', array(
            'widget' => $widget,
            'form' => $form->createView()
        ), new Response('', $statusCode));
    }


    /**
     * @Route("/cms/widget/add", name="cms_widget_add")
     */
    public function widgetAddAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $documentId = $request->get('document_id');
        $region = $request->get('region');
        $regionType = $request->get('region_type');
        $prev = $request->get('prev');
        $next = $request->get('next');
        $service = $request->get('widget');

        // nastavime razeni novemu boxu
        $sort = $prev?($prev + 1):1;
        if ($next)
        {
          $oNext = $document = $this->getDoctrine()
                                    ->getRepository(Widget::class)
                                    ->find((int)$next);

          $qb = $em->createQueryBuilder();

          $q = $qb->update('CmsBundle\Entity\Widget', 'w')
            ->set('w.sort', 'w.sort + 1')
            //->set(array('w.sort' => DB::expr('w.sort + 1')))
            ->where('w.region = ?1')
            ->andWhere('w.document = ?2')
            ->andWhere('w.sort >= ?3')
            ->setParameter(1, $region)
            ->setParameter(2, $documentId)
            ->setParameter(3, $oNext->getSort())
            ->getQuery();

          $p = $q->execute();
        }

        $document = $this->getDoctrine()
                          ->getRepository(Document::class)
                          ->find($documentId);

        $widget = new Widget();

        $widget->setTag('div');
        $widget->setHtml('RRR');
        $widget->setSort($sort);
        $widget->setRegion($region);
        if ($regionType == 'page')
        {
          $widget->setDocument($document);
        }
        $widget->setParameters($this->get($service)->getDefault());
        $widget->setService($service);


        $em->persist($widget);
        $em->flush();

        $widgetHtml = $this->get('templating')->render($this->get($service)->getTemplate(), array('widget' => $widget));

        return new JsonResponse(array('id' => $widget->getId(), 'widgetHtml' => $widgetHtml));
    }


}
