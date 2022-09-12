<?php

namespace App\Models;

use Doctrine\ORM\Mapping as ORM;

use App\Enums\StateEnum;
use DateTimeImmutable;
use DateTimeInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="tablet_updates")
 */
class TabletUpdate
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\ManyToOne(targetEntity="Tablet", inversedBy="tabletUpdates", cascade={"persist"}, fetch="LAZY")
     */
    private Tablet $tablet;

    /**
     * @Orm\ManyToOne(targetEntity="Update", inversedBy="tabletUpdates", cascade={"persist"}, fetch="EAGER")
     */
    private Update $update;

    /**
     * @ORM\Column(type="StateEnum")
     */
    private StateEnum $status;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return StateEnum
     */
    public function getState(): ?StateEnum
    {
        if (is_string($this->status))
            $this->status = StateEnum::get($this->status);

        return $this->status;
    }

    /**
     * @param string|StateEnum $state
     * @return $this
     */
    public function setState($state): TabletUpdate
    {
        if (is_string($state))
            $state = StateEnum::get($state);
        $this->status = $state;
        return $this;
    }

    /**
     * @return Tablet
     */
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

    /**
     * @return Update
     */
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

    /**
     * @return DateTimeImmutable
     */
    public function getTimestamp(): DateTimeImmutable
    {
        return $this->update->getCreatedAt();
    }

    /**
     * @return string
     */
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