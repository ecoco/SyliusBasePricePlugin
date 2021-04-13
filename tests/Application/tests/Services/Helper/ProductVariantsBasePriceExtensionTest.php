<?php

declare(strict_types=1);

namespace Tests\Ecocode\SyliusBasePricePlugin\Application\Tests\Services\Helper;

use Doctrine\Common\Collections\ArrayCollection;
use Ecocode\SyliusBasePricePlugin\Entity\Product\ProductVariantInterface;
use Ecocode\SyliusBasePricePlugin\Services\Calculator;
use Ecocode\SyliusBasePricePlugin\Services\Helper\ProductVariantsBasePriceExtension;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Currency\Context\CurrencyContextInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Twig\TwigFunction;

class ProductVariantsBasePriceExtensionTest extends WebTestCase
{
    public function testTwigFunctionsInit()
    {
        $calculatorMock = $this->createPartialMock(Calculator::class, []);

        $channelContextMock  = $this->createMock(ChannelContextInterface::class);
        $currencyContextMock = $this->createMock(CurrencyContextInterface::class);

        $helper = new ProductVariantsBasePriceExtension(
            $calculatorMock,
            $channelContextMock,
            $currencyContextMock
        );

        $functions = $helper->getFunctions();

        $this->assertIsArray($functions);
        $this->assertArrayHasKey(0, $functions);
        $this->assertArrayHasKey(1, $functions);
        $this->assertInstanceOf(TwigFunction::class, $functions[0]);
        $this->assertInstanceOf(TwigFunction::class, $functions[1]);
        $this->assertEquals('ecocode_calculate_base_price', $functions[0]->getName());
        $this->assertEquals('ecocode_calculate_base_prices', $functions[1]->getName());
    }

    public function testTwigCalculateBasePrice()
    {
        $calculatorMock = $this->createPartialMock(Calculator::class, ['calculate']);

        $channelContextMock  = $this->createMock(ChannelContextInterface::class);
        $currencyContextMock = $this->createMock(CurrencyContextInterface::class);

        $helper = new ProductVariantsBasePriceExtension(
            $calculatorMock,
            $channelContextMock,
            $currencyContextMock
        );

        $productVariant = $this->createMock(ProductVariantInterface::class);

        $channel = $this->createMock(ChannelInterface::class);

        $channelContextMock->expects($this->once())->method('getChannel')->willReturn($channel);
        $currencyContextMock->expects($this->once())->method('getCurrencyCode')->willReturn('EUR');
        $calculatorMock->expects($this->once())->method('calculate')->with($productVariant, $channel, 'EUR')->willReturn('TEST_RESPONSE');

        $response = $helper->calculateBasePrice($productVariant);

        $this->assertEquals('TEST_RESPONSE', $response);
    }

    public function testTwigCalculateBasePrices()
    {
        $calculatorMock = $this->createPartialMock(Calculator::class, ['calculate']);

        $channelContextMock  = $this->createMock(ChannelContextInterface::class);
        $currencyContextMock = $this->createMock(CurrencyContextInterface::class);

        $helper = new ProductVariantsBasePriceExtension(
            $calculatorMock,
            $channelContextMock,
            $currencyContextMock
        );

        $product        = $this->createMock(ProductInterface::class);
        $productVariant = $this->createMock(ProductVariantInterface::class);

        $product->expects($this->once())->method('getVariants')->willReturn(new ArrayCollection([$productVariant]));

        $channel = $this->createMock(ChannelInterface::class);

        $channelContextMock->expects($this->once())->method('getChannel')->willReturn($channel);
        $currencyContextMock->expects($this->once())->method('getCurrencyCode')->willReturn('EUR');
        $calculatorMock->expects($this->once())->method('calculate')->with($productVariant, $channel, 'EUR')->willReturn('TEST_RESPONSE2');

        $response = $helper->calculateBasePrices($product);

        $this->assertEquals(['TEST_RESPONSE2'], $response);
    }
}
