<?php

namespace CmsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Page
 *
 * @ORM\Table(name="region")
 * @ORM\Entity(repositoryClass="CmsBundle\Repository\RegionRepository")
 */
class Region
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
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="sid", type="string", length=255, nullable=true)
     */
    private $sid;

    /**
     * @var string
     *
     * @ORM\Column(name="html_id", type="string", length=255, nullable=true)
     */
    private $htmlId;

    /**
     * @var string
     *
     * @ORM\Column(name="tag", type="string", length=255, options={"default" : "div"})
     */
    private $tag = 'div';

    /**
     * @var string
     *
     * @ORM\Column(name="attributes", type="string", length=255, nullable=true)
     */
    private $attributes;

    /**
     * @var string
     *
     * @ORM\Column(name="class", type="string", length=255, nullable=true)
     */
    private $class;

    /**
     * @var string
     *
     * @ORM\Column(name="class_md", type="string", length=255, nullable=true)
     */
    private $class_md = 'col-md-12';

    /**
     * @ORM\ManyToOne(targetEntity="Document", inversedBy="regions")
     * @ORM\JoinColumn(name="document_id", referencedColumnName="id")
     */
    private $document;


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
     * Set name
     *
     * @param string $name
     *
     * @return Region
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set class
     *
     * @param string $class
     *
     * @return Region
     */
    public function setClass($class)
    {
        $this->class = $class;

        return $this;
    }

    /**
     * Get class
     *
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * Set document
     *
     * @param \CmsBundle\Entity\Document $document
     *
     * @return Region
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
     * Set classMd
     *
     * @param string $classMd
     *
     * @return Region
     */
    public function setClassMd($classMd)
    {
        $this->class_md = $classMd;

        return $this;
    }

    /**
     * Get classMd
     *
     * @return string
     */
    public function getClassMd()
    {
        return $this->class_md;
    }

    public function getIdentificator()
    {
        return 'region-' . str_replace('.', '-', $this->getName());
    }

    public function getFullClass()
    {
        $class = $this->getClassMd();
        $class .= ($this->getClass())?' ' . $this->getClass():'';

        return $class;
    }

    /**
     * Set sid.
     *
     * @param string|null $sid
     *
     * @return Region
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

    /**
     * Set tag.
     *
     * @param string $tag
     *
     * @return Region
     */
    public function setTag($tag)
    {
        $this->tag = $tag;

        return $this;
    }

    /**
     * Get tag.
     *
     * @return string
     */
    public function getTag()
    {
        return $this->tag;
    }

    /**
     * Set htmlId.
     *
     * @param string|null $htmlId
     *
     * @return Region
     */
    public function setHtmlId($htmlId = null)
    {
        $this->htmlId = $htmlId;

        return $this;
    }

    /**
     * Get htmlId.
     *
     * @return string|null
     */
    public function getHtmlId()
    {
        return $this->htmlId;
    }

    /**
     * Set attributes.
     *
     * @param string|null $attributes
     *
     * @return Region
     */
    public function setAttributes($attributes = null)
    {
        $this->attributes = $attributes;

        return $this;
    }

    /**
     * Get attributes.
     *
     * @return string|null
     */
    public function getAttributes()
    {
        return $this->attributes;
    }
}
