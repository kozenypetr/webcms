<?php

namespace CmsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;


use CmsBundle\Entity\GalleryCategory;

use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Traits\TimestampableEntity;


/**
 * Gallery
 *
 * @ORM\Table(name="gallery")
 * @ORM\Entity(repositoryClass="CmsBundle\Repository\GalleryRepository")
 */
class Gallery
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
     * Novinka
     * @var boolean
     *
     * @ORM\Column(name="is_new", type="boolean", options={"default": false})
     */
    private $isNew = false;

    /**
     * Doporucujeme
     * @var boolean
     *
     * @ORM\Column(name="is_top", type="boolean", options={"default": false})
     */
    private $isTop = false;


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
     * @ORM\Column(name="annotation", type="string", length=255, nullable=true)
     */
    private $annotation;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\ManyToMany(targetEntity="GalleryCategory", inversedBy="galleries")
     * @ORM\JoinTable(
     *      name="gallery_to_category",
     *      joinColumns={@ORM\JoinColumn(onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn(onDelete="CASCADE")}
     * )
     */
    private $categories;

    /**
     * Many Products have One Category.
     * @ORM\ManyToOne(targetEntity="GalleryCategory", inversedBy="mainGaleries")
     * @ORM\JoinColumn(name="main_category_id", referencedColumnName="id")
     */
    private $mainCategory;


    /**
     * One Product has Many Images.
     * @ORM\OneToMany(targetEntity="Image", mappedBy="gallery", cascade={"persist", "remove"})
     * @ORM\OrderBy({"sort" = "ASC"})
     */
    private $images;


    /**
     * @var integer
     *
     * @ORM\Column(name="sort", type="integer", nullable=true, options={"default": 1000})
     */
    private $sort = 1000;

    private $document;


    private $template;

    
    public function __construct()
    {
      $this->categories = new ArrayCollection();
      $this->images     = new ArrayCollection();
    }


    public function setDocument($document)
    {
        $this->document = $document;
    }

    public function setTemplate($template)
    {
        $this->template = $template;
    }

    public function getTemplate()
    {
        return $this->template;
    }

    public function getUrl()
    {
        return $this->document->getUrl() . '/' . $this->getMainCategory()->getSlug() . '/' . $this->getSlug();
    }


    public function __clone() {

        if ($this->getImages()->count())
        {
            $images = $this->getImages();

            $this->images = new ArrayCollection();

            foreach ($images as $image)
            {
                $i = clone $image;
                $i->setGallery($this);
                $this->addImage($i);
            }
        }
    }

    public function __toString() {
        return $this->name?$this->name:'galerie';
    }

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
     * @return Gallery
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
     * Set isNew.
     *
     * @param bool $isNew
     *
     * @return Gallery
     */
    public function setIsNew($isNew)
    {
        $this->isNew = $isNew;

        return $this;
    }

    /**
     * Get isNew.
     *
     * @return bool
     */
    public function getIsNew()
    {
        return $this->isNew;
    }

    /**
     * Set isTop.
     *
     * @param bool $isTop
     *
     * @return Gallery
     */
    public function setIsTop($isTop)
    {
        $this->isTop = $isTop;

        return $this;
    }

    /**
     * Get isTop.
     *
     * @return bool
     */
    public function getIsTop()
    {
        return $this->isTop;
    }

    /**
     * Set locale.
     *
     * @param string $locale
     *
     * @return Gallery
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
     * @return Gallery
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
     * @return Gallery
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
     * @return Gallery
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
     * @return Gallery
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
     * @return Gallery
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
     * @return Gallery
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
     * @return Gallery
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
     * @return Gallery
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
     * @return Gallery
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
     * Add category.
     *
     * @param \CmsBundle\Entity\GalleryCategory $category
     *
     * @return Gallery
     */
    public function addCategory(\CmsBundle\Entity\GalleryCategory $category)
    {
        $this->categories[] = $category;

        return $this;
    }

    /**
     * Remove category.
     *
     * @param \CmsBundle\Entity\GalleryCategory $category
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeCategory(\CmsBundle\Entity\GalleryCategory $category)
    {
        return $this->categories->removeElement($category);
    }

    /**
     * Get categories.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCategories()
    {
        return $this->categories;
    }

    /**
     * Set mainCategory.
     *
     * @param \CmsBundle\Entity\GalleryCategory|null $mainCategory
     *
     * @return Gallery
     */
    public function setMainCategory(\CmsBundle\Entity\GalleryCategory $mainCategory = null)
    {
        $this->mainCategory = $mainCategory;

        return $this;
    }

    /**
     * Get mainCategory.
     *
     * @return \CmsBundle\Entity\GalleryCategory|null
     */
    public function getMainCategory()
    {
        return $this->mainCategory;
    }

    /**
     * Add image.
     *
     * @param \CmsBundle\Entity\Image $image
     *
     * @return Gallery
     */
    public function addImage(\CmsBundle\Entity\Image $image)
    {
        $this->images[] = $image;

        return $this;
    }

    /**
     * Remove image.
     *
     * @param \CmsBundle\Entity\Image $image
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeImage(\CmsBundle\Entity\Image $image)
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
}
