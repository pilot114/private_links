<?php

namespace App\Entities;

/**
 * Resource
 */
class Resource
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var array
     */
    private $links;

    /**
     * @var string
     */
    private $generate;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $accesses;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->accesses = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set links.
     *
     * @param array $links
     *
     * @return Resource
     */
    public function setLinks($links)
    {
        $this->links = $links;

        return $this;
    }

    /**
     * Get links.
     *
     * @return array
     */
    public function getLinks()
    {
        return $this->links;
    }

    /**
     * Set generate.
     *
     * @param string $generate
     *
     * @return Resource
     */
    public function setGenerate($generate)
    {
        $this->generate = $generate;

        return $this;
    }

    /**
     * Get generate.
     *
     * @return string
     */
    public function getGenerate()
    {
        return $this->generate;
    }

    /**
     * Add access.
     *
     * @param \App\Entities\Access $access
     *
     * @return Resource
     */
    public function addAccess(\App\Entities\Access $access)
    {
        $this->accesses[] = $access;

        return $this;
    }

    /**
     * Remove access.
     *
     * @param \App\Entities\Access $access
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeAccess(\App\Entities\Access $access)
    {
        return $this->accesses->removeElement($access);
    }

    /**
     * Get accesses.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAccesses()
    {
        return $this->accesses;
    }
    /**
     * @var string
     */
    private $name;


    /**
     * Set name.
     *
     * @param string $name
     *
     * @return Resource
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}