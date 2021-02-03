<?php

declare(strict_types=1);

namespace Tests\Ecocode\SyliusBasePricePlugin\Application\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ecocode\SyliusBasePricePlugin\Entity\Product\ProductVariantInterface;
use Ecocode\SyliusBasePricePlugin\Entity\Product\ProductVariantTrait;
use Sylius\Component\Core\Model\ProductVariant as BaseProductVariant;

/**
 * Class ProductVariant
 * @package Tests\Ecocode\SyliusBasePricePlugin\Application\Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="sylius_product_variant")
 */
class ProductVariant extends BaseProductVariant implements ProductVariantInterface
{
    use ProductVariantTrait;
}
