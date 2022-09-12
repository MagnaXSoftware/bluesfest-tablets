<?php

namespace App\Models;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Entity
 * @ORM\Table(name="areas")
 */
class Area
{

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    protected int $id;
    /**
     * @ORM\Column(type="string")
     */
    protected string $name;
    /**
     * @ORM\OneToMany(targetEntity="Tablet", mappedBy="area", fetch="LAZY")
     * @var ArrayCollection<int, Tablet>
     */
    protected $tablets;

    public function __construct()
    {
        $this->tablets = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return Area
     */
    public function setName(string $name): Area
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return int
     */
    public function getExpected(): ?int
    {
        return count($this->getTablets());
    }

    /**
     * @return ArrayCollection<int, Tablet>
     */
    public function getTablets()
    {
        return $this->tablets;
    }

}