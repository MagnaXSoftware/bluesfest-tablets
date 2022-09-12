<?php

namespace App\Models;

use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="updates")
 */
class Update
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private int $id;
    /**
     * @ORM\ManyToOne(targetEntity="Area")
     */
    private ?Area $area;
    /**
     * @ORM\OneToMany(targetEntity="TabletUpdate", mappedBy="update", cascade={"persist"}, fetch="LAZY")
     * @var ArrayCollection<int,TabletUpdate>
     */
    private $tabletUpdates;
    /**
     * @ORM\Column(type="string")
     */
    private string $actor;
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $notes;
    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private DateTimeImmutable $created_at;

    public function __construct()
    {
        $this->tabletUpdates = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return ?Area
     */
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

    /**
     * @return string
     */
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

    /**
     * @return string|null
     */
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

    /**
     * @return DateTimeImmutable
     */
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