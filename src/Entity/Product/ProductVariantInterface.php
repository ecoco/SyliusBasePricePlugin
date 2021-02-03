<?php

declare(strict_types=1);

namespace Ecocode\SyliusBasePricePlugin\Entity\Product;

/**
 * Interface ProductVariantInterface
 * @package Ecocode\SyliusBasePricePlugin\Entity\Product
 */
interface ProductVariantInterface
{
    /**
     * @param string|null $basePriceUnit
     *
     * @return $this
     */
    public function setBasePriceUnit(?string $basePriceUnit);

    /**
     * @return string|null
     */
    public function getBasePriceUnit(): ?string;

    /**
     * @param int|null $basePriceValue
     *
     * @return $this
     */
    public function setBasePriceValue(?int $basePriceValue);

    /**
     * @return int|null
     */
    public function getBasePriceValue(): ?int;
}
