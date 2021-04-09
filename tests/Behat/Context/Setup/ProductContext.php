<?php

declare(strict_types=1);

namespace Tests\Ecocode\SyliusBasePricePlugin\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use Doctrine\Persistence\ObjectManager;
use Sylius\Component\Core\Model\ProductInterface;
use Ecocode\SyliusBasePricePlugin\Entity\Product\ProductVariantInterface;
use Sylius\Component\Product\Resolver\ProductVariantResolverInterface;

/**
 * Class ProductContext
 * @package Sylius\Behat\Context\Setup
 */
final class ProductContext implements Context
{
    /** @var ObjectManager */
    private $objectManager;

    /** @var ProductVariantResolverInterface */
    private $defaultVariantResolver;

    public function __construct(
        ObjectManager $objectManager,
        ProductVariantResolverInterface $defaultVariantResolver
    ) {
        $this->objectManager = $objectManager;
        $this->defaultVariantResolver = $defaultVariantResolver;
    }

    /**
     * @Given the product :product has base price unit :unit and value :value
     */
    public function productHasBasePrice(ProductInterface $product, string $value, string $unit): void
    {
        /** @var ProductVariantInterface $productVariant */
        $productVariant = $this->defaultVariantResolver->getVariant($product);

        $productVariant->setBasePriceValue((int)$value);
        $productVariant->setBasePriceUnit($unit);

        $this->objectManager->flush();
    }
}
