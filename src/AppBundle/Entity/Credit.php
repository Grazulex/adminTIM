<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;


/**
 * Company
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CreditRepository")
 */
class Credit
{
 
    const STATUS_NEW      = 0;
    const STATUS_OPEN     = 1;
    const STATUS_CLOSED   = 2;
    
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=true)
     *
     * @var AppBundle\Entity\User;
     */
    protected $user;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Customer")
     * @ORM\JoinColumn(name="customer_id", referencedColumnName="id", nullable=true)
     *
     * @var AppBundle\Entity\Customer;
     */
    protected $customer=null;
    
        /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Location")
     * @ORM\JoinColumn(name="location_id", referencedColumnName="id", nullable=true)
     *
     * @var AppBundle\Entity\Location;
     */
    protected $location;       
    
    /**
     * @ORM\Column(type="datetime")
     */
    protected $created;     
    
    /**
     * @var float
     * @ORM\Column(name="total", type="decimal", scale=2, nullable=true)
     */
    private $total;  
    
    /**
     * @var float
     * @ORM\Column(type="decimal", scale=2, nullable=true)
     */
    private $total_with_discount;      
    
    /**
     * @var float
     * @ORM\Column(name="discount", type="decimal", scale=2, nullable=true)
     */
    private $discount;
    
    /**
     * @var string
     * @ORM\Column(type="text", nullable=true)
     */
    private $discount_comment;           
    
    /**
     * @var string
     * @ORM\Column(name="reference", type="string", length=25, nullable=true)
     */
    private $reference;    
    
    /**
     * @var string
     * @ORM\Column(type="integer", nullable=true)
     */
    private $credit_number;     
    
    /**
     * @ORM\Column(type="date", nullable=true)
     */
    protected $paiement;   
    
    /**
     * @var string
     *
     * @ORM\Column(name="status", type="integer")
     */
    private $status = self::STATUS_NEW;    
         
    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\CreditLine", mappedBy="credit", cascade={"persist", "remove"}, orphanRemoval=true)
     * @ORM\OrderBy({"id" = "ASC"})
     */
    protected $lines;     

    public function __construct()
    {
        $this->created = new \DateTime();  
        $this->lines = new ArrayCollection();
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
     * Set created
     *
     * @param \DateTime $created
     *
     * @return Credit
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set total
     *
     * @param string $total
     *
     * @return Credit
     */
    public function setTotal($total)
    {
        $this->total = $total;

        return $this;
    }

    /**
     * Get total
     *
     * @return string
     */
    public function getTotal()
    {
        return $this->total;
    }
    
    /**
     * Set totalWithDiscount
     *
     * @param string $totalWithDiscount
     *
     * @return Invoice
     */
    public function setTotalWithDiscount($totalWithDiscount)
    {
        $this->total_with_discount = $totalWithDiscount;

        return $this;
    }

    /**
     * Get totalWithDiscount
     *
     * @return string
     */
    public function getTotalWithDiscount()
    {
        return $this->total_with_discount;
    }
    

    /**
     * Set discount
     *
     * @param string $discount
     *
     * @return Credit
     */
    public function setDiscount($discount)
    {
        $this->discount = $discount;

        return $this;
    }

    /**
     * Get discount
     *
     * @return string
     */
    public function getDiscount()
    {
        return $this->discount;
    }

    /**
     * Set discountComment
     *
     * @param string $discountComment
     *
     * @return Credit
     */
    public function setDiscountComment($discountComment)
    {
        $this->discount_comment = $discountComment;

        return $this;
    }

    /**
     * Get discountComment
     *
     * @return string
     */
    public function getDiscountComment()
    {
        return $this->discount_comment;
    }

    /**
     * Set reference
     *
     * @param string $reference
     *
     * @return Credit
     */
    public function setReference($reference)
    {
        $this->reference = $reference;

        return $this;
    }

    /**
     * Get reference
     *
     * @return string
     */
    public function getReference()
    {
        return $this->reference;
    }

    /**
     * Set creditNumber
     *
     * @param integer $creditNumber
     *
     * @return Credit
     */
    public function setCreditNumber($creditNumber)
    {
        $this->credit_number = $creditNumber;

        return $this;
    }

    /**
     * Get creditNumber
     *
     * @return integer
     */
    public function getCreditNumber()
    {
        return $this->credit_number;
    }

    /**
     * Set paiement
     *
     * @param \DateTime $paiement
     *
     * @return Credit
     */
    public function setPaiement($paiement)
    {
        $this->paiement = $paiement;

        return $this;
    }

    /**
     * Get paiement
     *
     * @return \DateTime
     */
    public function getPaiement()
    {
        return $this->paiement;
    }

    /**
     * Set status
     *
     * @param integer $status
     *
     * @return Credit
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return integer
     */
    public function getStatus()
    {
        return $this->status;
    }
    
    /**
     * Set user
     *
     * @param \AppBundle\Entity\User $user
     *
     * @return Credit
     */
    public function setUser(\AppBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \AppBundle\Entity\user
     */
    public function getUser()
    {
        return $this->user;
    }
    
    /**
     * Set location
     *
     * @param \AppBundle\Entity\Location $location
     *
     * @return Invoice
     */
    public function setLocation(\AppBundle\Entity\Location $location = null)
    {
        $this->location = $location;

        return $this;
    }

    /**
     * Get location
     *
     * @return \AppBundle\Entity\Location
     */
    public function getLocation()
    {
        return $this->location;
    }     

    /**
     * Add line
     *
     * @param \AppBundle\Entity\CreditLine $line
     *
     * @return Credit
     */
    public function addLine(\AppBundle\Entity\CreditLine $line)
    {
        $this->lines[] = $line;
        $line->setCredit($this); 

        return $this;
    }

    /**
     * Remove line
     *
     * @param \AppBundle\Entity\CreditLine $line
     */
    public function removeLine(\AppBundle\Entity\CreditLine $line)
    {
        $this->lines->removeElement($line);
    }

    /**
     * Get lines
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getLines()
    {
        return $this->lines;
    }
    
    
    public function getTotalVAT($vat)
    {
        $total = 0;
        foreach ($this->getLines() as $line) {
            if ($line->getTaxrate() == $vat) {
                $total = $total + ($line->getprice()*$line->getQuantity());
            }
        }
        return $total;
    }
    
    public function getTotalWithoutVAT($vat)
    {
        $total = 0;
        foreach ($this->getLines() as $line) {
            if ($line->getTaxrate() == $vat) {
                $total = $total + (($line->getprice()/(1+$vat))*$line->getQuantity());
            }
        }
        return $total;
    }    
    
    public function getVAT($vat)
    {
        return $this->getTotalVAT($vat)-$this->getTotalWithoutVAT($vat);
    }    
    
    public function __toString()
    {
        return 'CREDIT('.(string)$this->getId().')'.(string)$this->getCreditNumber().' - '.$this->getCustomer()->getName();
    }

    /**
     * @return AppBundle\Entity\Customer
     */
    public function getCustomer()
    {
        return $this->customer;
    }

    /**
     * @param AppBundle\Entity\Customer $customer
     */
    public function setCustomer($customer)
    {
        $this->customer = $customer;
    }
}

/**
 * update `credit` set customer_id = (select id from customer where customer.old = credit.user_id and customer.old_location = credit.location_id)
 */
