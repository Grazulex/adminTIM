<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Gedmo\Mapping\Annotation as Gedmo;


/**
 * Logistic
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class LogisticLine
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
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Logistic", inversedBy="lines")
     */
    protected $logistic;    
    
    
    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true, name="comment")
     */
    protected $comment;  
    
    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Item", inversedBy="logistics")
     *
     * @var AppBundle\Entity\Item;
     */
    protected $item;        
    
    /**
     * @var float
     * @ORM\Column(name="controle", type="string", length=20 , nullable=false)
     */
    private $controle=0;  
    
    /**
     * @var string
     * @ORM\Column(type="smallint", nullable=false)
     */
    private $quantity = 1;  
    
    /**
     * @var float
     * @ORM\Column(name="checked", type="boolean")
     */
    private $checked = false;      
    
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
     * Set comment
     *
     * @param string $comment
     *
     * @return InvoiceLine
     */
    public function setComment($comment)
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * Get comment
     *
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * Set controle
     *
     * @param string $controle
     *
     * @return LogisticLine
     */
    public function setControle($controle)
    {
        $this->controle = $controle;

        return $this;
    }

    /**
     * Get check
     *
     * @return string
     */
    public function getControle()
    {
        return $this->controle;
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
     * Set logistic
     *
     * @param \AppBundle\Entity\Logistic $logistic
     *
     * @return LogisticLine
     */
    public function setLogistic(\AppBundle\Entity\Logistic $logistic = null)
    {
        $this->logistic = $logistic;

        return $this;
    }

    /**
     * Get logistic
     *
     * @return \AppBundle\Entity\Logistic
     */
    public function getLogistic()
    {
        return $this->logistic;
    }
    
    /**
     * Add logistic
     *
     * @param \AppBundle\Entity\Logistic $logistic
     *
     * @return LogisticLine
     */
    public function addLogistic(\AppBundle\Entity\Logistic $logistic)
    {
        $this->logistic[] = $logistic;

        return $this;
    }

    /**
     * Remove invoice
     *
     * @param \AppBundle\Entity\Logistic $logistic
     */
    public function removeLogistic(\AppBundle\Entity\Logistic $logistic)
    {
        $this->logistic->removeElement($logistic);
    }    

    function getChecked() {
        return $this->checked;
    }

    function setChecked($checked) {
        $this->checked = $checked;
    }

    /**
     * Set item
     *
     * @param \AppBundle\Entity\Item $item
     *
     * @return LogisticLine
     */
    public function setItem(\AppBundle\Entity\Item $item = null)
    {
        $this->item = $item;

        return $this;
    }

    /**
     * Get item
     *
     * @return \AppBundle\Entity\Item
     */
    public function getItem()
    {
        return $this->item;
    }
    
    public function __toString()
    {
        if ($this->comment) {
            if ($this->controle) {
                return $this->quantity.' X '.$this->item->getName().' ('.$this->controle.' - '.$this->comment.')';
            } else {
                return $this->quantity.' X '.$this->item->getName().' ('.$this->comment.')';
            }
        } else {
            if ($this->controle) {
                return $this->quantity.' X '.$this->item->getName().' ('.$this->controle.')';
            } else {
                return $this->quantity.' X '.$this->item->getName();    
            }
        }
    }    
    
}
