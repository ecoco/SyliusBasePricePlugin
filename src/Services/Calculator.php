<?php

declare(strict_types=1);

namespace Ecocode\SyliusBasePricePlugin\Services;

use Ecocode\SyliusBasePricePlugin\Entity\Mapping;
use Ecocode\SyliusBasePricePlugin\Entity\Product\ProductVariantInterface;
use RuntimeException;
use Sylius\Bundle\MoneyBundle\Templating\Helper\ConvertMoneyHelper;
use Sylius\Bundle\MoneyBundle\Templating\Helper\ConvertMoneyHelperInterface;
use Sylius\Bundle\MoneyBundle\Templating\Helper\FormatMoneyHelper;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ChannelPricingInterface;
use Sylius\Component\Core\Model\ProductVariantInterface as CoreModelProductVariantInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Extension\AbstractExtension;
use UnitConverter\UnitConverter;
use function constant;

/**
 * Class Calculator
 * @package Ecocode\SyliusBasePricePlugin\Services
 */
class Calculator extends AbstractExtension
{
    const PRECISION = 6;

    /** @var array<string, array<string, array<string, string|float>>> */
    private $mappingConfig = [];

    /** @var bool */
    private $useShortUnitName = true;

    /** @var TranslatorInterface */
    private $translator;

    /** @var UnitConverter|null */
    private $unitConverter;

    /** @var FormatMoneyHelper */
    private $moneyFormatter;

    /** @var ConvertMoneyHelperInterface|null */
    private $moneyConverter;

    /**
     * Calculator constructor.
     *
     * @param TranslatorInterface $translator
     * @param UnitConverter       $unitConverter
     * @param FormatMoneyHelper   $formatMoneyHelper
     */
    public function __construct(
        TranslatorInterface $translator,
        UnitConverter $unitConverter,
        FormatMoneyHelper $formatMoneyHelper
    ) {
        $this->translator     = $translator;
        $this->moneyFormatter = $formatMoneyHelper;
        $this->setUnitConverter($unitConverter);
    }

    public function setMoneyConverter(ConvertMoneyHelper $convertMoneyHelper): self
    {
        $this->moneyConverter = $convertMoneyHelper;

        return $this;
    }

    public function setUnitConverter(UnitConverter $unitConverter): self
    {
        $this->unitConverter = $unitConverter;

        return $this;
    }

    private function getUnitConverter(): UnitConverter
    {
        if ($this->unitConverter === null) {
            throw new RuntimeException('Unit Converter class not set');
        }

        return $this->unitConverter;
    }

    /**
     * @param array<string, array<string, array<string, string|float>>> $mappingConfig
     *
     * @return $this
     */
    public function setMappingConfig(array $mappingConfig): self
    {
        $this->mappingConfig = $mappingConfig;

        return $this;
    }

    public function setUseShortUnitName(bool $useShortUnitName): self
    {
        $this->useShortUnitName = $useShortUnitName;

        return $this;
    }

    /**
     * @param ProductVariantInterface $productVariant
     * @param ChannelInterface        $channel
     * @param string                  $currencyCode current currency code
     *
     * @return string|null
     */
    public function calculate(
        ProductVariantInterface $productVariant,
        ChannelInterface $channel,
        string $currencyCode
    ): ?string {
        if (!$productVariant instanceof CoreModelProductVariantInterface) {
            return null;
        }

        $measurementMapping = $this->getMeasurementMapping($productVariant);

        $selected = [];
        foreach ($measurementMapping as $mappings) {
            $this
                ->getUnitConverter()
                ->from((string)$productVariant->getBasePriceUnit())
                ->convert((string)$productVariant->getBasePriceValue(), self::PRECISION);

            foreach ($mappings as $mapping) {
                $symbol         = $mapping->getUnitClass()->getSymbol();
                $targetUnitSize = $this->getUnitConverter()->to((string)$symbol);

                if (!$this->isMappingUsable($mapping, $targetUnitSize)) {
                    continue;
                }

                $channelPricing = $productVariant->getChannelPricingForChannel($channel);
                if ($channelPricing === null) {
                    continue;
                }
                if ($channelPricing->getPrice() === null) {
                    continue;
                }

                $selected = compact('mapping', 'channelPricing', 'targetUnitSize');
            }
        }

        if ($selected === []) {
            return null;
        }

        /** @var ChannelPricingInterface $channelPricing */
        $channelPricing = $selected['channelPricing'];
        /** @var Mapping $mapping */
        $mapping        = $selected['mapping'];
        $targetUnitSize = (float)$selected['targetUnitSize'];

        /** @var array $params */
        $params = $this->getBasePriceFormatParams(
            $channel,
            $channelPricing,
            $mapping,
            $targetUnitSize,
            $currencyCode
        );

        return $this->translator->trans('ecocode_sylius_base_price_plugin.format', $params);
    }

    /**
     * @param ProductVariantInterface $productVariant
     *
     * @return array<string, Mapping[]>
     */
    public function getMeasurementMapping(ProductVariantInterface $productVariant): array
    {
        $basePriceUnitValue = $productVariant->getBasePriceUnit();
        if ($basePriceUnitValue === null || $productVariant->getBasePriceValue() === null) {
            return [];
        }

        $basePriceUnit = $this->getUnitConverter()->getRegistry()->loadUnit($basePriceUnitValue);

        if ($basePriceUnit === null) {
            return [];
        }

        $measurement = $basePriceUnit->getUnitOf();
        if ($measurement === null) {
            return [];
        }

        return $this->getMeasurementMappings($measurement);
    }

    private function getBasePriceFormatParams(
        ChannelInterface $channel,
        ChannelPricingInterface $channelPricing,
        Mapping $mapping,
        float $targetUnitSize,
        string $targetCurrency
    ): array {
        $symbol              = (string)$mapping->getUnitClass()->getSymbol();
        $format              = 'ecocode_sylius_base_price_plugin.%s.%s';
        $unitType            = sprintf($format, $this->useShortUnitName ? 'unit_short' : 'unit', $symbol);
        $unitTypeTranslation = $this->translator->trans($unitType);
        if ($unitTypeTranslation == $unitType) { // fallback if unit translation is missing
            $unitTypeTranslation = $this->useShortUnitName ? $symbol : $mapping->getUnitClass()->getName();
        }

        if ($this->moneyConverter === null) {
            throw new RuntimeException('Money converter not set');
        }

        $mod              = $mapping->getMod();
        $baseCurrency     = $channel->getBaseCurrency();
        $baseCurrencyCode = $baseCurrency !== null && $baseCurrency->getCode() !== null ? $baseCurrency->getCode() : $targetCurrency;
        $channelPrice     = (float)$channelPricing->getPrice();
        $price            = ($channelPrice / $targetUnitSize) * $mod;
        $convertedPrice   = $this->moneyConverter->convertAmount((int)$price, $baseCurrencyCode, $targetCurrency);
        $defaultLocale    = $channel->getDefaultLocale();
        $locale           = $defaultLocale !== null ? (string)$defaultLocale->getCode() : '';
        $formattedPrice   = $this->moneyFormatter->formatAmount((int)$convertedPrice, $targetCurrency, $locale);

        return [
            '%PRICE%' => $formattedPrice,
            '%VALUE%' => $mod > 1 ? $mod . ' ' : '',
            '%TYPE%'  => $unitTypeTranslation
        ];
    }

    /**
     * @param string|null $measurement
     *
     * @return array<string, Mapping[]>
     */
    private function getMeasurementMappings(?string $measurement): array
    {
        $mappings = [];
        if ($measurement === null) {
            return $mappings;
        }

        foreach ($this->mappingConfig as $measurementsConst => $mappingsConfig) {
            $measurementConfig = (string)constant($measurementsConst);
            if ($measurement != $measurementConfig) {
                continue;
            }

            foreach ($mappingsConfig as $mappingData) {
                $mappings[$measurementConfig][] = new Mapping($mappingData);
            }
        }

        return $mappings;
    }

    /**
     * @param Mapping          $mapping
     * @param int|float|string $targetUnitValue
     *
     * @return bool
     */
    private function isMappingUsable(Mapping $mapping, $targetUnitValue): bool
    {
        if ($targetUnitValue <= 0) {
            return false;
        }

        if ($targetUnitValue > $mapping->getIfMoreThan()) {
            return true;
        }

        return false;
    }
}
