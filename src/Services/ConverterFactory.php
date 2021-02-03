<?php

declare(strict_types=1);

namespace Ecocode\SyliusBasePricePlugin\Services;

use Ecocode\SyliusBasePricePlugin\Exceptions\BadMeasurementException;
use Twig\Extension\AbstractExtension;
use UnitConverter\Measure;
use UnitConverter\UnitConverter;

/**
 * Class Converter
 * @package Ecocode\SyliusBasePricePlugin\Services
 */
class ConverterFactory extends AbstractExtension
{
    /**
     * @param string[] $measurements example: ['UnitConverter\Measure::MASS']
     *
     * @return UnitConverter
     */
    public static function createUnitConverter(array $measurements = null): UnitConverter
    {
        $converter = UnitConverter::createBuilder();
        $converter->addSimpleCalculator();

        if (!$measurements) {
            return $converter->addDefaultRegistry()->build();
        }

        $units = [];
        foreach ($measurements as $const) {
            $measurement = constant($const);
            if (!$measurement) {
                throw new BadMeasurementException('unknown measurement "' . $measurement . '" given');
            }

            $units = array_merge(
                $units,
                array_map(function ($class) {
                    return new $class();
                }, Measure::getDefaultUnitsFor($measurement))
            );
        }

        return $converter->addRegistryWith($units)->build();
    }
}
