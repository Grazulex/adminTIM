<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;


/**
 * Location
 *
 * @ORM\Table()
 * @ORM\Entity()
 */
class Location {
    
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;    
    
    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="locations")
     */
    protected $user;    

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true, name="firstname")
     */
    protected $firstname;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true, name="lastname")
     */
    protected $lastname;      
    
    /**
     * @var string
     * @ORM\Column(name="vat", type="string", length=128, nullable=true)
     * @Assert\Length(
     *             min=3,
     *             max=128)
     */
    private $vat;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true, name="adress1")
     * @Assert\Length(
     *             min=3,
     *             max=255)
     */
    protected $adress1;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true, name="adress2")
     */
    protected $adress2;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true, name="adress3")
     */
    protected $adress3;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true, name="zip")
     * @Assert\Length(
     *             min=3,
     *             max=255)
     */
    protected $zip;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true, name="city")
     * @Assert\Length(
     *             min=3,
     *             max=255)
     */
    protected $city;
    
    /**
     * @var string
     * @ORM\Column(type="string", length=5, nullable=true, name="country")
     */
    protected $country;
    
    function getId() {
        return $this->id;
    }    
    
    /**
     * Set firstname
     *
     * @param string $firstname
     *
     * @return User
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;

        return $this;
    }

    /**
     * Get firstname
     *
     * @return string
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * Set lastname
     *
     * @param string $lastname
     *
     * @return User
     */
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;

        return $this;
    }

    /**
     * Get lastname
     *
     * @return string
     */
    public function getLastname()
    {
        return $this->lastname;
    }        
    
    /**
     * Set user
     *
     * @param \AppBundle\Entity\User $user
     *
     * @return Location
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

    function getVat() {
        return $this->vat;
    }

    function getAdress1() {
        return $this->adress1;
    }

    function getAdress2() {
        return $this->adress2;
    }

    function getAdress3() {
        return $this->adress3;
    }

    function getZip() {
        return $this->zip;
    }

    function getCity() {
        return $this->city;
    }

    function getCountry() {
        return $this->country;
    }

    function setVat($vat) {
        $this->vat = $vat;
    }

    function setAdress1($adress1) {
        $this->adress1 = $adress1;
    }

    function setAdress2($adress2) {
        $this->adress2 = $adress2;
    }

    function setAdress3($adress3) {
        $this->adress3 = $adress3;
    }

    function setZip($zip) {
        $this->zip = $zip;
    }

    function setCity($city) {
        $this->city = $city;
    }

    function setCountry($country) {
        $this->country = $country;
    }
    
    public function __toString()
    {
        return $this->adress1.' '.$this->adress2.' ('.$this->zip.' '.$this->city.')'.$this->vat;
    }        
    
}