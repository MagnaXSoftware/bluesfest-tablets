<?php

namespace App\Models;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;


#[ORM\Entity]
#[ORM\Table(name: "areas")]
class Area
{

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    #[ORM\Column(type: "integer")]
    protected int $id;

    #[ORM\Column(type: "string")]
    protected string $name;

    /**
     * @var ArrayCollection<int, Tablet>
     */
    #[ORM\OneToMany(mappedBy: "area", targetEntity: Tablet::class, fetch: "LAZY")]
    protected $tablets;

    public function __construct()
    {
        $this->tablets = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): Area
    {
        $this->name = $name;
        return $this;
    }

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