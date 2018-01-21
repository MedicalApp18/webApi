<?php

    namespace AppBundle\Entity;
    
    use \FOS\UserBundle\Model\User as BaseUser;
    use Doctrine\ORM\Mapping as ORM;
    use Symfony\Component\Validator\Constraints as Assert;
    use Symfony\Component\HttpFoundation\File\UploadedFile;
    use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
    use Symfony\Bundle\FrameworkBundle\Controller\Controller;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
    use Symfony\Component\Filesystem\Filesystem;
    use Symfony\Component\Filesystem\Exception\IOException;
    use Symfony\Component\HttpFoundation\Cookie;
    use Symfony\Component\HttpFoundation\RedirectResponse;
    use Symfony\Components\CssSelector\Parser;
    use Symfony\Component\Serializer\Serializer;
    use Symfony\Component\Serializer\Encoder\XmlEncoder;
    use Symfony\Component\Serializer\Encoder\JsonEncoder;
    use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
    use Lsw\ApiCallerBundle\Call\HttpGetJson;
    use Doctrine\Common\Collections\ArrayCollection;
    use \DateTime;
    /**
    * @ORM\Entity
    * @ORM\Table(name="fos_user")
    */
    class User extends BaseUser
    {
        /**
        * @ORM\Id
        * @ORM\Column(type="integer")
        * @ORM\GeneratedValue(strategy="AUTO")
        */
       protected $id;
       
        /**
        * @ORM\Column(type="string", length=200, nullable=true)
        */
        protected $fullName='';
        
        /**
        * @ORM\Column(type="string", length=20, nullable=true)
        */
        protected $date_format='m/d/Y';
        
        /**
        * @var \DateTime
        *
        * @ORM\Column(name="date_created", type="datetime", nullable=true)
        */
        private $date_created;
        
        /**
        * @var \DateTime
        *
        * @ORM\Column(name="date_modified", type="datetime", nullable=true)
        */
        protected $date_modified;
        
        /**
        * @ORM\Column(type="string", length=50, nullable=true)
        */
        protected $mobileNumber='';
        
        /**
        * @ORM\Column(type="string", length=50, nullable=true)
        */
        protected $telephone='';
        
        /**
        * @ORM\Column(type="integer", nullable=true)
        */
        protected $city_id=NULL;
        
        /**
        * @ORM\Column(type="date", nullable=true)
        */
        protected $birth_date=NULL;
        
        /**
        * @ORM\Column(type="string", columnDefinition="enum('', 'Male', 'Female')")
        */
        protected $gender = '';
        /**
        * @Assert\File(maxSize="50M")
        */
        private $file;
        
        /**
        * @ORM\Column(type="string", length=255, nullable=true)
        */
        protected $profilePhoto;
        
        /**
        * @ORM\OneToMany(targetEntity="AppBundle\Entity\Clinic", mappedBy="user")
        * @var Clinic[]
        */
        private $clinics;
        
        /**
        * @ORM\Column(type="string", length=100, nullable=true)
        */
        protected $totalExp='';
        
        /**
        * @ORM\Column(type="text", nullable=true)
        */
        protected $aboutMe;

        public function __construct()
        {
            parent::__construct();
            $this->clinics = new ArrayCollection();
            // your own logic
        }
        /**
        * @ORM\PrePersist
        */
        public function setCreated()
        {
            $this->setDateCreated(new \DateTime());
        }
       
        /**
        * @ORM\PrePersist
        * @ORM\PreUpdate
        */
        public function setUpdated()
        {
            $this->setDateModified(new \DateTime());
        }

    /**
     * Set dateFormat
     *
     * @param string $dateFormat
     *
     * @return User
     */
    public function setDateFormat($dateFormat)
    {
        $this->date_format = $dateFormat;

        return $this;
    }

    /**
     * Get dateFormat
     *
     * @return string
     */
    public function getDateFormat()
    {
        return $this->date_format;
    }

    /**
     * Set telephone
     *
     * @param string $telephone
     *
     * @return User
     */
    public function setTelephone($telephone)
    {
        $this->telephone = $telephone;

        return $this;
    }

    /**
     * Get telephone
     *
     * @return string
     */
    public function getTelephone()
    {
        return $this->telephone;
    }

    /**
     * Set profilePhoto
     *
     * @param string $profilePhoto
     *
     * @return User
     */
    public function setProfilePhoto($profilePhoto)
    {
        $this->profilePhoto = $profilePhoto;

        return $this;
    }

    /**
     * Get profilePhoto
     *
     * @return string
     */
    public function getProfilePhoto()
    {
        return $this->profilePhoto;
    }
    
    /**
     * Set mobileNumber
     *
     * @param string $mobileNumber
     *
     * @return User
     */
    public function setMobileNumber($mobileNumber)
    {
        $this->mobileNumber = $mobileNumber;

        return $this;
    }

    /**
     * Get mobileNumber
     *
     * @return string
     */
    public function getMobileNumber()
    {
        return $this->mobileNumber;
    }

    /**
     * Set fullName
     *
     * @param string $fullName
     *
     * @return User
     */
    public function setFullName($fullName)
    {
        $this->fullName = $fullName;

        return $this;
    }

    /**
     * Get fullName
     *
     * @return string
     */
    public function getFullName()
    {
        return $this->fullName;
    }

    /**
     * Set cityId
     *
     * @param integer $cityId
     *
     * @return User
     */
    public function setCityId($cityId)
    {
        $this->city_id = $cityId;

        return $this;
    }

    /**
     * Get cityId
     *
     * @return integer
     */
    public function getCityId()
    {
        return $this->city_id;
    }
    
    /**
     * Set birthDate
     *
     * @param \DateTime $birthDate
     *
     * @return User
     */
    public function setBirthDate($birthDate)
    {
        $this->birth_date = $birthDate;

        return $this;
    }

    /**
     * Get birthDate
     *
     * @return \DateTime
     */
    public function getBirthDate()
    {
        return $this->birth_date;
    }

    /**
     * Set gender
     *
     * @param string $gender
     *
     * @return User
     */
    public function setGender($gender)
    {
        $this->gender = $gender;

        return $this;
    }

    /**
     * Get gender
     *
     * @return string
     */
    public function getGender()
    {
        return $this->gender;
    }
    
    
    public function updateData($data, $userObj, $em, $controllerObj){
        if(isset($data['fullName'])){
            $userObj->setFullName($data['fullName']);
        }
        if(isset($data['gender'])){
            $userObj->setGender($data['gender']);
        }
        if(isset($data['dateOfBirth'])){
            $userObj->setBirthDate(new \DateTime($data['dateOfBirth']));
        }
        if(isset($data['cityId'])){
            $userObj->setCityId($data['cityId']);
        }
        if(isset($data['aboutMe'])){
            $userObj->setAboutMe($data['aboutMe']);
        }
        if(isset($data['totalExp'])){
            $userObj->setTotalExp($data['totalExp']);
        }
        if(isset($data['fileData'])){
            $userObj->setProfilePhoto($data['fileData']);
        }
        //$userObj->setEnabled($requestData['enabled']);
        //$userObj->setEmail($requestData['email']);
        //$userObj->setUsername($requestData['username']);
        if(isset($data['password'])){
            $factory = $controllerObj->get('security.encoder_factory');
            $encoder = $factory->getEncoder($this);
            $password = $encoder->encodePassword($data['password'], $this->getSalt());
            $userObj->setPassword($password);
        }
        $userObj->setDateModified(new \DateTime("now"));
        $em->persist($userObj);
        $em->flush();
    }

    /**
     * Add clinic
     *
     * @param \App\Entity\Clinic $clinic
     *
     * @return User
     */
    //public function addClinic(\App\Entity\Clinic $clinic)
    //{
    //    $this->clinics[] = $clinic;
    //
    //    return $this;
    //}

    /**
     * Remove clinic
     *
     * @param \AppBundle\Entity\Clinic $clinic
     */
    public function removeClinic(\AppBundle\Entity\Clinic $clinic)
    {
        $this->clinics->removeElement($clinic);
    }

    /**
     * Get clinics
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getClinics()
    {
        return $this->clinics;
    }

    /**
     * Set dateCreated
     *
     * @param \DateTime $dateCreated
     *
     * @return User
     */
    public function setDateCreated($dateCreated)
    {
        $this->date_created = $dateCreated;

        return $this;
    }

    /**
     * Get dateCreated
     *
     * @return \DateTime
     */
    public function getDateCreated()
    {
        return $this->date_created;
    }

    /**
     * Set dateModified
     *
     * @param \DateTime $dateModified
     *
     * @return User
     */
    public function setDateModified($dateModified)
    {
        $this->date_modified = $dateModified;

        return $this;
    }

    /**
     * Get dateModified
     *
     * @return \DateTime
     */
    public function getDateModified()
    {
        return $this->date_modified;
    }

    /**
     * Set totalExp
     *
     * @param string $totalExp
     *
     * @return User
     */
    public function setTotalExp($totalExp)
    {
        $this->totalExp = $totalExp;

        return $this;
    }

    /**
     * Get totalExp
     *
     * @return string
     */
    public function getTotalExp()
    {
        return $this->totalExp;
    }

    /**
     * Set aboutMe
     *
     * @param string $aboutMe
     *
     * @return User
     */
    public function setAboutMe($aboutMe)
    {
        $this->aboutMe = $aboutMe;

        return $this;
    }

    /**
     * Get aboutMe
     *
     * @return string
     */
    public function getAboutMe()
    {
        return $this->aboutMe;
    }
}
