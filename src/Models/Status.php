<?php

namespace App\Models;

use App\Enums\StateEnum;
use DateTimeImmutable;
use DateTimeInterface;

class Status
{
    private int $area_id;
    private ?Area $area;
    private $status;
    private int $deployed;
    private int $stored;
    private int $recovered;
    private int $timestamp;
    private string $notes = '';
    private string $actor;

    public function __set(string $name, $value): void
    {
        if ($name == 'area_area_id' or $name == 'area_area_name') {
            if (!isset($this->area) || is_null($this->area)) {
                $this->area = new Area();
            }
            if ($name == 'area_area_id') {
                $this->area->setId($value);
            }
            if ($name == 'area_area_name') {
                $this->area->setName($value);
            }
        }
    }

    /**
     * @return int
     */
    public function getAreaId(): int
    {
        if (!is_null($this->area)) {
            return $this->getArea()->getId();
        }
        return $this->area_id;
    }

    /**
     * @param int $area_id
     * @return Status
     */
    public function setAreaId(int $area_id): Status
    {
        $this->area = null;
        $this->area_id = $area_id;
        return $this;
    }

    /**
     * @return Area
     */
    public function getArea(): Area
    {
        return $this->area;
    }

    /**
     * @param Area $area
     * @return Status
     */
    public function setArea(Area $area): Status
    {
        $this->area = $area;
        return $this;
    }

    /**
     * @return StateEnum
     */
    public function getStatus(): ?StateEnum
    {
        if (is_string($this->status))
            $this->status = StateEnum::get($this->status);

        return $this->status;
    }

    /**
     * @param string|StateEnum $status
     * @return Status
     */
    public function setStatus($status): Status
    {
        if (is_string($status))
            $status = StateEnum::get($status);
        $this->status = $status;
        return $this;
    }

    /**
     * @return int
     */
    public function getDeployed(): int
    {
        return $this->deployed;
    }

    /**
     * @param int $deployed
     * @return Status
     */
    public function setDeployed(int $deployed): Status
    {
        $this->deployed = $deployed;
        return $this;
    }

    /**
     * @return int
     */
    public function getStored(): int
    {
        return $this->stored;
    }

    /**
     * @param int $stored
     * @return Status
     */
    public function setStored(int $stored): Status
    {
        $this->stored = $stored;
        return $this;
    }

    /**
     * @return int
     */
    public function getRecovered(): int
    {
        return $this->recovered;
    }

    /**
     * @param int $recovered
     * @return Status
     */
    public function setRecovered(int $recovered): Status
    {
        $this->recovered = $recovered;
        return $this;
    }

    /**
     * @return int
     */
    public function getTimestamp(): int
    {
        return $this->timestamp;
    }

    /**
     * @param int $timestamp
     * @return Status
     */
    public function setTimestamp(int $timestamp): Status
    {
        $this->timestamp = $timestamp;
        return $this;
    }

    /**
     * @return DateTimeInterface
     */
    public function getDateTime(): DateTimeInterface
    {
        return new \DateTimeImmutable('@' . $this->getTimestamp());
    }

    /**
     * @return string
     */
    public function getNotes(): string
    {
        return $this->notes;
    }

    /**
     * @param string $notes
     * @return Status
     */
    public function setNotes(string $notes): Status
    {
        $this->notes = $notes;
        return $this;
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
     * @return Status
     */
    public function setActor(string $actor): Status
    {
        $this->actor = $actor;
        return $this;
    }


}