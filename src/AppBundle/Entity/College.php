<?php

    namespace AppBundle\Entity;
    use Doctrine\ORM\Mapping as ORM;
    use Doctrine\Common\Collections\ArrayCollection;
    use JMS\Serializer\SerializerBuilder;
    /**
    * @ORM\Entity
    * @ORM\Table(name="college")
    * @ORM\HasLifecycleCallbacks
    */
    class College
    {
        /**
        * @ORM\Id
        * @ORM\Column(type="integer")
        * @ORM\GeneratedValue(strategy="AUTO")
        */
        protected $id;
        
        /**
        * @var string
        *
        * @ORM\Column(name="name", type="string", length=255, nullable=false)
        */
        private $name;
        
        /**
        * @var string
        *
        * @ORM\Column(name="state", type="string", length=255, nullable=false)
        */
        private $state;
    
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
     * Set name
     *
     * @param string $name
     *
     * @return Degree
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set state
     *
     * @param string $state
     *
     * @return College
     */
    public function setState($state)
    {
        $this->state = $state;

        return $this;
    }

    /**
     * Get state
     *
     * @return string
     */
    public function getState()
    {
        return $this->state;
    }
}
