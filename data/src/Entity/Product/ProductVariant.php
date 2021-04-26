<?php
# src/Entity/Product/ProductVariant.php
declare(strict_types=1);

namespace App\Entity\Product;

use Doctrine\ORM\Mapping as ORM;
use Ecocode\SyliusBasePricePlugin\Entity\Product\ProductVariantInterface;
use Ecocode\SyliusBasePricePlugin\Entity\Product\ProductVariantTrait;
use Sylius\Component\Core\Model\ProductVariant as BaseProductVariant;

/**
 * @ORM\Entity
 * @ORM\Table(name="sylius_product_variant")
 */
class ProductVariant extends BaseProductVariant implements ProductVariantInterface
{
    use ProductVariantTrait;
}
