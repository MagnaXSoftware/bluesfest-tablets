<?php

namespace App\Models;

class Area
{
    protected ?int $id = null;
    protected string $name;
    protected ?int $expected;
    protected array $statuses = [];

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return Area
     */
    public function setId(int $id): Area
    {
        $this->id = $id;
        return $this;
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
        return $this->expected;
    }

    /**
     * @param int $expected
     * @return Area
     */
    public function setExpected(int $expected): Area
    {
        $this->expected = $expected;
        return $this;
    }

    /**
     * @return Status[]
     */
    public function getStatuses(): array
    {
        return $this->statuses;
    }

    /**
     * @param Status[] $statuses
     * @return Area
     */
    public function setStatuses(array $statuses): Area
    {
        $this->statuses = $statuses;
        return $this;
    }

}