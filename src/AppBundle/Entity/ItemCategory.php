<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * ItemCategory
 *
 * @ORM\Table()
 * @ORM\Entity
 * @Gedmo\TranslationEntity(class="AppBundle\Entity\ItemCategoryTranslation")
 */
class ItemCategory
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
     * @ORM\OneToMany(targetEntity="ItemCategory", mappedBy="parent")
     */
    protected $children;    
    
    /**
     * @ORM\ManyToOne(targetEntity="ItemCategory", inversedBy="children")
     * @ORM\JoinColumn(name="parent", referencedColumnName="id")
     */    
    protected $parent;

    
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
     * @var string
     * @ORM\Column(type="integer", nullable=true)
     */
    private $reference = 1;   
    
    /**
     * @var boolean
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $useForCash = false;
    
    /**
     * @var boolean
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $useForLogistic = false;    
    
    
    /**
     * The tax rate to apply on the product.
     *
     * @var string
     * @ORM\Column(type="decimal", scale=2, name="tax_rate")
     */
    protected $taxrate = 0.21;     
    
    /**
     * @ORM\OneToMany(targetEntity="ItemCategoryTranslation", mappedBy="object", cascade={"persist", "remove"})
     */
    protected $translations;

    /**
     * Required for Translatable behaviour
     * @Gedmo\Locale
     */
    protected $locale;    
    
    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Item", mappedBy="category", cascade={"persist", "remove"})
     * @ORM\OrderBy({"id" = "ASC"})
     */
    protected $items;    
    
    
    /**
     * @var ArrayCollection
     * 
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Logistic", mappedBy="category")
     */
    protected $logistics;   
    
    /**
     * @var ArrayCollection
     * 
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Cash", mappedBy="category")
     */
    protected $cashs;     
    
    public function __construct()
    {
        $this->items = new ArrayCollection();
        $this->logistics = new ArrayCollection();
        $this->translations = new ArrayCollection();
        $this->cashs = new ArrayCollection();
        $this->children = new ArrayCollection();
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
     * Set reference
     *
     * @param integer $reference
     *
     * @return Category
     */
    public function setReference($reference)
    {
        $this->reference = $reference;

        return $this;
    }

    /**
     * Get reference
     *
     * @return integer
     */
    public function getReference()
    {
        return $this->reference;
    }

    /**
     * Set taxrate
     *
     * @param string $taxrate
     *
     * @return Category
     */
    public function setTaxrate($taxrate)
    {
        $this->taxrate = $taxrate;

        return $this;
    }

    /**
     * Get taxrate
     *
     * @return string
     */
    public function getTaxrate()
    {
        return $this->taxrate;
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

    public function addTranslation(ItemCategoryTranslation $t)
    {
        $this->translations->add($t);
        $t->setObject($this);
    }

    public function removeTranslation(ItemCategoryTranslation $t)
    {
        $this->translations->removeElement($t);
    }

    public function setTranslations($translations)
    {
        $this->translations = $translations;
    }    
        
    /**
     * Add item
     *
     * @param \AppBundle\Entity\Item $item
     *
     * @return Category
     */
    public function addLine(\AppBundle\Entity\Item $item)
    {
        $this->items[] = $item;
        $item->setCategory($this); 

        return $this;
    }

    /**
     * Remove item
     *
     * @param \AppBundle\Entity\Item $item
     */
    public function removeLine(\AppBundle\Entity\Item $item)
    {
        $this->items->removeElement($item);
    }

    /**
     * Get items
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getItems()
    {
        return $this->items;
    }
    
    public function __toString()
    {
        return (string)$this->getName();
    }     
    
    /**
     * Add logistic
     *
     * @param \AppBundle\Entity\Logistic $logistic
     *
     * @return itemCategory
     */
    public function addLogistic(\AppBundle\Entity\Logistic $logistic)
    {
        $this->logistics[] = $logistic;

        return $this;
    }

    /**
     * Remove Logistic
     *
     * @param \AppBundle\Entity\Logistic $logistic
     */
    public function removeLogistic(\AppBundle\Entity\Logistic $logistic)
    {
        $this->logistics->removeElement($logistic);
    }

    /**
     * Get logistics
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getLogistics()
    {
        return $this->logistics;
    }
    
    public function getParent() {
        return $this->parent;
    }

    public function getChildren() {
        return $this->children;
    }

    // always use this to setup a new parent/child relationship
    public function addChild(ItemCategory $child) {
       $this->children[] = $child;
       $child->setParent($this);
    }

    public function setParent(ItemCategory $parent) {
       $this->parent = $parent;
    }

    /**
     * Add cashs
     *
     * @param \AppBundle\Entity\Cash $cash
     * @return Category
     */
    public function addCash(\AppBundle\Entity\Cash $cash)
    {
        $this->cashs[] = $cash;

        return $this;
    }

    /**
     * Remove cash
     *
     * @param \AppBundle\Entity\Cash $cash
     */
    public function removeCash(\AppBundle\Entity\Cash $cash)
    {
        $this->cashs->removeElement($cash);
    }

    /**
     * Get cashs
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCashs()
    {
        return $this->cashs;
    }  

    function getUseForCash() {
        return $this->useForCash;
    }

    function getUseForLogistic() {
        return $this->useForLogistic;
    }

    function setUseForCash($useForCash) {
        $this->useForCash = $useForCash;
    }

    function setUseForLogistic($useForLogistic) {
        $this->useForLogistic = $useForLogistic;
    }

    
   
}
