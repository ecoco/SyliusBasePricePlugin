<?php

declare(strict_types=1);

namespace Tests\Ecocode\SyliusBasePricePlugin\Application\Tests\Entity;

use Ecocode\SyliusBasePricePlugin\Entity\Mapping;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use stdClass;
use UnitConverter\Unit\Mass\Kilogram;

class MappingTest extends TestCase
{
    public function testMappingConstructorSetter()
    {
        $data = [
            'mod'        => 0.2,
            'unit'       => Kilogram::class,
            'ifMoreThan' => 2,
        ];

        $mapping = new Mapping($data);

        $this->assertEquals($data['mod'], $mapping->getMod());
        $this->assertEquals($data['unit'], $mapping->getUnit());
        $this->assertEquals($data['ifMoreThan'], $mapping->getIfMoreThan());
    }

    public function testMappingSetters()
    {
        $data = [
            'mod'        => 0,
            'unit'       => '',
            'ifMoreThan' => 0,
        ];

        $mapping = new Mapping($data);
        $mapping->setUnit('TEST_UNIT');
        $mapping->setMod(0.4);
        $mapping->setIfMoreThan(0.7);

        $this->assertEquals('TEST_UNIT', $mapping->getUnit());
        $this->assertEquals(0.4, $mapping->getMod());
        $this->assertEquals(0.7, $mapping->getIfMoreThan());
    }

    public function testMapperGetUnitClassReturnsClass()
    {
        $data = [
            'unit'       => Kilogram::class,
            'mod'        => 0,
            'ifMoreThan' => 0,
        ];
        $mapping = new Mapping($data);

        $class = $mapping->getUnitClass();

        $this->assertIsObject($class);
        $this->assertInstanceOf(Kilogram::class, $class);
    }

    public function testMapperGetUnitClassThrowsNotClassException()
    {
        $data = [
            'unit'       => 'UnknownTestClass',
            'mod'        => 0,
            'ifMoreThan' => 0,
        ];
        $mapping = new Mapping($data);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Unit class UnknownTestClass do not exist');

        $mapping->getUnitClass();
    }

    public function testMapperGetUnitClassThrowsWrongInterfaceException()
    {
        $data = [
            'unit'       => stdClass::class,
            'mod'        => 0,
            'ifMoreThan' => 0,
        ];
        $mapping = new Mapping($data);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Unit class stdClass have no implemented UnitInterface');

        $mapping->getUnitClass();
    }
}
