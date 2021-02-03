<?php

declare(strict_types=1);

namespace Ecocode\SyliusBasePricePlugin\Entity;

use UnitConverter\Unit\UnitInterface;

/**
 * Class Mapping
 * @package Ecocode\SyliusBasePricePlugin\Entity
 */
class Mapping
{
    /** @var float */
    private $mod;

    /** @var float */
    private $ifMoreThan;

    /** @var string */
    private $unit;

    /** @var UnitInterface|null */
    private $unitClass = null;

    /**
     * Mapping constructor.
     *
     * @param array<string,float|string> $data
     */
    public function __construct(array $data)
    {
        $this->setMod((float)$data['mod']);
        $this->setUnit((string)$data['unit']);
        $this->setIfMoreThan((float)$data['ifMoreThan']);
    }

    /**
     * @return float
     */
    public function getMod(): float
    {
        return $this->mod;
    }

    /**
     * @param float $mod
     *
     * @return $this
     */
    public function setMod(float $mod): self
    {
        $this->mod = $mod;

        return $this;
    }

    /**
     * @return float
     */
    public function getIfMoreThan(): float
    {
        return $this->ifMoreThan;
    }

    /**
     * @param float $ifMoreThan
     *
     * @return $this
     */
    public function setIfMoreThan(float $ifMoreThan): self
    {
        $this->ifMoreThan = $ifMoreThan;

        return $this;
    }

    /**
     * @return string
     */
    public function getUnit(): string
    {
        return $this->unit;
    }

    /**
     * @return UnitInterface
     */
    public function getUnitClass(): UnitInterface
    {
        if ($this->unitClass === null) {
            $this->unitClass = new $this->unit;
        }

        return $this->unitClass;
    }

    /**
     * @param string $unit
     *
     * @return $this
     */
    public function setUnit(string $unit): self
    {
        $this->unit = $unit;

        return $this;
    }
}
