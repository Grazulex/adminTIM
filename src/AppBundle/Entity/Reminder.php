<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;


/**
 * Company
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ReminderRepository")
 */
class Reminder
{
    
    const TYPE_FIRST      = 1;
    const TYPE_SECOND     = 2;
    const TYPE_THIRD      = 3; 
    const TYPE_FOUR       = 4; 
    
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Invoice", inversedBy="reminders")
     * @ORM\JoinColumn(name="invoice_id", referencedColumnName="id")
     *
     * @var AppBundle\Entity\Invoice;
     */
    protected $invoice; 
    
    /**
     * @ORM\Column(type="datetime")
     */
    protected $created; 

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="integer")
     */
    private $type = self::TYPE_FIRST;   
    
    public function __construct()
    {
        $this->created = new \DateTime();  
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
     * @return Reminder
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
     * Set type
     *
     * @param integer $type
     *
     * @return Reminder
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return integer
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set invoice
     *
     * @param \AppBundle\Entity\Invoice $invoice
     *
     * @return Reminder
     */
    public function setInvoice(\AppBundle\Entity\Invoice $invoice = null)
    {
        $this->invoice = $invoice;

        return $this;
    }

    /**
     * Get invoice
     *
     * @return \AppBundle\Entity\Invoice
     */
    public function getInvoice()
    {
        return $this->invoice;
    }
    
    /** {@inheritdoc} */
    public function __toString()
    {
        return $this->created->format('d-m-Y').' ('.$this->type.')';
    }
    
}
