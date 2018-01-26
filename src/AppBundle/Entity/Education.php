<?php

    namespace AppBundle\Entity;
    use Doctrine\ORM\Mapping as ORM;
    use Doctrine\Common\Collections\ArrayCollection;
    use JMS\Serializer\SerializerBuilder;
    /**
    * @ORM\Entity
    * @ORM\Table(name="educations")
    * @ORM\HasLifecycleCallbacks
    */
    class Education
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
        * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Degree")
        * @ORM\JoinColumn(onDelete="CASCADE")
        */
        protected $degree;
        
        /**
        * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Year")
        * @ORM\JoinColumn(onDelete="CASCADE")
        */
        protected $year;
        
        /**
        * @ORM\ManyToOne(targetEntity="AppBundle\Entity\College")
        * @ORM\JoinColumn(onDelete="CASCADE")
        */
        protected $college;
        
        /**
        * @ORM\Column(type="string", length=255, nullable=true)
        */
        protected $other_college='';
        
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
          $this->degree = new ArrayCollection();
          $this->year = new ArrayCollection();
          $this->college = new ArrayCollection();
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
     * Set user
     *
     * @param \AppBundle\Entity\User $user
     *
     * @return Education
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

    /**
     * Set degree
     *
     * @param \AppBundle\Entity\Degree $degree
     *
     * @return Education
     */
    public function setDegree(\AppBundle\Entity\Degree $degree = null)
    {
        $this->degree = $degree;

        return $this;
    }

    /**
     * Get degree
     *
     * @return \AppBundle\Entity\Degree
     */
    public function getDegree()
    {
        return $this->degree;
    }

    /**
     * Set year
     *
     * @param \AppBundle\Entity\Year $year
     *
     * @return Education
     */
    public function setYear(\AppBundle\Entity\Year $year = null)
    {
        $this->year = $year;

        return $this;
    }

    /**
     * Get year
     *
     * @return \AppBundle\Entity\Year
     */
    public function getYear()
    {
        return $this->year;
    }

    /**
     * Set college
     *
     * @param \AppBundle\Entity\College $college
     *
     * @return Education
     */
    public function setCollege(\AppBundle\Entity\College $college = null)
    {
        $this->college = $college;

        return $this;
    }

    /**
     * Get college
     *
     * @return \AppBundle\Entity\College
     */
    public function getCollege()
    {
        return $this->college;
    }
    /**
     * Set otherCollege
     *
     * @param string $otherCollege
     *
     * @return Education
     */
    public function setOtherCollege($otherCollege)
    {
        $this->other_college = $otherCollege;

        return $this;
    }

    /**
     * Get otherCollege
     *
     * @return string
     */
    public function getOtherCollege()
    {
        return $this->other_college;
    }

    /**
     * Set createdDate
     *
     * @param \DateTime $createdDate
     *
     * @return Education
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
     * @return Education
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
    
    public function saveEducationData($requestData, $userObj, $degreeObj, $yearObj, $collegeObj, $em, $controllerObj){
        $this->setUser($userObj);
        $this->setDegree($degreeObj);    
        $this->setCollege($collegeObj);    
        $this->setYear($yearObj);    
        $this->setCreatedDate(new \DateTime("now"));
        $this->setUpdatedDate(new \DateTime("now"));
		$em->persist($this);
		$em->flush();
        return $this->getId();
    }
    public function updateEducationData($eduObj, $degreeObj, $yearObj, $collegeObj, $em){
        if(count($degreeObj)> 0){
            $eduObj->setDegree($degreeObj);
        }
        if(count($yearObj)> 0){
            $eduObj->setYear($yearObj);
        }
        if(count($collegeObj)> 0){
            $eduObj->setCollege($collegeObj);
        }
        $eduObj->setUpdatedDate(new \DateTime("now"));
        $em->persist($eduObj);
        $em->flush();
    }
}
