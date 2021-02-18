<?php

declare(strict_types=1);

namespace Ecocode\SyliusBasePricePlugin\Services;

use Ecocode\SyliusBasePricePlugin\Entity\Mapping;
use Ecocode\SyliusBasePricePlugin\Entity\Product\ProductVariantInterface;
use Sylius\Bundle\MoneyBundle\Templating\Helper\ConvertMoneyHelper;
use Sylius\Bundle\MoneyBundle\Templating\Helper\ConvertMoneyHelperInterface;
use Sylius\Bundle\MoneyBundle\Templating\Helper\FormatMoneyHelper;
use Sylius\Component\Core\Model\Channel;
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
    /** @var array<string, array<string, array<string, string|float>>> */
    private $mappingConfig;

    /** @var bool */
    private $useShortUnitName;

    /** @var TranslatorInterface */
    private $translator;

    /** @var UnitConverter */
    private $unitConverter;

    /** @var FormatMoneyHelper */
    private $moneyFormatter;

    /** @var ConvertMoneyHelperInterface */
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

    public function setMoneyConverter(ConvertMoneyHelper $convertMoneyHelper)
    {
        $this->moneyConverter = $convertMoneyHelper;

        return $this;
    }

    public function setUnitConverter(UnitConverter $unitConverter): self
    {
        $this->unitConverter = $unitConverter;

        return $this;
    }

    /**
     * @param array<string, array<string, array<string, string|float>>> $mapping
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
     * @param Channel                 $channel
     * @param string|null             $currencyCode current currency code
     *
     * @return string|null
     */
    public function calculate(
        ProductVariantInterface $productVariant,
        Channel $channel,
        string $currencyCode = null
    ): ?string {
        if (!$productVariant instanceof CoreModelProductVariantInterface) {
            return null;
        }

        $measurementMapping = $this->getMeasurementMapping($productVariant);

        foreach ($measurementMapping as $mappings) {
            $this
                ->unitConverter
                ->from((string)$productVariant->getBasePriceUnit())
                ->convert((string)$productVariant->getBasePriceValue());

            foreach ($mappings as $mapping) {
                $symbol         = $mapping->getUnitClass()->getSymbol();
                $targetUnitSize = $this->unitConverter->to((string)$symbol);

                if (!$this->isMappingUsable($mapping, $targetUnitSize)) {
                    continue;
                }

                $channelPricing = $productVariant->getChannelPricingForChannel($channel);
                if (!$channelPricing) {
                    continue;
                }

                $params = $this->getBasePriceFormatParams(
                    $channel,
                    $channelPricing,
                    $mapping,
                    (float)$targetUnitSize,
                    $currencyCode
                );

                return $this->translator->trans('ecocode_sylius_base_price_plugin.format', $params);
            }
        }

        return null;
    }

    /**
     * @param ProductVariantInterface $productVariant
     *
     * @return array<string, Mapping[]>
     */
    public function getMeasurementMapping(ProductVariantInterface $productVariant): array
    {
        $basePriceUnitValue = $productVariant->getBasePriceUnit();
        if (empty($basePriceUnitValue) || empty($productVariant->getBasePriceValue())) {
            return [];
        }

        $basePriceUnit = $this->unitConverter->getRegistry()->loadUnit($basePriceUnitValue);

        if (!$basePriceUnit) {
            return [];
        }

        $measurement = $basePriceUnit->getUnitOf();
        if (empty($measurement)) {
            return [];
        }

        return $this->getMeasurementMappings($measurement);
    }

    private function getBasePriceFormatParams(
        Channel $channel,
        ChannelPricingInterface $channelPricing,
        Mapping $mapping,
        float $targetUnitSize,
        string $currencyCode
    ): array {
        $symbol              = $mapping->getUnitClass()->getSymbol();
        $format              = 'ecocode_sylius_base_price_plugin.%s.%s';
        $unitType            = sprintf($format, $this->useShortUnitName ? 'unit_short' : 'unit', $symbol);
        $unitTypeTranslation = $this->translator->trans($unitType);
        if ($unitTypeTranslation == $unitType) { // fallback if unit translation is missing
            $unitTypeTranslation = $this->useShortUnitName ? $symbol : $mapping->getUnitClass()->getName();
        }

        $mod              = $mapping->getMod();
        $baseCurrency     = $channel->getBaseCurrency();
        $baseCurrencyCode = $baseCurrency && $baseCurrency->getCode() ? $baseCurrency->getCode() : $currencyCode;
        $targetCurrency   = $currencyCode ?? $baseCurrencyCode;
        $price            = ($channelPricing->getPrice() / $targetUnitSize) * $mod;
        $convertedPrice   = $this->moneyConverter->convertAmount((int)$price, $baseCurrencyCode, $targetCurrency);
        $defaultLocale    = $channel->getDefaultLocale();
        $locale           = $defaultLocale ? (string)$defaultLocale->getCode() : '';
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
            $measurementConfig = (string)constant((string)$measurementsConst);
            if ($measurement != $measurementConfig) {
                continue;
            }

            foreach ($mappingsConfig as $mappingData) {
                $mappings[(string)$measurementConfig][] = new Mapping($mappingData);
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
