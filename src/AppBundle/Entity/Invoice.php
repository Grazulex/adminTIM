<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;


/**
 * Company
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="AppBundle\Repository\InvoiceRepository")
 */
class Invoice
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
     * @ORM\Column(type="date", nullable=true)
     */
    protected $term;     
    
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
     * @ORM\Column(type="string", length=2, nullable=true)
     */
    private $discount_type;     
    
    /**
     * @var string
     * @ORM\Column(name="reference", type="string", length=25, nullable=true)
     */
    private $reference;    
    
    /**
     * @var string
     * @ORM\Column(type="integer", nullable=true)
     */
    private $invoice_number;     
    
    /**
     * @ORM\Column(type="date", nullable=true)
     */
    protected $paiement;   
    
    /**
     * @ORM\Column(name="reference_paiement", type="string", length=50, nullable=true)
     */
    protected $reference_paiement;     
    
    /**
     * @var string
     *
     * @ORM\Column(name="status", type="integer")
     */
    private $status = self::STATUS_NEW;    
         
    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\InvoiceLine", mappedBy="invoice", cascade={"persist", "remove"}, orphanRemoval=true)
     * @ORM\OrderBy({"id" = "ASC"})
     */
    protected $lines;  
    
    /**
     * @var ArrayCollection
     * 
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Reminder", mappedBy="invoice", cascade={"remove"})
     */
    protected $reminders;     

    public function __construct()
    {
        $this->created = new \DateTime();
        $this->term = new \DateTime('+1 month');   
        $this->lines = new ArrayCollection();
        $this->reminders = new ArrayCollection();
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
     * @return Invoice
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
     * Set term
     *
     * @param \DateTime $term
     *
     * @return Invoice
     */
    public function setTerm($term)
    {
        $this->term = $term;

        return $this;
    }

    /**
     * Get term
     *
     * @return \DateTime
     */
    public function getTerm()
    {
        return $this->term;
    }

    /**
     * Set total
     *
     * @param string $total
     *
     * @return Invoice
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
     * Set discount
     *
     * @param string $discount
     *
     * @return Invoice
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
     * @return Invoice
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
     * Set discountType
     *
     * @param string $discountType
     *
     * @return Invoice
     */
    public function setDiscountType($discountType)
    {
        $this->discount_type = $discountType;

        return $this;
    }

    /**
     * Get discountType
     *
     * @return string
     */
    public function getDiscountType()
    {
        return $this->discount_type;
    }    

    /**
     * Set reference
     *
     * @param string $reference
     *
     * @return Invoice
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
     * Set invoiceNumber
     *
     * @param integer $invoiceNumber
     *
     * @return Invoice
     */
    public function setInvoiceNumber($invoiceNumber)
    {
        $this->invoice_number = $invoiceNumber;

        return $this;
    }

    /**
     * Get invoiceNumber
     *
     * @return integer
     */
    public function getInvoiceNumber()
    {
        return $this->invoice_number;
    }
    
    /**
     * Set paiement
     *
     * @param \DateTime $paiement
     *
     * @return Invoice
     */
    public function setReferencePaiement($reference_paiement)
    {
        $this->reference_paiement = $reference_paiement;

        return $this;
    }  
    
    public function getReferencePaiement()
    {
        return $this->reference_paiement;
    }    
    
    

    /**
     * Set paiement
     *
     * @param \DateTime $paiement
     *
     * @return Invoice
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
     * @return Invoice
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
     * @return Invoice
     */
    public function setUser(\AppBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \AppBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }
    
    /**
     * Set Location
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
    
    public function getName()
    {
        return $this->getCustomer()->getFirstname().' '.$this->getCustomer()->getLastname();
    }   
    

    /**
     * Add line
     *
     * @param \AppBundle\Entity\InvoiceLine $line
     *
     * @return Invoice
     */
    public function addLine(\AppBundle\Entity\InvoiceLine $line)
    {
        $this->lines[] = $line;
        $line->setInvoice($this);        

        return $this;
    }

    /**
     * Remove line
     *
     * @param \AppBundle\Entity\InvoiceLine $line
     */
    public function removeLine(\AppBundle\Entity\InvoiceLine $line)
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

    /**
     * Add reminder
     *
     * @param \AppBundle\Entity\Reminder $reminder
     *
     * @return Invoice
     */
    public function addReminder(\AppBundle\Entity\Reminder $reminder)
    {
        $this->reminders[] = $reminder;

        return $this;
    }

    /**
     * Remove reminder
     *
     * @param \AppBundle\Entity\Reminder $reminder
     */
    public function removeReminder(\AppBundle\Entity\Reminder $reminder)
    {
        $this->reminders->removeElement($reminder);
    }

    /**
     * Get reminders
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getReminders()
    {
        return $this->reminders;
    }
    
    public function __toString()
    {
        return 'INVOICE('.(string)$this->getId().')'.(string)$this->getInvoiceNumber().' - '.$this->getCustomer()->getName();
    }  
    
    public function getTotalDiscount() 
    {
        if ($this->getDiscount() > 0) {
            return $this->getDiscount().' '.$this->getDiscountType();
        } else {
            return '';
        }
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
 * update `invoice` set customer_id = (select id from customer where customer.old = invoice.user_id and customer.old_location = invoice.location_id)
 */
