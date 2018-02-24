<?php

namespace CmsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Page
 *
 * @ORM\Table(name="document_category")
 * @ORM\Entity(repositoryClass="CmsBundle\Repository\DocumentCategoryRepository")
 */
class DocumentCategory
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
     * 
     * @var boolean
     *
     * @ORM\Column(name="is_enable_as_parent", type="boolean")
     */
    private $isEnableAsParent;


    /**
     * @var string
     *
     * @ORM\Column(name="tags", type="json_array")
     */
    private $tags;

    /**
     * @var string
     *
     * @ORM\Column(name="icon", type="string", length=255)
     */
    private $icon;

    /**
     * @var string
     *
     * @ORM\Column(name="widget", type="string", length=255, nullable=true)
     */
    private $widget;

    /**
     * @var string
     *
     * @ORM\Column(name="template", type="string", length=255, nullable=true)
     */
    private $template;

    /**
     * @var string
     *
     * @ORM\Column(name="sid", type="string", length=255, unique=true)
     */
    private $sid;


    /**
     * @ORM\OneToMany(targetEntity="Document", mappedBy="category")
     */
    private $documents;

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
     * @return DocumentCategory
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
     * Set sid
     *
     * @param string $sid
     *
     * @return DocumentCategory
     */
    public function setSid($sid)
    {
        $this->sid = $sid;

        return $this;
    }

    /**
     * Get sid
     *
     * @return string
     */
    public function getSid()
    {
        return $this->sid;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->documents = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add document
     *
     * @param \CmsBundle\Entity\Document $document
     *
     * @return DocumentCategory
     */
    public function addDocument(\CmsBundle\Entity\Document $document)
    {
        $this->documents[] = $document;

        return $this;
    }

    /**
     * Remove document
     *
     * @param \CmsBundle\Entity\Document $document
     */
    public function removeDocument(\CmsBundle\Entity\Document $document)
    {
        $this->documents->removeElement($document);
    }

    /**
     * Get documents
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getDocuments()
    {
        return $this->documents;
    }

    /**
     * Set tags.
     *
     * @param array $tags
     *
     * @return DocumentCategory
     */
    public function setTags($tags)
    {
        $this->tags = $tags;

        return $this;
    }

    /**
     * Get tags.
     *
     * @return array
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * Set icon.
     *
     * @param string $icon
     *
     * @return DocumentCategory
     */
    public function setIcon($icon)
    {
        $this->icon = $icon;

        return $this;
    }

    /**
     * Get icon.
     *
     * @return string
     */
    public function getIcon()
    {
        return $this->icon;
    }

    /**
     * Set widget.
     *
     * @param string $widget
     *
     * @return DocumentCategory
     */
    public function setWidget($widget)
    {
        $this->widget = $widget;

        return $this;
    }

    /**
     * Get widget.
     *
     * @return string
     */
    public function getWidget()
    {
        return $this->widget;
    }

    /**
     * Set isEnableAsParent.
     *
     * @param bool $isEnableAsParent
     *
     * @return DocumentCategory
     */
    public function setIsEnableAsParent($isEnableAsParent)
    {
        $this->isEnableAsParent = $isEnableAsParent;

        return $this;
    }

    /**
     * Get isEnableAsParent.
     *
     * @return bool
     */
    public function getIsEnableAsParent()
    {
        return $this->isEnableAsParent;
    }

    /**
     * Set template.
     *
     * @param string|null $template
     *
     * @return DocumentCategory
     */
    public function setTemplate($template = null)
    {
        $this->template = $template;

        return $this;
    }

    /**
     * Get template.
     *
     * @return string|null
     */
    public function getTemplate()
    {
        return $this->template;
    }
}
