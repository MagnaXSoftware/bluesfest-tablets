<?php

namespace App\Models;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="tablets")
 */
class Tablet
{

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private int $id;
    /**
     * @ORM\Column(type="string")
     */
    private string $code;
    /**
     * @ORM\ManyToOne(targetEntity="Area", inversedBy="tablets", fetch="EAGER")
     * @var Area
     */
    private Area $area;
    /**
     * @ORM\OneToMany(targetEntity="TabletUpdate", mappedBy="tablet", cascade={"persist"}, fetch="EAGER")
     * @var ArrayCollection<int,TabletUpdate>
     */
    private $tabletUpdates;

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
     * @return Area|null
     */
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

    /**
     * @return string
     */
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

    /**
     * @return ?TabletUpdate
     */
    public function getLastUpdate()
    {
        return $this->tabletUpdates->matching((new Criteria())->orderBy(['update.created_at' => 'DESC']))->first();

    }

}