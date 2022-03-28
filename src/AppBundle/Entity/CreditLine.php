<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Gedmo\Mapping\Annotation as Gedmo;


/**
 * Company
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class CreditLine
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
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Credit", inversedBy="lines")
     */
    protected $credit;    
    
    
    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true, name="name")
     * @Assert\NotBlank()
     * @Assert\Length(
     *             min=3,
     *             max=255)
     */
    protected $name;  
    
    /**
     * @var float
     * @ORM\Column(name="price", type="decimal", scale=2, nullable=true)
     */
    private $price;  
    
    /**
     * @var string
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $quantity = 1;  
    
    /**
     * The tax rate to apply on the product.
     *
     * @var string
     * @ORM\Column(type="decimal", scale=2, name="tax_rate")
     */
    protected $taxrate = 0.21;    
    
    

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
     * @return InvoiceLine
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
     * Set price
     *
     * @param string $price
     *
     * @return InvoiceLine
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price
     *
     * @return string
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set quantity
     *
     * @param integer $quantity
     *
     * @return InvoiceLine
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * Get quantity
     *
     * @return integer
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * Set taxrate
     *
     * @param string $taxrate
     *
     * @return CreditLine
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

    /**
     * Set credit
     *
     * @param \AppBundle\Entity\Credit $credit
     *
     * @return CreditLine
     */
    public function setCredit(\AppBundle\Entity\Credit $credit = null)
    {
        $this->credit = $credit;

        return $this;
    }

    /**
     * Get credit
     *
     * @return \AppBundle\Entity\Credit
     */
    public function getCredit()
    {
        return $this->credit;
    }
    /**
     * Return the total price (tax included).
     *
     * @return float
     */
    public function getTotalPrice()
    {
        return $this->price * $this->quantity;
        //return $this->price * $this->quantity * (1 + $this->taxrate);
    }        
}
