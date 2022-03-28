<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;


/**
 * Company
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CashRepository")
 */
class Cash
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
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Company", inversedBy="cashs")
     *
     * @var AppBundle\Entity\Company;
     */
    protected $company;  
    
    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\ItemCategory", inversedBy="cashs")
     *
     * @var AppBundle\Entity\ItemCategory;
     */
    protected $category;    
    
    /**
     * @ORM\Column(type="datetime")
     */
    protected $created; 
    
    /**
     * @var float
     * @ORM\Column(name="total", type="decimal", scale=2)
     */
    private $total;     

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

    /**
     * Set total
     *
     * @param string $total
     *
     * @return Cash
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
     * Set company
     *
     * @param \AppBundle\Entity\Company $company
     *
     * @return Cash
     */
    public function setCompany(\AppBundle\Entity\Company $company = null)
    {
        $this->company = $company;

        return $this;
    }

    /**
     * Get company
     *
     * @return \AppBundle\Entity\Company
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * Set category
     *
     * @param \AppBundle\Entity\ItemCategory $category
     *
     * @return Cash
     */
    public function setCategory(\AppBundle\Entity\ItemCategory $category = null)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category
     *
     * @return \AppBundle\Entity\ItemCategory
     */
    public function getCategory()
    {
        return $this->category;
    }
    
    public function __toString()
    {
        return 'CASH('.(string)$this->getId().')'.$this->getCompany()->getName().' - '.$this->getCategory()->getName();
    }  
        
}
