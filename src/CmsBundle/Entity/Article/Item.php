<?php

namespace CmsBundle\Entity\Article;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

use CmsBundle\Entity\Article\Category;
use CmsBundle\Entity\Article\Image;
use CmsBundle\Entity\Article\Tag;

use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Traits\TimestampableEntity;


/**
 * Gallery
 *
 * @ORM\Table(name="article_item")
 * @ORM\Entity(repositoryClass="CmsBundle\Repository\Article\ItemRepository")
 */
class Item
{
    use TimestampableEntity;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_active", type="boolean", options={"default": true})
     */
    private $isActive = true;

    /**
     * @var string
     *
     * @ORM\Column(name="locale", type="string", length=2)
     */
    private $locale = "cs";

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="subname", type="string", length=255, nullable=true)
     */
    private $subname;

    /**
     * @var string
     *
     * @ORM\Column(name="date_display", type="date", length=255, nullable=true)
     */
    private $dateDisplay;

    /**
     * @var string
     *
     * @ORM\Column(name="date_publish", type="date", length=255, nullable=true)
     */
    private $datePublish;

    /**
     * @var string
     *
     * @ORM\Column(name="custom_metatitle", type="string", length=255, nullable=true)
     */
    private $customMetatitle;

    /**
     * @var string
     *
     * @ORM\Column(name="custom_metadescription", type="text", nullable=true, nullable=true)
     */
    private $customMetadescription;

    /**
     * @var string
     *
     * @ORM\Column(name="custom_metakeywords", type="string", length=255, nullable=true)
     */
    private $customMetakeywords;

    /**
     * @var string
     *
     * @Gedmo\Slug(fields={"name"})
     * @ORM\Column(name="slug", type="string", length=255)
     */
    private $slug;

    /**
     * @var string
     *
     * @ORM\Column(name="annotation_list", type="text", nullable=true)
     */
    private $annotationList;

    /**
     * @var string
     *
     * @ORM\Column(name="annotation", type="text", nullable=true)
     */
    private $annotation;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="text", type="text", nullable=true)
     */
    private $text;

    /**
     * @var integer
     *
     * @ORM\Column(name="sort", type="integer", nullable=true, options={"default": 1000})
     */
    private $sort = 1000;


    /**
     * Many Products have One Category.
     * @ORM\ManyToOne(targetEntity="Category", inversedBy="items")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id")
     */
    private $category;


    /**
     * @ORM\ManyToMany(targetEntity="Tag", inversedBy="tags")
     * @ORM\JoinTable(
     *      name="article_item_to_tag",
     *      joinColumns={@ORM\JoinColumn(onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn(onDelete="CASCADE")}
     * )
     */
    private $tags;


    /**
     * One Product has Many Images.
     * @ORM\OneToMany(targetEntity="Image", mappedBy="item", cascade={"persist", "remove"})
     * @ORM\OrderBy({"sort" = "ASC"})
     */
    private $images;




    public function getMetatitle()
    {
        return $this->getCustomMetatitle()?$this->getCustomMetatitle():$this->getName();
    }

    public function getMetadescription()
    {
        return $this->getCustomMetadescription()?$this->getCustomMetadescription():'';
    }

    public function getMetakeywords()
    {
        return $this->getCustomMetakeywords()?$this->getCustomMetakeywords():'';
    }

    public function getUrl()
    {
        return  $this->getCategory()->getUrl() . '/' . $this->getSlug();
    }

    public function getPath()
    {
        return array($this->getCategory(), $this);
    }

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set isActive.
     *
     * @param bool $isActive
     *
     * @return Item
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * Get isActive.
     *
     * @return bool
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * Set locale.
     *
     * @param string $locale
     *
     * @return Item
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;

        return $this;
    }

    /**
     * Get locale.
     *
     * @return string
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * Set name.
     *
     * @param string $name
     *
     * @return Item
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set subname.
     *
     * @param string|null $subname
     *
     * @return Item
     */
    public function setSubname($subname = null)
    {
        $this->subname = $subname;

        return $this;
    }

    /**
     * Get subname.
     *
     * @return string|null
     */
    public function getSubname()
    {
        return $this->subname;
    }

    /**
     * Set customMetatitle.
     *
     * @param string|null $customMetatitle
     *
     * @return Item
     */
    public function setCustomMetatitle($customMetatitle = null)
    {
        $this->customMetatitle = $customMetatitle;

        return $this;
    }

    /**
     * Get customMetatitle.
     *
     * @return string|null
     */
    public function getCustomMetatitle()
    {
        return $this->customMetatitle;
    }

    /**
     * Set customMetadescription.
     *
     * @param string|null $customMetadescription
     *
     * @return Item
     */
    public function setCustomMetadescription($customMetadescription = null)
    {
        $this->customMetadescription = $customMetadescription;

        return $this;
    }

    /**
     * Get customMetadescription.
     *
     * @return string|null
     */
    public function getCustomMetadescription()
    {
        return $this->customMetadescription;
    }

    /**
     * Set customMetakeywords.
     *
     * @param string|null $customMetakeywords
     *
     * @return Item
     */
    public function setCustomMetakeywords($customMetakeywords = null)
    {
        $this->customMetakeywords = $customMetakeywords;

        return $this;
    }

    /**
     * Get customMetakeywords.
     *
     * @return string|null
     */
    public function getCustomMetakeywords()
    {
        return $this->customMetakeywords;
    }

    /**
     * Set slug.
     *
     * @param string $slug
     *
     * @return Item
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug.
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set annotation.
     *
     * @param string|null $annotation
     *
     * @return Item
     */
    public function setAnnotation($annotation = null)
    {
        $this->annotation = $annotation;

        return $this;
    }

    /**
     * Get annotation.
     *
     * @return string|null
     */
    public function getAnnotation()
    {
        return $this->annotation;
    }

    /**
     * Set description.
     *
     * @param string|null $description
     *
     * @return Item
     */
    public function setDescription($description = null)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description.
     *
     * @return string|null
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set sort.
     *
     * @param int|null $sort
     *
     * @return Item
     */
    public function setSort($sort = null)
    {
        $this->sort = $sort;

        return $this;
    }

    /**
     * Get sort.
     *
     * @return int|null
     */
    public function getSort()
    {
        return $this->sort;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->tags = new \Doctrine\Common\Collections\ArrayCollection();
        $this->images = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set category.
     *
     * @param \CmsBundle\Entity\Article\Category|null $category
     *
     * @return Item
     */
    public function setCategory(\CmsBundle\Entity\Article\Category $category = null)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category.
     *
     * @return \CmsBundle\Entity\Article\Category|null
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Add tag.
     *
     * @param \CmsBundle\Entity\Article\Tag $tag
     *
     * @return Item
     */
    public function addTag(\CmsBundle\Entity\Article\Tag $tag)
    {
        $this->tags[] = $tag;

        return $this;
    }

    /**
     * Remove tag.
     *
     * @param \CmsBundle\Entity\Article\Tag $tag
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeTag(\CmsBundle\Entity\Article\Tag $tag)
    {
        return $this->tags->removeElement($tag);
    }

    /**
     * Get tags.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * Add image.
     *
     * @param \CmsBundle\Entity\Article\Image $image
     *
     * @return Item
     */
    public function addImage(\CmsBundle\Entity\Article\Image $image)
    {
        $this->images[] = $image;

        return $this;
    }

    /**
     * Remove image.
     *
     * @param \CmsBundle\Entity\Article\Image $image
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeImage(\CmsBundle\Entity\Article\Image $image)
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
     * Set text.
     *
     * @param string|null $text
     *
     * @return Item
     */
    public function setText($text = null)
    {
        $this->text = $text;

        return $this;
    }

    /**
     * Get text.
     *
     * @return string|null
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Set annotationList.
     *
     * @param string|null $annotationList
     *
     * @return Item
     */
    public function setAnnotationList($annotationList = null)
    {
        $this->annotationList = $annotationList;

        return $this;
    }

    /**
     * Get annotationList.
     *
     * @return string|null
     */
    public function getAnnotationList()
    {
        return $this->annotationList;
    }

    /**
     * Set dateDisplay.
     *
     * @param string|null $dateDisplay
     *
     * @return Item
     */
    public function setDateDisplay($dateDisplay = null)
    {
        $this->dateDisplay = $dateDisplay;

        return $this;
    }

    /**
     * Get dateDisplay.
     *
     * @return string|null
     */
    public function getDateDisplay()
    {
        return $this->dateDisplay;
    }

    /**
     * Set datePublish.
     *
     * @param string|null $datePublish
     *
     * @return Item
     */
    public function setDatePublish($datePublish = null)
    {
        $this->datePublish = $datePublish;

        return $this;
    }

    /**
     * Get datePublish.
     *
     * @return string|null
     */
    public function getDatePublish()
    {
        return $this->datePublish;
    }
}
