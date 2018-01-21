<?php

    namespace AppBundle\Entity;
    use Doctrine\ORM\Mapping as ORM;
    /**
    * @ORM\Entity
    * @ORM\Table(name="user_otp")
    * @ORM\HasLifecycleCallbacks
    */
    class UserOtp
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
        protected $otp='';
        /**
        * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
        * @ORM\JoinColumn(onDelete="CASCADE")
        */
        protected $user;
        
        /**
        * @var datetime $created_date
        *
        * @ORM\Column(name="created_at", type="datetime", nullable=true)
        */
        private $created_date;
        
         /**
        * @var datetime $used_date
        *
        * @ORM\Column(name="used_date", type="datetime", nullable=true)
        */
        private $used_date;
        

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
     * @return Clinic
     */
    public function setUser(\AppBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get userId
     *
     * @return integer
     */
    //public function getUserId()
    //{
    //    return $this->user_id;
    //}

    /**
     * Set createdDate
     *
     * @param \DateTime $createdDate
     *
     * @return UserOtp
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
     * Set usedDate
     *
     * @param \DateTime $usedDate
     *
     * @return UserOtp
     */
    public function setUsedDate($usedDate)
    {
        $this->used_date = $usedDate;

        return $this;
    }

    /**
     * Get usedDate
     *
     * @return \DateTime
     */
    public function getUsedDate()
    {
        return $this->used_date;
    }

    /**
     * Set otp
     *
     * @param string $otp
     *
     * @return UserOtp
     */
    public function setOtp($otp)
    {
        $this->otp = $otp;

        return $this;
    }

    /**
     * Get otp
     *
     * @return string
     */
    public function getOtp()
    {
        return $this->otp;
    }
}
