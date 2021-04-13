<?php

declare(strict_types=1);

namespace Tests\Ecocode\SyliusBasePricePlugin\Application\Tests\DependencyInjection;

use Ecocode\SyliusBasePricePlugin\DependencyInjection\EcocodeSyliusBasePriceExtension;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use UnitConverter\Unit\Mass\Kilogram;
use UnitConverter\Unit\Volume\Millilitre;

class EcocodeSyliusBasePriceExtensionTest extends TestCase
{
    public function testExtensionNoOverride()
    {
        $config = [
            0 => [
                'use_short_unit_name' => false,
                'mapping'             => [
                    'UnitConverter\Measure::VOLUME' => [
                        [
                            'unit'       => Millilitre::class,
                            'mod'        => 10,
                            'ifMoreThan' => 1
                        ]
                    ]
                ]
            ],
        ];

        $container = new ContainerBuilder();
        $extension = new EcocodeSyliusBasePriceExtension();
        $extension->load($config, $container);

        $this->assertTrue($container->hasParameter("ecocode_sylius_base_price"));
        $this->assertTrue($container->hasParameter("ecocode_sylius_base_price.mapping"));
        $this->assertEquals($config[0]['mapping'], $container->getParameter("ecocode_sylius_base_price.mapping"));
        $this->assertFalse($container->getParameter("ecocode_sylius_base_price.use_short_unit_name"));
    }

    public function testExtensionWithOverride()
    {
        $config = [
            0 => [
                'use_short_unit_name' => false,
                'mapping'             => [
                    'UnitConverter\Measure::VOLUME' => [
                        [
                            'unit'       => Millilitre::class,
                            'mod'        => 10,
                            'ifMoreThan' => 1
                        ]
                    ]
                ]
            ],
            1 => [
                'use_short_unit_name' => true,
                'mapping'             => [
                    'UnitConverter\Measure::Mass' => [
                        [
                            'unit' => Kilogram::class,
                        ]
                    ]
                ]
            ],
        ];

        $container = new ContainerBuilder();
        $extension = new EcocodeSyliusBasePriceExtension();
        $extension->load($config, $container);

        $this->assertTrue($container->hasParameter("ecocode_sylius_base_price"));
        $this->assertTrue($container->hasParameter("ecocode_sylius_base_price.mapping"));

        $expected = $config[1]['mapping'];

        $expected['UnitConverter\Measure::Mass'][0]['ifMoreThan'] = 0;
        $expected['UnitConverter\Measure::Mass'][0]['mod']        = 1;

        $this->assertEquals($expected, $container->getParameter("ecocode_sylius_base_price.mapping"));

        $this->assertTrue($container->getParameter("ecocode_sylius_base_price.use_short_unit_name"));
    }

    public function testExtensionWithEmptyMappingOverride()
    {
        $config = [
            0 => [
                'use_short_unit_name' => false,
                'mapping'             => [
                    'UnitConverter\Measure::VOLUME' => [
                        [
                            'unit'       => Millilitre::class,
                            'mod'        => 10,
                            'ifMoreThan' => 1
                        ]
                    ]
                ]
            ],
            1 => [
                'use_short_unit_name' => true,
                'mapping'             => []
            ],
        ];

        $container = new ContainerBuilder();
        $extension = new EcocodeSyliusBasePriceExtension();
        $extension->load($config, $container);

        $this->assertTrue($container->hasParameter("ecocode_sylius_base_price"));
        $this->assertTrue($container->hasParameter("ecocode_sylius_base_price.mapping"));

        $this->assertEquals($config[0]['mapping'], $container->getParameter("ecocode_sylius_base_price.mapping"));

        $this->assertTrue($container->getParameter("ecocode_sylius_base_price.use_short_unit_name"));
    }

    public function testExtensionWithEmptyOverride()
    {
        $config = [
            0 => [
                'use_short_unit_name' => false,
                'mapping'             => [
                    'UnitConverter\Measure::VOLUME' => [
                        [
                            'unit'       => Millilitre::class,
                            'mod'        => 10,
                            'ifMoreThan' => 1
                        ]
                    ]
                ]
            ],
            1 => [
                'use_short_unit_name' => true
            ],
        ];

        $container = new ContainerBuilder();
        $extension = new EcocodeSyliusBasePriceExtension();
        $extension->load($config, $container);

        $this->assertTrue($container->hasParameter("ecocode_sylius_base_price"));
        $this->assertTrue($container->hasParameter("ecocode_sylius_base_price.mapping"));

        $this->assertEquals($config[0]['mapping'], $container->getParameter("ecocode_sylius_base_price.mapping"));

        $this->assertTrue($container->getParameter("ecocode_sylius_base_price.use_short_unit_name"));
    }
}
