<?php

declare(strict_types=1);

namespace Tests\Ecocode\SyliusBasePricePlugin\Application\Tests\DependencyInjection;

use Ecocode\SyliusBasePricePlugin\DependencyInjection\Configuration;
use PHPUnit\Framework\TestCase;
use UnitConverter\Unit\Volume\Millilitre;

class ConfigurationTest extends TestCase
{
    /**
     * @dataProvider configurationValidDataProvider
     *
     * @param array $inputConfig
     * @param array $expectedConfig
     */
    public function testConfiguration(array $inputConfig, array $expectedConfig)
    {
        $configuration = new Configuration();

        $node             = $configuration->getConfigTreeBuilder()->buildTree();
        $normalizedConfig = $node->normalize($inputConfig);
        $finalizedConfig  = $node->finalize($normalizedConfig);

        $this->assertEquals($expectedConfig, $finalizedConfig);
    }

    public function configurationValidDataProvider(): array
    {
        return [
            'test all ok'            => [
                [
                    'use_short_unit_name' => true,
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
                [
                    'use_short_unit_name' => true,
                    'mapping'             => [
                        'UnitConverter\Measure::VOLUME' => [
                            [
                                'unit'       => Millilitre::class,
                                'mod'        => 10,
                                'ifMoreThan' => 1
                            ]
                        ]
                    ]
                ]
            ],
            'test auto if more than' => [
                [
                    'use_short_unit_name' => true,
                    'mapping'             => [
                        'UnitConverter\Measure::VOLUME' => [
                            [
                                'unit' => Millilitre::class,
                                'mod'  => 10,
                            ]
                        ]
                    ]
                ],
                [
                    'use_short_unit_name' => true,
                    'mapping'             => [
                        'UnitConverter\Measure::VOLUME' => [
                            [
                                'unit'       => Millilitre::class,
                                'mod'        => 10,
                                'ifMoreThan' => 0
                            ]
                        ]
                    ]
                ]
            ],
            'test auto mod'          => [
                [
                    'use_short_unit_name' => true,
                    'mapping'             => [
                        'UnitConverter\Measure::VOLUME' => [
                            [
                                'unit'       => Millilitre::class,
                                'ifMoreThan' => 1
                            ]
                        ]
                    ]
                ],
                [
                    'use_short_unit_name' => true,
                    'mapping'             => [
                        'UnitConverter\Measure::VOLUME' => [
                            [
                                'unit'       => Millilitre::class,
                                'mod'        => 1,
                                'ifMoreThan' => 1
                            ]
                        ]
                    ]
                ]
            ],
            'test auto all'          => [
                [
                    'use_short_unit_name' => true,
                    'mapping'             => [
                        'UnitConverter\Measure::VOLUME' => [
                            [
                                'unit' => Millilitre::class,
                            ]
                        ]
                    ]
                ],
                [
                    'use_short_unit_name' => true,
                    'mapping'             => [
                        'UnitConverter\Measure::VOLUME' => [
                            [
                                'unit'       => Millilitre::class,
                                'mod'        => 1,
                                'ifMoreThan' => 0
                            ]
                        ]
                    ]
                ]
            ],
        ];
    }
}
