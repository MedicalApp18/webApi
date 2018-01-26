<?php

    namespace AppBundle\Entity;
    use Doctrine\ORM\Mapping as ORM;
    /**
    * @ORM\Entity
    * @ORM\Table(name="user_documents")
    * @ORM\HasLifecycleCallbacks
    */
    class UserDocuments
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
        protected $orgFileName='';
        
        /**
        * @ORM\Column(type="string", length=200, nullable=true)
        */
        protected $fileName='';
        
        /**
        * @ORM\Column(type="string", length=200, nullable=true)
        */
        protected $fileType='';
        
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
    public function getUserId()
    {
        return $this->user_id;
    }

    /**
     * Set orgFileName
     *
     * @param string $orgFileName
     *
     * @return UserDocuments
     */
    public function setOrgFileName($orgFileName)
    {
        $this->orgFileName = $orgFileName;

        return $this;
    }

    /**
     * Get orgFileName
     *
     * @return string
     */
    public function getOrgFileName()
    {
        return $this->orgFileName;
    }

    /**
     * Set fileName
     *
     * @param string $fileName
     *
     * @return UserDocuments
     */
    public function setFileName($fileName)
    {
        $this->fileName = $fileName;

        return $this;
    }

    /**
     * Get fileName
     *
     * @return string
     */
    public function getFileName()
    {
        return $this->fileName;
    }

    /**
     * Set fileType
     *
     * @param string $fileType
     *
     * @return UserDocuments
     */
    public function setFileType($fileType)
    {
        $this->fileType = $fileType;

        return $this;
    }

    /**
     * Get fileType
     *
     * @return string
     */
    public function getFileType()
    {
        return $this->fileType;
    }

    /**
     * Set createdDate
     *
     * @param \DateTime $createdDate
     *
     * @return UserDocuments
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
     * Get user
     *
     * @return \AppBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }
    
    public function saveFileData($data, $userObj, $em, $controllerObj){
        $this->setUser($userObj);
        $this->setOrgFileName($data['orgFileName']);    
        $this->setFileName($data['fileName']);    
        $this->setFileType($data['fileType']);    
        $this->setCreatedDate(new \DateTime("now"));
        $em->persist($this);
		$em->flush();
        return $this->getId();
    }
}
