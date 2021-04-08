<?php

declare(strict_types=1);

namespace Ecocode\SyliusBasePricePlugin\Services\Helper;

use Ecocode\SyliusBasePricePlugin\Entity\Product\ProductVariantInterface;
use Ecocode\SyliusBasePricePlugin\Services\Calculator;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Currency\Context\CurrencyContextInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

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

    /** @var CurrencyContextInterface */
    private $currencyContext;

    public function __construct(
        Calculator $calculator,
        ChannelContextInterface $channelContext,
        CurrencyContextInterface $currencyContext
    ) {
        $this->channelContext  = $channelContext;
        $this->calculator      = $calculator;
        $this->currencyContext = $currencyContext;
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
        $channel             = $this->channelContext->getChannel();
        $currentCurrencyCode = $this->currencyContext->getCurrencyCode();

        return $this->calculator->calculate(
            $productVariant,
            $channel,
            $currentCurrencyCode
        );
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
