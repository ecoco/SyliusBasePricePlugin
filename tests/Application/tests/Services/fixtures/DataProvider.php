<?php

declare(strict_types=1);

use UnitConverter\Unit\Mass\Gram;
use UnitConverter\Unit\Mass\Kilogram;
use UnitConverter\Unit\Mass\Tonne;
use UnitConverter\Unit\Volume\Litre;
use UnitConverter\Unit\Volume\Millilitre;
use UnitConverter\Unit\DigitalStorage\Terabyte;
use UnitConverter\Unit\DigitalStorage\Gigabyte;
use UnitConverter\Unit\DigitalStorage\Megabyte;

return [
    '#0' => [ // simple case convert to 100g
        'expected' => '$0.10 / 100 g',
        'unit'     => new Kilogram(),
        'size'     => 1,
        'price'    => 100,
        'currency' => 'USD',
        'locale'   => 'en',
        'config'   => [
            'use_short' => true,
            'mapping'   => [
                'UnitConverter\Measure::MASS' => [
                    ['unit' => Gram::class, 'mod' => 100, 'ifMoreThan' => 0],
                    ['unit' => Kilogram::class, 'mod' => 1, 'ifMoreThan' => 1],
                ],
            ],
        ],
    ],
    '#1' => [ // simple case convert to 1kg
        'expected' => '$0.10 / kg',
        'unit'     => new Kilogram(),
        'size'     => 10,
        'price'    => 100,
        'currency' => 'USD',
        'locale'   => 'en',
        'config'   => [
            'use_short' => true,
            'mapping'   => [
                'UnitConverter\Measure::MASS' => [
                    ['unit' => Kilogram::class, 'mod' => 1, 'ifMoreThan' => 1],
                ],
            ],
        ],
    ],
    '#2' => [ // simple case convert to 1kg
        'expected' => '$1.00 / kg',
        'unit'     => new Kilogram(),
        'size'     => 1,
        'price'    => 100,
        'currency' => 'USD',
        'locale'   => 'en',
        'config'   => [
            'use_short' => true,
            'mapping'   => [
                'UnitConverter\Measure::MASS' => [
                    ['unit' => Kilogram::class, 'mod' => 1, 'ifMoreThan' => 0],
                ],
            ],
        ],
    ],
    '#3' => [ // simple case not created because too big
        'expected' => null,
        'unit'     => new Gram(),
        'size'     => 1000, // 1000g = 1kg and its less than defined "ifMoreThan" for kg
        'price'    => 100,
        'currency' => 'USD',
        'locale'   => 'en',
        'config'   => [
            'use_short' => true,
            'mapping'   => [
                'UnitConverter\Measure::MASS' => [
                    ['unit' => Kilogram::class, 'mod' => 1, 'ifMoreThan' => 1],
                ],
            ],
        ],
    ],
    '#4' => [ // fallback case
        'expected' => '$2.00 / 10 kg',
        'unit'     => new Kilogram(),
        'size'     => 100,
        'price'    => 2000,
        'currency' => 'USD',
        'locale'   => 'en',
        'config'   => [
            'use_short' => true,
            'mapping'   => [
                'UnitConverter\Measure::MASS' => [
                    ['unit' => Kilogram::class, 'mod' => 100, 'ifMoreThan' => 200], // skip because ifMoreThan too big
                    ['unit' => Kilogram::class, 'mod' => 10, 'ifMoreThan' => 99.99], // will be picked
                ],
            ],
        ],
    ],
    // ------ EURO with mixed config using volume ---------
    '#5' => [ // simple case convert to 100ml
        'expected' => '€0.10 / 100 ml',
        'unit'     => new Litre(),
        'size'     => 1,
        'price'    => 100,
        'currency' => 'EUR',
        'locale'   => 'en',
        'config'   => [
            'use_short' => true,
            'mapping'   => [
                'UnitConverter\Measure::VOLUME' => [
                    ['unit' => Millilitre::class, 'mod' => 100, 'ifMoreThan' => 0],
                    ['unit' => Litre::class, 'mod' => 1, 'ifMoreThan' => 1],
                ],
                'UnitConverter\Measure::MASS' => [
                    ['unit' => Kilogram::class, 'mod' => 100, 'ifMoreThan' => 200], // skip because ifMoreThan too big
                    ['unit' => Kilogram::class, 'mod' => 10, 'ifMoreThan' => 99.99], // will be picked
                ],
            ],
        ],
    ],
    '#6' => [ // simple case convert to 1l
        'expected' => '€0.10 / l',
        'unit'     => new Litre(),
        'size'     => 10,
        'price'    => 100,
        'currency' => 'EUR',
        'locale'   => 'en',
        'config'   => [
            'use_short' => true,
            'mapping'   => [
                'UnitConverter\Measure::MASS' => [
                    ['unit' => Kilogram::class, 'mod' => 100, 'ifMoreThan' => 200], // skip because ifMoreThan too big
                    ['unit' => Kilogram::class, 'mod' => 10, 'ifMoreThan' => 99.99], // will be picked
                ],
                'UnitConverter\Measure::VOLUME' => [
                    ['unit' => Litre::class, 'mod' => 1, 'ifMoreThan' => 1],
                ],
            ],
        ],
    ],
    '#7' => [ // simple case convert to 1l
        'expected' => '€1.00 / l',
        'unit'     => new Litre(),
        'size'     => 1,
        'price'    => 100,
        'currency' => 'EUR',
        'locale'   => 'en',
        'config'   => [
            'use_short' => true,
            'mapping'   => [
                'UnitConverter\Measure::MASS' => [
                    ['unit' => Kilogram::class, 'mod' => 100, 'ifMoreThan' => 200], // skip because ifMoreThan too big
                    ['unit' => Kilogram::class, 'mod' => 10, 'ifMoreThan' => 99.99], // will be picked
                ],
                'UnitConverter\Measure::VOLUME' => [
                    ['unit' => Litre::class, 'mod' => 1, 'ifMoreThan' => 0],
                ],
            ],
        ],
    ],
    '#8' => [ // simple case not created because too big
        'expected' => null,
        'unit'     => new Millilitre(),
        'size'     => 1000, // 1000g = 1kg and its less than defined "ifMoreThan" for kg
        'price'    => 100,
        'currency' => 'EUR',
        'locale'   => 'en',
        'config'   => [
            'use_short' => true,
            'mapping'   => [
                'UnitConverter\Measure::VOLUME' => [
                    ['unit' => Litre::class, 'mod' => 1, 'ifMoreThan' => 1],
                ],
                'UnitConverter\Measure::MASS' => [
                    ['unit' => Kilogram::class, 'mod' => 100, 'ifMoreThan' => 200], // skip because ifMoreThan too big
                    ['unit' => Kilogram::class, 'mod' => 10, 'ifMoreThan' => 99.99], // will be picked
                ],
            ],
        ],
    ],
    '#9' => [ // fallback case
        'expected' => '€2.00 / 10 l',
        'unit'     => new Litre(),
        'size'     => 100,
        'price'    => 2000,
        'currency' => 'EUR',
        'locale'   => 'en',
        'config'   => [
            'use_short' => true,
            'mapping'   => [
                'UnitConverter\Measure::MASS' => [
                    ['unit' => Kilogram::class, 'mod' => 100, 'ifMoreThan' => 200], // skip because ifMoreThan too big
                    ['unit' => Kilogram::class, 'mod' => 10, 'ifMoreThan' => 99.99], // will be picked
                ],
                'UnitConverter\Measure::VOLUME' => [
                    ['unit' => Litre::class, 'mod' => 100, 'ifMoreThan' => 200], // skip because ifMoreThan too big
                    ['unit' => Litre::class, 'mod' => 10, 'ifMoreThan' => 99.99], // will be picked
                ],
            ],
        ],
    ],
    // ---------- other units --------
    '#10' => [
        'expected' => '£3.83 / 100 GB', // HDD
        'unit'     => new Gigabyte(),
        'size'     => 3000,
        'price'    => 11500,
        'currency' => 'GBP',
        'locale'   => 'en',
        'config'   => [
            'use_short' => true,
            'mapping'   => [
                'UnitConverter\Measure::DIGITAL_STORAGE' => [
                    ['unit' => Gigabyte::class, 'mod' => 100, 'ifMoreThan' => 0],
                ],
            ],
        ],
    ],
    '#11' => [
        'expected' => '€0.04 / 100 MB', // Gen4 PCIe M.2
        'unit'     => new Terabyte(),
        'size'     => 1,
        'price'    => 40000,
        'currency' => 'EUR',
        'locale'   => 'en',
        'config'   => [
            'use_short' => true,
            'mapping'   => [
                'UnitConverter\Measure::DIGITAL_STORAGE' => [
                    ['unit' => Megabyte::class, 'mod' => 100, 'ifMoreThan' => 0],
                ],
            ],
        ],
    ],
    '#12' => [
        'expected' => '€38.31 / kg',
        'unit'     => new Gram(),
        'size'     => 261,
        'price'    => 1000,
        'currency' => 'EUR',
        'locale'   => 'en',
        'config'   => [
            'use_short' => true,
            'mapping'   => [
                'UnitConverter\Measure::MASS' => [
                    ['unit' => Gram::class, 'mod' => 100, 'ifMoreThan' => 0],
                    ['unit' => Kilogram::class, 'mod' => 1, 'ifMoreThan' => 0.25],
                    ['unit' => Tonne::class, 'mod' => 1, 'ifMoreThan' => 50],
                ],
            ],
        ],
    ],
    '#13' => [
        'expected' => '€9.99 / t',
        'unit'     => new Gram(),
        'size'     => 50000001, // 5t and 1g
        'price'    => 50000,
        'currency' => 'EUR',
        'locale'   => 'en',
        'config'   => [
            'use_short' => true,
            'mapping'   => [
                'UnitConverter\Measure::MASS' => [
                    ['unit' => Gram::class, 'mod' => 100, 'ifMoreThan' => 0],
                    ['unit' => Kilogram::class, 'mod' => 1, 'ifMoreThan' => 0.25],
                    ['unit' => Tonne::class, 'mod' => 1, 'ifMoreThan' => 50],
                ],
            ],
        ],
    ],
];
