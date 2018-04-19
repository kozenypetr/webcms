<?php

namespace CmsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Page
 *
 * @ORM\Table(name="widget")
 * @ORM\Entity(repositoryClass="CmsBundle\Repository\WidgetRepository")
 */
class Widget
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="sid", type="string", length=255, nullable=true)
     */
    private $sid;


    /**
     * @var string
     *
     * @ORM\Column(name="tag", type="string", length=255, options={"default" : "div"})
     */
    private $tag = 'div';

    /**
     * @var string
     *
     * @ORM\Column(name="subclass", type="string", length=255, nullable=true)
     */
    private $subclass;

    /**
     * @ORM\ManyToOne(targetEntity="Document", inversedBy="widgets")
     * @ORM\JoinColumn(name="document_id", referencedColumnName="id")
     */
    private $document;

    /**
     *
     * @var boolean
     *
     * @ORM\Column(name="is_system", type="boolean", options={"default" : false})
     */
    private $isSystem = false;

    /**
     * @var string
     *
     * @ORM\Column(name="region", type="string", length=32)
     */
    private $region;


    /**
     * @var int
     *
     * @ORM\Column(name="sort", type="integer")
     */
    private $sort;


    /**
     * @var text
     *
     * @ORM\Column(name="html", type="text")
     */
    private $html;

    /**
     * @var string
     *
     * @ORM\Column(name="service", type="string", length=255)
     */
    private $service;

    /**
     * @var parametry widgetu
     *
     * @ORM\Column(name="parameters", type="json_array")
     */
    private $parameters;


    /**
     * @ORM\OneToMany(targetEntity="WidgetImage", mappedBy="widget", cascade={"remove"})
     * @ORM\OrderBy({"sort" = "ASC"})
     */
    private $images;


    /*
     * @ORM\ManyToOne(targetEntity="Box", inversedBy="widgets")
     * @ORM\JoinColumn(name="box_id", referencedColumnName="id")
     *
    private $box;
    */

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }
    

    /**
     * Set tag
     *
     * @param string $tag
     *
     * @return Widget
     */
    public function setTag($tag)
    {
        $this->tag = $tag;

        return $this;
    }

    /**
     * Get tag
     *
     * @return string
     */
    public function getTag()
    {
        return $this->tag;
    }

    /**
     * Set sort
     *
     * @param integer $sort
     *
     * @return Widget
     */
    public function setSort($sort)
    {
        $this->sort = $sort;

        return $this;
    }

    /**
     * Get sort
     *
     * @return integer
     */
    public function getSort()
    {
        return $this->sort;
    }

    /**
     * Set html
     *
     * @param string $html
     *
     * @return Widget
     */
    public function setHtml($html)
    {
        $this->html = $html;

        return $this;
    }

    /**
     * Get html
     *
     * @return string
     */
    public function getHtml()
    {
        return $this->html;
    }

    public function toArray()
    {
        return get_object_vars($this);
    }

    /**
     * Set parameters
     *
     * @param array $parameters
     *
     * @return Widget
     */
    public function setParameters($parameters)
    {
        $this->parameters = $parameters;

        return $this;
    }

    /**
     * Get parameters
     *
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * Set service
     *
     * @param string $service
     *
     * @return Widget
     */
    public function setService($service)
    {
        $this->service = $service;

        return $this;
    }

    /**
     * Get service
     *
     * @return string
     */
    public function getService()
    {
        return $this->service;
    }


    public function getClass()
    {
        $parameters = $this->getParameters();
        $classes = array();
        $classes[] = 'widget';
        foreach (array('class', 'class_md', 'class_sm', 'class_xs', 'class_lg', 'predefined_class') as $c)
        {
            if (isset($parameters[$c]) && $parameters[$c])
            {
                $classes[] = $parameters[$c];
            }
        }
        return join(' ', $classes);
    }

    /**
     * Set region
     *
     * @param string $region
     *
     * @return Widget
     */
    public function setRegion($region)
    {
        $this->region = $region;

        return $this;
    }

    /**
     * Get region
     *
     * @return string
     */
    public function getRegion()
    {
        return $this->region;
    }

    /**
     * Set document
     *
     * @param \CmsBundle\Entity\Document $document
     *
     * @return Widget
     */
    public function setDocument(\CmsBundle\Entity\Document $document = null)
    {
        $this->document = $document;

        return $this;
    }

    /**
     * Get document
     *
     * @return \CmsBundle\Entity\Document
     */
    public function getDocument()
    {
        return $this->document;
    }

    /**
     * Set sid.
     *
     * @param string|null $sid
     *
     * @return Widget
     */
    public function setSid($sid = null)
    {
        $this->sid = $sid;

        return $this;
    }

    /**
     * Get sid.
     *
     * @return string|null
     */
    public function getSid()
    {
        return $this->sid;
    }


    public function getParameter($name)
    {
        return isset($this->parameters[$name])?$this->parameters[$name]:null;
    }

    public function getSuggestionTemplateFiles($defaultTemplate)
    {
        $templates = [];

        $sid = $this->getParameter('sid');
        if ($sid)
        {
            $templates[] = str_replace('.html.twig', "-{$sid}.html.twig", $defaultTemplate);
        }

        $templates[] = str_replace('.html.twig', "-id-{$this->getId()}.html.twig", $defaultTemplate);

        $templates[] = $defaultTemplate;

        return $templates;
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->images = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add image.
     *
     * @param \CmsBundle\Entity\WidgetImage $image
     *
     * @return Widget
     */
    public function addImage(\CmsBundle\Entity\WidgetImage $image)
    {
        $this->images[] = $image;

        return $this;
    }

    /**
     * Remove image.
     *
     * @param \CmsBundle\Entity\WidgetImage $image
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeImage(\CmsBundle\Entity\WidgetImage $image)
    {
        return $this->images->removeElement($image);
    }

    /**
     * Get images.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getImages()
    {
        return $this->images;
    }

    /**
     * Set subelement.
     *
     * @param string $subelement
     *
     * @return Widget
     */
    public function setSubelement($subelement)
    {
        $this->subelement = $subelement;

        return $this;
    }

    /**
     * Get subelement.
     *
     * @return string
     */
    public function getSubelement()
    {
        return $this->subelement;
    }

    /**
     * Set subclass.
     *
     * @param string $subclass
     *
     * @return Widget
     */
    public function setSubclass($subclass)
    {
        $this->subclass = $subclass;

        return $this;
    }

    /**
     * Get subclass.
     *
     * @return string
     */
    public function getSubclass()
    {
        return $this->subclass;
    }

    /**
     * Set isSystem.
     *
     * @param bool $isSystem
     *
     * @return Widget
     */
    public function setIsSystem($isSystem)
    {
        $this->isSystem = $isSystem;

        return $this;
    }

    /**
     * Get isSystem.
     *
     * @return bool
     */
    public function getIsSystem()
    {
        return $this->isSystem;
    }
}
