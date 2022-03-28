<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Logistic
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="AppBundle\Repository\LogisticRepository")
 */
class Logistic {

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
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\ItemCategory", inversedBy="logistics")
     *
     * @var AppBundle\Entity\ItemCategory;
     */
    protected $category;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $created;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $checked;
    
    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\LogisticLine", mappedBy="logistic", cascade={"persist", "remove"})
     * @ORM\OrderBy({"id" = "ASC"})
     */
    protected $lines;   
    
    /**
     * @var float
     * @ORM\Column(name="invoiced", type="boolean", nullable=true)
     */
    private $invoiced = false;      


    public function __construct() {
        $this->created = new \DateTime();
        $this->lines = new ArrayCollection();
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     *
     * @return Cash
     */
    public function setCreated($created) {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime
     */
    public function getCreated() {
        return $this->created;
    }

    function getUser() {
        return $this->user;
    }

    function getCategory() {
        return $this->category;
    }

    function getChecked() {
        return $this->checked;
    }


    function setUser(\AppBundle\Entity\User $user = null) {
        $this->user = $user;

        return $this;
    }

    function setCategory(\AppBundle\Entity\ItemCategory $category = null) {
        $this->category = $category;

        return $this;
    }

    function setChecked($checked) {
        $this->checked = $checked;

        return $this;
    }

    
    /**
     * Add line
     *
     * @param \AppBundle\Entity\LogisticLine $line
     *
     * @return Logistic
     */
    public function addLine(\AppBundle\Entity\LogisticLine $line)
    {
        $this->lines[] = $line;
        $line->setLogistic($this); 

        return $this;
    }

    /**
     * Remove line
     *
     * @param \AppBundle\Entity\LogisticLine $line
     */
    public function removeLine(\AppBundle\Entity\LogisticLine $line)
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
    
    public function __toString()
    {
        return 'LOGISTIC('.(string)$this->getId().')'.(string)$this->getCategory()->getName().' - '.$this->getCustomer()->getName();
    }      
    
    function getInvoiced() {
        return $this->invoiced;
    }

    function setInvoiced($invoiced) {
        $this->invoiced = $invoiced;
    }
    
    public function getTotalLines(){
        return count($this->getLines());
    }

    /**
     * @return AppBundle\Entity\User
     */
    public function getCustomer()
    {
        return $this->customer;
    }

    /**
     * @param AppBundle\Entity\User $customer
     */
    public function setCustomer($customer)
    {
        $this->customer = $customer;
    }
    
    
}

/**
 * update `logistic` set customer_id = (select id from customer where customer.old = logistic.user_id order by customer.id desc limit 1)
 */