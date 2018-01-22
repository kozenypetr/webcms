<?php

namespace CmsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use Gedmo\Mapping\Annotation as Gedmo;


/**
 * Page
 * @Gedmo\Tree(type="nested")
 * @ORM\Table(name="document")
 * @ORM\Entity(repositoryClass="CmsBundle\Repository\DocumentRepository")
 */
class Document
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
     * @ORM\Column(name="url", type="string", length=255, unique=true)
     */
    private $url;

    /**
     * @ORM\ManyToOne(targetEntity="DocumentCategory", inversedBy="documents")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id")
     */
    private $category;


    /**
     * @Gedmo\TreeLeft
     * @ORM\Column(name="lft", type="integer")
     */
    private $lft;

    /**
     * @Gedmo\TreeLevel
     * @ORM\Column(name="lvl", type="integer")
     */
    private $lvl;

    /**
     * @Gedmo\TreeRight
     * @ORM\Column(name="rgt", type="integer")
     */
    private $rgt;

    /**
     * @Gedmo\TreeRoot
     * @ORM\ManyToOne(targetEntity="Document")
     * @ORM\JoinColumn(name="tree_root", referencedColumnName="id", onDelete="CASCADE")
     */
    private $root;

    /**
     * @Gedmo\TreeParent
     * @ORM\ManyToOne(targetEntity="Document", inversedBy="children")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $parent;

    /**
     * @ORM\OneToMany(targetEntity="Document", mappedBy="parent")
     * @ORM\OrderBy({"lft" = "ASC"})
     */
    private $children;


    /**
     * @ORM\OneToMany(targetEntity="Widget", mappedBy="document")
     * @ORM\OrderBy({"sort" = "ASC"})
     */
    private $widgets;

    /**
     * @ORM\OneToMany(targetEntity="Region", mappedBy="document")
     */
    private $regions;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->children = new \Doctrine\Common\Collections\ArrayCollection();
        $this->widgets = new \Doctrine\Common\Collections\ArrayCollection();
        $this->regions = new \Doctrine\Common\Collections\ArrayCollection();
    }

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
     * @return Document
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
     * Set url
     *
     * @param string $url
     *
     * @return Document
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set lft
     *
     * @param integer $lft
     *
     * @return Document
     */
    public function setLft($lft)
    {
        $this->lft = $lft;

        return $this;
    }

    /**
     * Get lft
     *
     * @return integer
     */
    public function getLft()
    {
        return $this->lft;
    }

    /**
     * Set lvl
     *
     * @param integer $lvl
     *
     * @return Document
     */
    public function setLvl($lvl)
    {
        $this->lvl = $lvl;

        return $this;
    }

    /**
     * Get lvl
     *
     * @return integer
     */
    public function getLvl()
    {
        return $this->lvl;
    }

    /**
     * Set rgt
     *
     * @param integer $rgt
     *
     * @return Document
     */
    public function setRgt($rgt)
    {
        $this->rgt = $rgt;

        return $this;
    }

    /**
     * Get rgt
     *
     * @return integer
     */
    public function getRgt()
    {
        return $this->rgt;
    }

    /**
     * Set category
     *
     * @param \CmsBundle\Entity\DocumentCategory $category
     *
     * @return Document
     */
    public function setCategory(\CmsBundle\Entity\DocumentCategory $category = null)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category
     *
     * @return \CmsBundle\Entity\DocumentCategory
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Set root
     *
     * @param \CmsBundle\Entity\Document $root
     *
     * @return Document
     */
    public function setRoot(\CmsBundle\Entity\Document $root = null)
    {
        $this->root = $root;

        return $this;
    }

    /**
     * Get root
     *
     * @return \CmsBundle\Entity\Document
     */
    public function getRoot()
    {
        return $this->root;
    }

    /**
     * Set parent
     *
     * @param \CmsBundle\Entity\Document $parent
     *
     * @return Document
     */
    public function setParent(\CmsBundle\Entity\Document $parent = null)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get parent
     *
     * @return \CmsBundle\Entity\Document
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Add child
     *
     * @param \CmsBundle\Entity\Document $child
     *
     * @return Document
     */
    public function addChild(\CmsBundle\Entity\Document $child)
    {
        $this->children[] = $child;

        return $this;
    }

    /**
     * Remove child
     *
     * @param \CmsBundle\Entity\Document $child
     */
    public function removeChild(\CmsBundle\Entity\Document $child)
    {
        $this->children->removeElement($child);
    }

    /**
     * Get children
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * Add widget
     *
     * @param \CmsBundle\Entity\Widget $widget
     *
     * @return Document
     */
    public function addWidget(\CmsBundle\Entity\Widget $widget)
    {
        $this->widgets[] = $widget;

        return $this;
    }

    /**
     * Remove widget
     *
     * @param \CmsBundle\Entity\Widget $widget
     */
    public function removeWidget(\CmsBundle\Entity\Widget $widget)
    {
        $this->widgets->removeElement($widget);
    }

    /**
     * Get widgets
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getWidgets()
    {
        return $this->widgets;
    }

    /**
     * Add region
     *
     * @param \CmsBundle\Entity\Region $region
     *
     * @return Document
     */
    public function addRegion(\CmsBundle\Entity\Region $region)
    {
        $this->regions[] = $region;

        return $this;
    }

    /**
     * Remove region
     *
     * @param \CmsBundle\Entity\Region $region
     */
    public function removeRegion(\CmsBundle\Entity\Region $region)
    {
        $this->regions->removeElement($region);
    }

    /**
     * Get regions
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getRegions()
    {
        return $this->regions;
    }

    /**
     * @param $region
     * @return array
     */
    public function getRegionWidgets($region)
    {
        $widgets = [];
        foreach ($this->getWidgets() as $widget)
        {
            if ($widget->getRegion() == $region)
            {
                $widgets[] = $widget;
            }
        }
        return $widgets;
    }


    public function getTreeName()
    {
        return str_repeat('-', $this->getLvl()) . ' ' .  $this->getName() . '[' . $this->getCategory()->getName() .  ']';
    }
}
