<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Item
 *
 * @ORM\Table()
 * @ORM\Entity
 * @Gedmo\TranslationEntity(class="AppBundle\Entity\ItemTranslation")
 */
class Item
{
 
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\ItemCategory", inversedBy="items")
     */
    protected $category;        

    
    /**
     * @var string
     * @Gedmo\Translatable
     * @ORM\Column(type="string", length=120, nullable=false, name="name")
     * @Assert\NotBlank()
     * @Assert\Length(
     *             min=2,
     *             max=120)
     */
    protected $name;  
    
    /**
     * @ORM\OneToMany(targetEntity="ItemTranslation", mappedBy="object", cascade={"persist", "remove"}, fetch="EAGER")
     */
    protected $translations;

    /**
     * Required for Translatable behaviour
     * @Gedmo\Locale
     */
    protected $locale;      
    
    /**
     * @var ArrayCollection
     * 
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\LogisticLine", mappedBy="item")
     */
    protected $logistics;     
    

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
     * @return Category
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
     * Set ItemCategory
     *
     * @param \AppBundle\Entity\ItemCategory $ItemCategory
     *
     * @return Item
     */
    public function setCategory(\AppBundle\Entity\ItemCategory $ItemCategory = null)
    {
        $this->category = $ItemCategory;

        return $this;
    }

    /**
     * Get Category
     *
     * @return \AppBundle\Entity\ItemCategory
     */
    public function getCategory()
    {
        return $this->category;
    } 

    public function __construct()
    {
        $this->logistics = new ArrayCollection();
        $this->translations = new ArrayCollection();
    } 
    
    public function __toString()
    {
        return $this->name;
    }     
    
    /**
     * Add Logistic
     *
     * @param \AppBundle\Entity\LogisticLine $line
     * @return Item
     */
    public function addLogistic(\AppBundle\Entity\LogisticLine $line)
    {
        $this->logistics[] = $line;

        return $this;
    }

    /**
     * Remove Logistic
     *
     * @param \AppBundle\Entity\LogisticLine $line
     */
    public function removeLogistic(\AppBundle\Entity\LogisticLine $line)
    {
        $this->logistics->removeElement($line);
    }

    /**
     * Get Logistics
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getLogistics()
    {
        return $this->logistics;
    }
    
    
    public function getLocale()
    {
        return $this->locale;
    }

    public function setLocale($locale)
    {
        $this->locale = $locale;
    }
    
    public function getTranslations()
    {
        return $this->translations;
    }

    public function addTranslation(ItemTranslation $t)
    {
        $this->translations->add($t);
        $t->setObject($this);
    }

    public function removeTranslation(ItemTranslation $t)
    {
        $this->translations->removeElement($t);
    }

    public function setTranslations($translations)
    {
        $this->translations = $translations;
    }    
    
    
}
