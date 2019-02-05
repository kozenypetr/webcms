<?php

namespace CmsBundle\Service;

use CmsBundle\Entity\Document;

class ContentManager
{
    private $em;

    private $container;

    private $url;

    private $document;

    public function __construct($em, $container)
    {
        $this->em = $em;
        $this->container = $container;


    }

    /**
     * Vraci aktualni dokument z pozadavku
     * @return Document
     */
    public function getDocument()
    {
        if (!$this->document)
        {
            $this->findDocument();
        }

        return $this->document;
    }


    public function setDocument($document)
    {
        $this->document = $document;
    }

    protected function findDocument()
    {
        if (!$this->findDocumentByUrl())
        {
            $this->findDocumentById();
        }
    }


    protected function findDocumentByUrl()
    {
        $this->document = null;
        $this->url = $this->container->get('request_stack')
            ->getCurrentRequest()
            ->get('url');

        if ($this->url)
        {
            $this->document = $this->em
                ->getRepository('CmsBundle:Document')
                ->findOneByUrl($this->url);
        }

        return $this->document;
    }

    protected function findDocumentById()
    {
        $this->documentId = $this->container->get('request_stack')
            ->getCurrentRequest()
            ->get('document_id');

        if ($this->documentId)
        {
            $this->document = $this->em
                ->getRepository('CmsBundle:Document')
                ->find($this->documentId);
        }

        return $this->document;
    }
}