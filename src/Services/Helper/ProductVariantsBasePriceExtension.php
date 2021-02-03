<?php

declare(strict_types=1);

namespace Ecocode\SyliusBasePricePlugin\Services\Helper;

use Ecocode\SyliusBasePricePlugin\Entity\Product\ProductVariantInterface;
use Ecocode\SyliusBasePricePlugin\Services\Calculator;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use Sylius\Component\Core\Model\ProductInterface;

/**
 * Class ProductVariantsBasePriceExtension
 * @package Ecocode\SyliusBasePricePlugin\Services\Helper
 */
final class ProductVariantsBasePriceExtension extends AbstractExtension
{
    /** @var Calculator */
    private $calculator;

    /** @var ChannelContextInterface */
    private $channelContext;

    public function __construct(Calculator $calculator, ChannelContextInterface $channelContext)
    {
        $this->channelContext    = $channelContext;
        $this->calculator = $calculator;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('ecocode_calculate_base_price', [$this, 'calculateBasePrice']),
            new TwigFunction('ecocode_calculate_base_prices', [$this, 'calculateBasePrices']),
        ];
    }

    public function calculateBasePrice(ProductVariantInterface $productVariant): ?string
    {
        /** @var \Sylius\Component\Core\Model\Channel $channel */
        $channel = $this->channelContext->getChannel();

        return $this->calculator->calculate($productVariant, $channel);
    }

    /**
     * @param ProductInterface $product
     *
     * @return array<int, string|null>
     */
    public function calculateBasePrices(ProductInterface $product): array
    {
        $basePrices = [];
        foreach ($product->getVariants() as $productVariant) {
            if ($productVariant instanceof ProductVariantInterface) {
                $basePrices[] = (string)$this->calculateBasePrice($productVariant);
            }
        }

        return $basePrices;
    }
}
