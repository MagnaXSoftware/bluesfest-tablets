<?php

namespace App\Models;

use App\Enums\StateEnum;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;


#[ORM\Entity]
#[ORM\Table(name: "tablet_updates")]
class TabletUpdate
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    #[ORM\Column(type: "integer")]
    private int $id;

    #[ORM\ManyToOne(targetEntity: Tablet::class, cascade: ["persist"], fetch: "LAZY", inversedBy: "tabletUpdates")]
    private Tablet $tablet;

    #[ORM\ManyToOne(targetEntity: Update::class, cascade: ["persist"], fetch: "EAGER", inversedBy: "tabletUpdates")]
    private Update $update;

    #[ORM\Column(type: "StateEnum")]
    private StateEnum $status;

    public function getId(): int
    {
        return $this->id;
    }

    public function getState(): StateEnum
    {
        return $this->status;
    }

    /**
     * @param string|StateEnum $state
     * @return $this
     */
    public function setState(StateEnum|string $state): TabletUpdate
    {
        if (is_string($state))
            $state = StateEnum::get($state);
        $this->status = $state;
        return $this;
    }

    public function getTablet(): Tablet
    {
        return $this->tablet;
    }

    /**
     * @param Tablet $tablet
     * @return $this
     */
    public function setTablet(Tablet $tablet): TabletUpdate
    {
        $this->tablet = $tablet;
        return $this;
    }

    public function getUpdate(): Update
    {
        return $this->update;
    }

    /**
     * @param Update $update
     * @return $this
     */
    public function setUpdate(Update $update): TabletUpdate
    {
        $this->update = $update;
        return $this;
    }

    public function getTimestamp(): DateTimeImmutable
    {
        return $this->update->getCreatedAt();
    }

    public function getWho(): string
    {
        return $this->update->getActor();
    }

    /**
     * @return string|null
     */
    public function getNotes(): ?string
    {
        return $this->update->getNotes();
    }
}