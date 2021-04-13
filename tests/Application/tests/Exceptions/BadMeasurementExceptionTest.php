<?php

declare(strict_types=1);

namespace Tests\Ecocode\SyliusBasePricePlugin\Application\Tests\Exceptions;


use Ecocode\SyliusBasePricePlugin\Entity\Mapping;
use Ecocode\SyliusBasePricePlugin\Exceptions\BadMeasurementException;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class BadMeasurementExceptionTest extends TestCase
{
    public function testExceptionClassIsFine()
    {
        $class = new BadMeasurementException('TEST');

        $this->assertInstanceOf(InvalidArgumentException::class, $class);
    }
}
