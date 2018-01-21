<?php

    namespace AppBundle\Entity;
    use Doctrine\ORM\Mapping as ORM;
    /**
    * @ORM\Entity
    * @ORM\Table(name="user_login")
    * @ORM\HasLifecycleCallbacks
    */
    class UserLogin
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
        * @ORM\Column(type="datetime")
        */
        protected $login_date;

    /**
     * Now we tell doctrine that before we persist or update we call the updatedTimestamps() function.
     *
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function updateTimestamps()
    {
        
        if($this->getLoginDate() == null)
        {
            $this->setLoginDate(new \DateTime());
        }
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
     * Set userId
     *
     * @param integer $userId
     *
     * @return UserLogin
     */
    public function setUserId($userId)
    {
        $this->user_id = $userId;

        return $this;
    }

    /**
     * Get userId
     *
     * @return integer
     */
    public function getUserId()
    {
        return $this->user_id;
    }

    /**
     * Set loginDate
     *
     * @param \DateTime $loginDate
     *
     * @return UserLogin
     */
    public function setLoginDate($loginDate)
    {
        $this->login_date = $loginDate;

        return $this;
    }

    /**
     * Get loginDate
     *
     * @return \DateTime
     */
    public function getLoginDate()
    {
        return $this->login_date;
    }
}
