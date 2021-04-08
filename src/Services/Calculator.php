<?php

declare(strict_types=1);

namespace Ecocode\SyliusBasePricePlugin\Services;

use Ecocode\SyliusBasePricePlugin\Entity\Mapping;
use Ecocode\SyliusBasePricePlugin\Entity\Product\ProductVariantInterface;
use Sylius\Bundle\MoneyBundle\Templating\Helper\FormatMoneyHelper;
use Sylius\Component\Core\Model\Channel;
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
    private $mappingConfig = [];

    /** @var bool */
    private $useShortUnitName = true;

    /** @var TranslatorInterface */
    private $translator;

    /** @var UnitConverter */
    private $unitConverter;

    /** @var FormatMoneyHelper */
    private $formatMoneyHelper;

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
        $this->translator        = $translator;
        $this->formatMoneyHelper = $formatMoneyHelper;
        $this->unitConverter     = $unitConverter;
    }

    public function setUnitConverter(UnitConverter $unitConverter): self
    {
        $this->unitConverter = $unitConverter;

        return $this;
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
     *
     * @return null|string
     */
    public function calculate(ProductVariantInterface $productVariant, Channel $channel): ?string
    {
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
                $symbol         = (string)$mapping->getUnitClass()->getSymbol();
                $targetUnitSize = $this->unitConverter->to($symbol);

                if (!$this->isMappingUsable($mapping, $targetUnitSize)) {
                    continue;
                }

                $channelPricing = $productVariant->getChannelPricingForChannel($channel);
                if ($channelPricing === null) {
                    continue;
                }
                $channelPrice = $channelPricing->getPrice();
                if ($channelPrice === null) {
                    continue;
                }

                $format              = 'ecocode_sylius_base_price_plugin.%s.%s';
                $unitType            = sprintf($format, $this->useShortUnitName ? 'unit_short' : 'unit', $symbol);
                $unitTypeTranslation = $this->translator->trans($unitType);
                if ($unitTypeTranslation == $unitType) { // fallback if unit translation is missing
                    $unitTypeTranslation = $this->useShortUnitName ? $symbol : $mapping->getUnitClass()->getName();
                }

                $mod           = $mapping->getMod();
                $baseCurrency  = $channel->getBaseCurrency();
                $defaultLocale = $channel->getDefaultLocale();
                $endPrice      = intval(($channelPrice / (float)$targetUnitSize) * $mod);
                $text          = $this->translator->trans(
                    'ecocode_sylius_base_price_plugin.format',
                    [
                        '%PRICE%' => $this->formatMoneyHelper->formatAmount(
                            $endPrice,
                            $baseCurrency !== null ? (string)$baseCurrency->getCode() : '',
                            $defaultLocale !== null ? (string)$defaultLocale->getCode() : ''
                        ),
                        '%VALUE%' => $mod > 1 ? $mod . ' ' : '',
                        '%TYPE%'  => $unitTypeTranslation
                    ]
                );

                return $text;
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
        if ($basePriceUnitValue === null || $productVariant->getBasePriceValue() === null) {
            return [];
        }

        $basePriceUnit = $this->unitConverter->getRegistry()->loadUnit($basePriceUnitValue);

        if ($basePriceUnit === null) {
            return [];
        }

        $measurement = $basePriceUnit->getUnitOf();
        if ($measurement === null) {
            return [];
        }

        return $this->getMeasurementMappings($measurement);
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
