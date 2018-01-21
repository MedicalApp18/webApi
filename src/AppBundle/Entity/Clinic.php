<?php

    namespace AppBundle\Entity;
    use Doctrine\ORM\Mapping as ORM;
    use Doctrine\Common\Collections\ArrayCollection;
    use JMS\Serializer\SerializerBuilder;
    /**
    * @ORM\Entity
    * @ORM\Table(name="clinics")
    * @ORM\HasLifecycleCallbacks
    */
    class Clinic
    {
        /**
        * @ORM\Id
        * @ORM\Column(type="integer")
        * @ORM\GeneratedValue(strategy="AUTO")
        */
        protected $id;
        
        /**
        * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
        * @ORM\JoinColumn(onDelete="CASCADE")
        */
        protected $user;
        
        /**
        * @ORM\Column(type="string", columnDefinition="enum('', 'Own', 'Consultant')")
        */
        protected $type = '';
        
        /**
        * @ORM\Column(type="string", length=100, nullable=true)
        */
        protected $clinicName='';
        
        /**
        * @ORM\Column(type="string", length=100, nullable=true)
        */
        protected $clinicNumber='';
        
        /**
        * @ORM\Column(type="integer", nullable=true)
        */
        protected $fees='';
        
        /**
        * @ORM\ManyToOne(targetEntity="AppBundle\Entity\City")
        * @ORM\JoinColumn(onDelete="CASCADE")
        */
        protected $city;
        
        /**
        * @ORM\Column(type="string", length=255, nullable=true)
        */
        protected $location='';
        
        /**
        * @var datetime $created_date
        *
        * @ORM\Column(type="datetime", nullable=true)
        */
        private $created_date;
        
        /**
        * @var datetime $updated_date
        *
        * @ORM\Column(type="datetime", nullable=true)
        */
        private $updated_date;
        
        /**
        * Constructor
        */
       public function __construct()
       {
          $this->user = new ArrayCollection();
          $this->city = new ArrayCollection();
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
     * Set type
     *
     * @param string $type
     *
     * @return Clinic
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set clinicName
     *
     * @param string $clinicName
     *
     * @return Clinic
     */
    public function setClinicName($clinicName)
    {
        $this->clinicName = $clinicName;

        return $this;
    }

    /**
     * Get clinicName
     *
     * @return string
     */
    public function getClinicName()
    {
        return $this->clinicName;
    }

    /**
     * Set clinicNumber
     *
     * @param string $clinicNumber
     *
     * @return Clinic
     */
    public function setClinicNumber($clinicNumber)
    {
        $this->clinicNumber = $clinicNumber;

        return $this;
    }

    /**
     * Get clinicNumber
     *
     * @return string
     */
    public function getClinicNumber()
    {
        return $this->clinicNumber;
    }

    /**
     * Set fees
     *
     * @param integer $fees
     *
     * @return Clinic
     */
    public function setFees($fees)
    {
        $this->fees = $fees;

        return $this;
    }

    /**
     * Get fees
     *
     * @return integer
     */
    public function getFees()
    {
        return $this->fees;
    }

    /**
     * Set city
     *
     * @param integer $city
     *
     * @return Clinic
     */
    public function setCity($city)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Get city
     *
     * @return integer
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set location
     *
     * @param string $location
     *
     * @return Clinic
     */
    public function setLocation($location)
    {
        $this->location = $location;

        return $this;
    }

    /**
     * Get location
     *
     * @return string
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * Set createdDate
     *
     * @param \DateTime $createdDate
     *
     * @return Clinic
     */
    public function setCreatedDate($createdDate)
    {
        $this->created_date = $createdDate;

        return $this;
    }

    /**
     * Get createdDate
     *
     * @return \DateTime
     */
    public function getCreatedDate()
    {
        return $this->created_date;
    }

    /**
     * Set updatedDate
     *
     * @param \DateTime $updatedDate
     *
     * @return Clinic
     */
    public function setUpdatedDate($updatedDate)
    {
        $this->updated_date = $updatedDate;

        return $this;
    }

    /**
     * Get updatedDate
     *
     * @return \DateTime
     */
    public function getUpdatedDate()
    {
        return $this->updated_date;
    }

    /**
     * Set user
     *
     * @param \AppBundle\Entity\User $user
     *
     * @return Clinic
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
    //public function getUser()
    //{
    //    return $this->user;
    //}
    
    public function saveClinic($requestData, $userObj, $em){
        $this->setType($requestData['type']);
        $this->setClinicName($requestData['clinicName']);
        $this->setClinicNumber($requestData['clinicNumber']);
        $this->setFees($requestData['fees']);
        $this->setCity($requestData['city']);
        $this->setLocation($requestData['location']);
        $this->setCreatedDate(new \DateTime("now"));
        $this->setUpdatedDate(new \DateTime("now"));
        $this->setUser($userObj);
        $em->persist($this);
        $em->flush();
    }
    
    public function getClinicDetails($userId, $id, $em){
        $query = $em->createQuery(
            'SELECT c
            FROM AppBundle:Clinic c
            WHERE c.user = :user
            AND c.id = :id'
        )->setParameter('user', $userId)
        ->setParameter('id', $id);
        $clinic = $query->getResult();
        return $clinic;
    }
}