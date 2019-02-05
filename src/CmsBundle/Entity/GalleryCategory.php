<?php

namespace CmsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

use Symfony\Component\HttpFoundation\File\UploadedFile;

use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Traits\TimestampableEntity;


/**
 * GalleryCategory
 *
 * @ORM\Table(name="gallery_category")
 * @ORM\Entity(repositoryClass="CmsBundle\Repository\GalleryCategoryRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class GalleryCategory
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
    private $isActive;

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
     * @ORM\Column(name="filename", type="string", length=255, nullable=true)
     */
    private $filename;

    /**
     * Unmapped property to handle file uploads
     */
    private $file;

    /**
     * @var string
     *
     * @ORM\Column(name="icon", type="string", length=255, nullable=true)
     */
    private $icon;

    /**
     * @var int
     *
     * @ORM\Column(name="sort", type="integer", nullable=true)
     */
    private $sort;

    /**
     * @var string
     *
     *
     * @ORM\Column(name="slug", type="string", length=255)
     * @Gedmo\Slug(fields={"name"})
     */
    private $slug;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\ManyToMany(targetEntity="Gallery", mappedBy="categories")
     */
    private $galleries;

    /**
     * One Product has Many Images.
     * @ORM\OneToMany(targetEntity="Gallery", mappedBy="mainCategory")
     */
    private $mainGalleries;


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


    private $document;


    private $template;


    public function __construct()
    {
      $this->galleries = new ArrayCollection();
      // $this->image = new EmbeddedFile();
    }


    public function __toString() {
      return sprintf("%s [%s]", (string)$this->getName(), $this->getLocale());
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
        return $this->document->getUrl() . '/' . $this->getSlug();
    }


    /**
     * Manages the copying of the file to the relevant place on the server
     */
    public function upload()
    {
        // the file property can be empty if the field is not required
        if (null === $this->getFile()) {
            return;
        }

        // we use the original file name here but you should
        // sanitize it at least to avoid any security issues
        // move takes the target directory and target filename as params
        $this->getFile()->move(
            $this->getUploadDir(),
            $this->getFile()->getClientOriginalName()
        );

        // set the path property to the filename where you've saved the file
        $this->filename = '/data/gallery/' . $this->getFile()->getClientOriginalName();

        // clean up the file property as you won't need it anymore
        $this->setFile(null);
    }

    public function getFile()
    {
        return $this->file;
    }

    public function setFile($file)
    {
        $this->file = $file;
    }

    public function getUploadDir()
    {
        return realpath(dirname(__FILE__) . '/../../../web/data/gallery');
    }

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function lifecycleFileUpload()
    {
        $this->upload();
    }


    /**
     * Updates the hash value to force the preUpdate and postUpdate events to fire
     */
    public function refreshUpdated()
    {
        $this->setUpdatedAt(new \DateTime());
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
     * @return GalleryCategory
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
     * @return GalleryCategory
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
     * @return GalleryCategory
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
     * Set feedName.
     *
     * @param string|null $feedName
     *
     * @return GalleryCategory
     */
    public function setFeedName($feedName = null)
    {
        $this->feedName = $feedName;

        return $this;
    }

    /**
     * Get feedName.
     *
     * @return string|null
     */
    public function getFeedName()
    {
        return $this->feedName;
    }

    /**
     * Set filename.
     *
     * @param string $filename
     *
     * @return GalleryCategory
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;

        return $this;
    }

    /**
     * Get filename.
     *
     * @return string
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * Set icon.
     *
     * @param string $icon
     *
     * @return GalleryCategory
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
     * Set sort.
     *
     * @param int|null $sort
     *
     * @return GalleryCategory
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
     * Set slug.
     *
     * @param string $slug
     *
     * @return GalleryCategory
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
     * Set description.
     *
     * @param string|null $description
     *
     * @return GalleryCategory
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
     * Add gallery.
     *
     * @param \CmsBundle\Entity\Gallery $gallery
     *
     * @return GalleryCategory
     */
    public function addGallery(\CmsBundle\Entity\Gallery $gallery)
    {
        $this->galleries[] = $gallery;

        return $this;
    }

    /**
     * Remove gallery.
     *
     * @param \CmsBundle\Entity\Gallery $gallery
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeGallery(\CmsBundle\Entity\Gallery $gallery)
    {
        return $this->galleries->removeElement($gallery);
    }

    /**
     * Get galleries.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getGalleries()
    {
        return $this->galleries;
    }

    /**
     * Add mainGallery.
     *
     * @param \CmsBundle\Entity\Gallery $mainGallery
     *
     * @return GalleryCategory
     */
    public function addMainGallery(\CmsBundle\Entity\Gallery $mainGallery)
    {
        $this->mainGalleries[] = $mainGallery;

        return $this;
    }

    /**
     * Remove mainGallery.
     *
     * @param \CmsBundle\Entity\Gallery $mainGallery
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeMainGallery(\CmsBundle\Entity\Gallery $mainGallery)
    {
        return $this->mainGalleries->removeElement($mainGallery);
    }

    /**
     * Get mainGalleries.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getMainGalleries()
    {
        return $this->mainGalleries;
    }




    /**
     * Set customMetatitle.
     *
     * @param string|null $customMetatitle
     *
     * @return GalleryCategory
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
     * @return GalleryCategory
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
     * @return GalleryCategory
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
}
