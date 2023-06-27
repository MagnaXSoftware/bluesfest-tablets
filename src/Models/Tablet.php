<?php

namespace App\Models;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "tablets")]
class Tablet
{

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    #[ORM\Column(type: "integer")]
    private int $id;

    #[ORM\Column(type: "string")]
    private string $code;

    #[ORM\ManyToOne(targetEntity: Area::class, fetch: "EAGER", inversedBy: "tablets")]
    private Area $area;

    /**
     * @var ArrayCollection<int,TabletUpdate>
     */
    #[ORM\OneToMany(mappedBy: "tablet", targetEntity: TabletUpdate::class, cascade: ["persist"], fetch: "EAGER")]
    private $tabletUpdates;

    public function __construct()
    {
        $this->tabletUpdates = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getArea(): ?Area
    {
        return $this->area;
    }

    /**
     * @param Area|null $area
     * @return $this
     */
    public function setArea(?Area $area): Tablet
    {
        $this->area = $area;
        return $this;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * @param string $code
     * @return $this
     */
    public function setCode(string $code): Tablet
    {
        $this->code = $code;
        return $this;
    }

    /**
     * @return ArrayCollection<int,TabletUpdate>
     */
    public function getTabletUpdates()
    {
        return $this->tabletUpdates->matching((new Criteria())->orderBy(['update.created_at' => 'DESC']));
    }

    public function getLastUpdate(): ?TabletUpdate
    {
        $collection = $this->getTabletUpdates();
        return $collection->first() ? $collection->first() : null;

    }

}