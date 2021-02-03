<?php

declare(strict_types=1);

namespace Ecocode\SyliusBasePricePlugin\Entity\Product;

use Doctrine\ORM\Mapping as ORM;

/**
 * Trait ProductVariantTrait
 * @package Ecocode\SyliusBasePricePlugin\Entity\Product
 */
trait ProductVariantTrait
{
    /**
     * @ORM\Column(name="base_price_unit", type="string", length=8, nullable=true)
     * @param null|string
     */
    protected $basePriceUnit;

    /**
     * @ORM\Column(name="base_price_value", type="integer", length=8, nullable=true)
     * @param null|int
     */
    protected $basePriceValue;

    /**
     * Product variant weight,volume,length unit name
     *
     * @param string|null $basePriceUnit
     *
     * @return $this
     */
    public function setBasePriceUnit(?string $basePriceUnit)
    {
        $this->basePriceUnit = $basePriceUnit;

        return $this;
    }

    /**
     * Product variant weight or volume unit name
     *
     * @return string|null
     */
    public function getBasePriceUnit(): ?string
    {
        return $this->basePriceUnit;
    }

    /**
     * Product variant weight or volume in defined "base_price_unit" units
     *
     * @param int|null $basePriceValue
     *
     * @return $this
     */
    public function setBasePriceValue(?int $basePriceValue)
    {
        $this->basePriceValue = $basePriceValue;

        return $this;
    }

    /**
     * Product variant weight or volume in defined "base_price_unit" units
     *
     * @return int|null
     */
    public function getBasePriceValue(): ?int
    {
        return $this->basePriceValue;
    }
}
