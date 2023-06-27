<?php

namespace App\Models;

use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "updates")]
class Update
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    #[ORM\Column(type: "integer")]
    private int $id;

    #[ORM\ManyToOne(targetEntity: Area::class)]
    private ?Area $area;

    /**
     * @var ArrayCollection<int,TabletUpdate>
     */
    #[ORM\OneToMany(mappedBy: "update", targetEntity: "TabletUpdate", cascade: ["persist"], fetch: "LAZY")]
    private $tabletUpdates;

    #[ORM\Column(type: "string")]
    private string $actor;

    #[ORM\Column(type: "string", nullable: true)]
    private ?string $notes;

    #[ORM\Column(type: "datetime_immutable")]
    private DateTimeImmutable $created_at;

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
    public function setArea(?Area $area): Update
    {
        $this->area = $area;
        return $this;
    }

    /**
     * @return ArrayCollection<int,TabletUpdate>
     */
    public function getTabletUpdates()
    {
        return $this->tabletUpdates;
    }


    public function getActor(): string
    {
        return $this->actor;
    }

    /**
     * @param string $actor
     * @return $this
     */
    public function setActor(string $actor): Update
    {
        $this->actor = $actor;
        return $this;
    }


    public function getNotes(): ?string
    {
        return $this->notes;
    }

    /**
     * @param string|null $notes
     * @return $this
     */
    public function setNotes(?string $notes): Update
    {
        $this->notes = $notes;
        return $this;
    }


    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->created_at;
    }

    /**
     * @param DateTimeImmutable $created_at
     * @return $this
     */
    public function setCreatedAt(DateTimeImmutable $created_at): Update
    {
        $this->created_at = $created_at;
        return $this;
    }

}