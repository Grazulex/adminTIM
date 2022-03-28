<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;


/**
 * Erase
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="AppBundle\Repository\EraseRepository")
 */
class Erase
{    
    const TYPE_INVOICE    = 0;
    const TYPE_CREDIT     = 1;
    
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    /**
     * @ORM\Column(type="datetime")
     */
    protected $created; 
    
    /**
     * @var float
     * @ORM\Column(name="type", type="integer")
     */
    private $type;  
    
    /**
     * @var float
     * @ORM\Column(name="number", type="integer")
     */
    private $number;     

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
     * @return Cash
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
    
    function getType() {
        return $this->type;
    }

    function getNumber() {
        return $this->number;
    }

    function setType($type) {
        $this->type = $type;
    }

    function setNumber($number) {
        $this->number = $number;
    }

    
}
