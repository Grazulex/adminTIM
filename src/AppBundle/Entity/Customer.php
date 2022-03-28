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
 * @ORM\Table(name="customer")
 * @ORM\Entity
 */
class Customer
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
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Company", inversedBy="customers")
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

    /**
     * @var float
     * @ORM\Column(name="old_location", type="integer", nullable=true)
     */
    protected $old_Location=0;

    /**
     * @var string
     * @ORM\Column(type="string", length=8, nullable=true, name="locale")
     */
    protected $locale;

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
     * @ORM\Column(type="string", length=255, nullable=true, name="firstname_location")
     */
    protected $firstname_location;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true, name="lastname_location")
     */
    protected $lastname_location;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true, name="email")
     */
    protected $email;

    /**
     * @var string
     * @ORM\Column(name="vat", type="string", length=128, nullable=true)
     * @Assert\Length(
     *             min=3,
     *             max=128)
     */
    protected $vat;

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


    public function __construct()
    {
        $this->locale = 'en_GB';
        $this->country = 'BE';
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

    public function getInvoicename()
    {
        $name='';
        if ($this->getLastname() OR $this->getLastnameLocation()) {
            if ($this->getLastnameLocation()) {
                $name = $this->getLastnameLocation();
            } else {
                $name = $this->getLastname();
            }
            if ($this->getFirstnameLocation()) {
                $name = $name . ' ' . $this->getFirstnameLocation();
            } else {
                if ($this->getFirstname()) {
                    $name = $name . ' ' . $this->getFirstname();
                }
            }
        } else {
            if ($this->getFirstnameLocation()) {
                $name = $this->getFirstnameLocation();
            }else {
                if ($this->getFirstname()) {
                    $name = $this->getFirstname();
                }
            }
        }
        return $name;
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
            if ($this->getCity()) {
                return $fullname . ' | ' . trim($this->getAdress1() . ' ' . $this->getCity());
            } else {
                return $fullname;
            }
        } else {
            if ($fullname) {
                $fullname = $fullname.' ('.trim($this->getAdress1().' '.$this->getCity()).' | '.strtoupper($this->getCompany()->getName()).')';
            } else {
                $fullname = trim($this->getAdress1().' '.$this->getCity()).' | '.strtoupper($this->getCompany()->getName());
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

  
    function getOld() {
        return $this->old;
    }

    function setOld($old) {
        $this->old = $old;
    }

    /**
     * @return string
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * @param string $firstname
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;
    }

    /**
     * @return string
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * @param string $lastname
     */
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getVat()
    {
        return $this->vat;
    }

    /**
     * @param string $vat
     */
    public function setVat($vat)
    {
        $this->vat = $vat;
    }

    /**
     * @return string
     */
    public function getAdress1()
    {
        return $this->adress1;
    }

    /**
     * @param string $adress1
     */
    public function setAdress1($adress1)
    {
        $this->adress1 = $adress1;
    }

    /**
     * @return string
     */
    public function getAdress2()
    {
        return $this->adress2;
    }

    /**
     * @param string $adress2
     */
    public function setAdress2($adress2)
    {
        $this->adress2 = $adress2;
    }

    /**
     * @return string
     */
    public function getAdress3()
    {
        return $this->adress3;
    }

    /**
     * @param string $adress3
     */
    public function setAdress3($adress3)
    {
        $this->adress3 = $adress3;
    }

    /**
     * @return string
     */
    public function getZip()
    {
        return $this->zip;
    }

    /**
     * @param string $zip
     */
    public function setZip($zip)
    {
        $this->zip = $zip;
    }

    /**
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @param string $city
     */
    public function setCity($city)
    {
        $this->city = $city;
    }

    /**
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * @param string $country
     */
    public function setCountry($country)
    {
        $this->country = $country;
    }

    /**
     * @return float
     */
    public function getOldLocation()
    {
        return $this->old_Location;
    }

    /**
     * @param float $old_Location
     */
    public function setOldLocation($old_Location)
    {
        $this->old_Location = $old_Location;
    }

    /**
     * @return string
     */
    public function getFirstnameLocation()
    {
        return $this->firstname_location;
    }

    /**
     * @param string $firstname_location
     */
    public function setFirstnameLocation($firstname_location)
    {
        $this->firstname_location = $firstname_location;
    }

    /**
     * @return string
     */
    public function getLastnameLocation()
    {
        return $this->lastname_location;
    }

    /**
     * @param string $lastname_location
     */
    public function setLastnameLocation($lastname_location)
    {
        $this->lastname_location = $lastname_location;
    }

    /**
     * @return string
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * @param string $locale
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;
    }




    
}
/**
 * INSERT INTO `customer`(customer.`old`, customer.`company_id`, customer.`firstname`, customer.`lastname`, customer.`email`, customer.`vat`, customer.`adress1`, customer.`adress2`, customer.`adress3`, customer.`zip`, customer.`city`, customer.`country`, customer.`old_location`, customer.lastname_location, customer.firstname_location, customer.`locale`)
SELECT user.`id`, user.`company_id`,user.firstname, user.lastname , user.`email`, location.vat, location.adress1, location.adress2, location.adress3, location.zip, location.city, location.country, location.id, location.lastname, location.firstname, user.locale FROM `user` left join location on (location.user_id = user.id)
 */