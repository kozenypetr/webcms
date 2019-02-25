<?php

namespace CmsBundle\Controller\Frontend;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class ContactController extends Controller
{
    /**
     * Odeslani formulare
     * @Route("/kontakt-odeslani-formulare", name="cms_contactform_send")
     * @Method({"GET", "POST"})
     */
    public function sendAction(Request $request)
    {
        $data = $request->get('form');

        $widget = $this->getDoctrine()
                       ->getRepository('CmsBundle:Widget')
                       ->find($data['widget_id']);

        $mailText = $widget->getParameter('mailText');

        $data['message'] = nl2br($data['message']);

        $query = $this->renderView('CmsBundle:Templates/base:contactform.base.html.twig', ['data' => $data]);

        $mailText = str_replace('%dotaz%', $query, $mailText);

        $message = (new \Swift_Message($data['subject']))
            ->setFrom($widget->getParameter('sender'))
            ->setTo($widget->getParameter('receiver'))
            ->setBcc($data['email'])
            ->setBody($mailText, 'text/html');

        $this->get('mailer')->send($message);

        if ($widget->getParameter('document'))
        {
            if (preg_match('/\[(.+):([0-9]+)\]/', $widget->getParameter('document'), $matches))
            {
                $documentId = $matches[2];
                // najdeme dokument
                $document = $this->getDoctrine()->getRepository('CmsBundle:Document')->find($documentId);

                return $this->redirect('/' . $document->getUrl());
            }
        }

        return $this->redirect('/kontakt');
    }
}
