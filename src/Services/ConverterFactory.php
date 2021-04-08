<?php

declare(strict_types=1);

namespace Ecocode\SyliusBasePricePlugin\Services;

use Ecocode\SyliusBasePricePlugin\Exceptions\BadMeasurementException;
use InvalidArgumentException;
use Twig\Extension\AbstractExtension;
use UnitConverter\Measure;
use UnitConverter\Unit\UnitInterface;
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

        if ($measurements === null || count($measurements) === 0) {
            return $converter->addDefaultRegistry()->build();
        }

        $units = [];
        foreach ($measurements as $const) {
            /** @var string|null $measurement */
            $measurement = constant($const);
            if ($measurement === null) {
                throw new BadMeasurementException('unknown measurement "' . $const . '" given');
            }

            /** @var array<array-key, UnitInterface> $mergeUnits */
            $mergeUnits = array_map(
                [__CLASS__, 'classFactory'],
                Measure::getDefaultUnitsFor($measurement)
            );

            $units = array_merge($units, $mergeUnits);
        }

        return $converter->addRegistryWith($units)->build();
    }

    public static function classFactory(string $class): UnitInterface
    {
        if (class_exists($class)) {
            /** @psalm-suppress MixedMethodCall */
            $object = new $class();

            if (!$object instanceof UnitInterface) {
                $message = sprintf('Class %s not instance of UnitInterface', $class);

                throw new InvalidArgumentException($message);
            }

            return $object;
        }

        $message = sprintf('Class %s not found', $class);

        throw new InvalidArgumentException($message);
    }
}
