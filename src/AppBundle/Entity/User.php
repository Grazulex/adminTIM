<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Sonata\UserBundle\Entity\BaseUser as BaseUser;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * User
 *
 * @ORM\Table(name="user",indexes={@ORM\Index(name="username_idx", columns={"username"})})
 * @ORM\Entity
 * @UniqueEntity(fields="usernameCanonical", errorPath="username", message="fos_user.username.already_used")
 * @ORM\AttributeOverrides({
 *      @ORM\AttributeOverride(name="email", column=@ORM\Column(type="string", name="email", length=255, unique=false, nullable=true)),
 *      @ORM\AttributeOverride(name="emailCanonical", column=@ORM\Column(type="string", name="email_canonical", length=255, unique=false, nullable=true))
 * })
 */
class User extends BaseUser
{
    
    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Location", mappedBy="user", cascade={"persist", "remove"})
     */
    protected $locations;     
    
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    
    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Company", inversedBy="users")
     * @ORM\JoinColumn(name="company_id", referencedColumnName="id", nullable=false)
     *
     * @var AppBundle\Entity\Company;
     */
    protected $company;  
    
    /**
     * @var float
     * @ORM\Column(name="old", type="integer", nullable=false)
     */    
    protected $old=0;
    
    public function __construct()
    {
        parent::__construct();
        $this->locations = new ArrayCollection();
        $this->locale = 'en_GB';
        $this->timezone = 'Europe/Brussels';   
        $this->enabled = true;
        $this->addRole('ROLE_USER');
        $this->setPlainPassword('123123');
    }       
   
    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }   
        
    public function getName()
    {   
        $name='';
        if ($this->getLastname()) {
            $name = $this->getLastname(); 
            if ($this->getFirstname()) {
                $name = $name.' '.$this->getFirstname();
            }
        } else {
            if ($this->getFirstname()) {
                $name= $this->getFirstname();
            }
        }
        return $name;
    }    
    
    public function getFullname()
    {
        $fullname = $this->getName();
        if ($this->getCompany()->getId() === 15) {
            return $fullname;
        } else {
            if ($fullname) {
                $fullname = $fullname.' ('.$this->getCompany()->getName().')';
            } else {
                $fullname = $this->getCompany()->getName();
            }
        }
        return $fullname;
        
    }      
    
    public function __toString()
    {
        return $this->getName();
    }    
    
    /**
     * Set user
     *
     * @param \AppBundle\Entity\Company $company
     * @return Company
     */
    public function setCompany(\AppBundle\Entity\Company $company = null)
    {
        $this->company = $company;

        return $this;
    }

    /**
     * Get user
     *
     * @return \AppBundle\Entity\Company 
     */
    public function getCompany()
    {
        return $this->company;
    }    
    
    /**
     * Add location
     *
     * @param \AppBundle\Entity\Location $location
     *
     * @return User
     */
    public function addLocation(\AppBundle\Entity\Location $location)
    {
        $this->locations[] = $location;
        $location->setUser($this); 

        return $this;
    }

    /**
     * Remove Location
     *
     * @param \AppBundle\Entity\Location $location
     */
    public function removeLocation(\AppBundle\Entity\Location $location)
    {
        $this->locations->removeElement($location);
    }

    /**
     * Get lines
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getLocations()
    {
        return $this->locations;
    }
  
    function getOld() {
        return $this->old;
    }

    function setOld($old) {
        $this->old = $old;
    }

    
}
