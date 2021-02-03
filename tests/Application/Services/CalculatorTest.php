<?php

declare(strict_types=1);

namespace Tests\Ecocode\SyliusBasePricePlugin\Application\Services;

use Ecocode\SyliusBasePricePlugin\Services\Calculator;
use Ecocode\SyliusBasePricePlugin\Services\ConverterFactory;
use PHPUnit\Framework\MockObject\MockObject;
use Sylius\Component\Core\Model\Channel;
use Sylius\Component\Core\Model\ChannelPricingInterface;
use Sylius\Component\Currency\Model\Currency;
use Sylius\Component\Locale\Model\Locale;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Tests\Ecocode\SyliusBasePricePlugin\Application\Entity\ProductVariant;
use UnitConverter\Unit\UnitInterface;

/**
 * Class CalculatorTest
 * @package Tests\Ecocode\SyliusBasePricePlugin\Application\Services
 */
class CalculatorTest extends WebTestCase
{
    /** @var Calculator */
    private $calculator;

    /** @var ChannelPricingInterface|MockObject */
    private $channelPricingMock;

    /** @var Channel|MockObject */
    private $channelMock;

    /** @var MockObject|ProductVariant */
    private $productVariantMock;

    public function setUp(): void
    {
        $this->channelPricingMock = $this->createMock(ChannelPricingInterface::class);
        $this->productVariantMock = $this->createMock(ProductVariant::class);
        $this->channelMock        = $this->createMock(Channel::class);

        self::bootKernel();
        $container = self::$container;

        $this->calculator = $container->get(Calculator::class);
    }

    public function calculatorReturnsDataProvider(): array
    {
        return include __DIR__ . DIRECTORY_SEPARATOR . 'fixtures' . DIRECTORY_SEPARATOR . 'DataProvider.php';
    }

    /**
     * @dataProvider calculatorReturnsDataProvider
     */
    public function testCalculationReturnsExpected(
        ?string $expected,
        UnitInterface $unit,
        int $size,
        int $price,
        string $currencyCode,
        string $localeCode,
        array $config
    ) {
        $this->calculator->setUseShortUnitName((bool)$config['use_short']);
        $this->calculator->setMappingConfig($config['mapping']);

        // need to init again to get up and running not defined measurements (from config.yaml)
        $this->calculator->setUnitConverter(ConverterFactory::createUnitConverter(array_keys($config['mapping'])));

        $this->productVariantMock
            ->expects($this->any())
            ->method('getBasePriceUnit')
            ->will($this->returnValue($unit->getSymbol()));

        $this->productVariantMock
            ->expects($this->any())
            ->method('getBasePriceValue')
            ->will($this->returnValue($size));

        $this->channelPricingMock
            ->expects($this->any())
            ->method('getPrice')
            ->will($this->returnValue($price));

        $this->productVariantMock
            ->expects($this->any())
            ->method('getChannelPricingForChannel')
            ->will($this->returnValue($this->channelPricingMock));

        $this->channelMock->expects($this->any())->method('getCode')->will($this->returnValue('en_US'));

        $currency = new Currency();
        $currency->setCode($currencyCode);
        $this->channelMock->expects($this->any())->method('getBaseCurrency')->will($this->returnValue($currency));

        $locale = new Locale();
        $locale->setCode($localeCode);
        $this->channelMock->expects($this->any())->method('getDefaultLocale')->will($this->returnValue($locale));

        $data = $this->calculator->calculate($this->productVariantMock, $this->channelMock);

        $this->assertEquals($expected, $data);
    }
}
