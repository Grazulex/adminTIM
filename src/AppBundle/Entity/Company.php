<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Company
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Company
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
     * @var string
     * @ORM\Column(type="string", length=120, nullable=false, name="name")
     * @Assert\NotBlank()
     * @Assert\Length(
     *             min=2,
     *             max=120)
     */
    protected $name;    
    
    /**
     * @var string
     * @ORM\Column(type="string", length=120, nullable=true, name="password")
     * @Assert\Length(
     *             min=2,
     *             max=120)
     */
    protected $password;   
    
    /**
     * @var string
     * @ORM\Column(type="string", length=256, nullable=true, name="mx")
     */
    protected $mx;    
    

    /**
     * @var boolean
     * @ORM\Column(type="boolean")
     */    
    protected $enable;
    
    /**
     * @var ArrayCollection
     * 
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\User", mappedBy="company")
     */
    protected $users;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Customer", mappedBy="company")
     */
    protected $customers;

    /**
     * @var ArrayCollection
     * 
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Cash", mappedBy="company")
     */
    protected $cashs;      
    
    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->cashs = new ArrayCollection();
        $this->enable = true;
    }    
    
    function getId() {
        return $this->id;
    }

    function getName() {
        return $this->name;
    }

    function getPassword() {
        return $this->password;
    }

    function getEnable() {
        return $this->enable;
    }
    
    function getMx() {
        return $this->mx;
    }

    function setName($name) {
        $this->name = $name;
    }

    function setPassword($password) {
        $this->password = $password;
    }

    function setEnable($enable) {
        $this->enable = $enable;
    }
    
    function setMx($mx) {
        $this->mx = $mx;
    }

    /**
     * Add user
     *
     * @param \AppBundle\Entity\User $user
     * @return User
     */
    public function addUser(\AppBundle\Entity\User $user)
    {
        $this->users[] = $user;

        return $this;
    }

    /**
     * Remove user
     *
     * @param \AppBundle\Entity\User $user
     */
    public function removeUser(\AppBundle\Entity\User $user)
    {
        $this->users->removeElement($user);
    }

    /**
     * Get users
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getUsers()
    {
        return $this->users;
    }  
    
    public function __toString()
    {
        return $this->name;
    }
    
    /**
     * Add cashs
     *
     * @param \AppBundle\Entity\Cash $cash
     * @return Company
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
    
    public function getTotalUsers(){
        return count($this->getUsers());
    }      
    
}