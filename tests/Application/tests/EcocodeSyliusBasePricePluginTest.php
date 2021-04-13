<?php

declare(strict_types=1);

namespace Tests\Ecocode\SyliusBasePricePlugin\Application\Tests;

use Ecocode\SyliusBasePricePlugin\EcocodeSyliusBasePricePlugin;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class EcocodeSyliusBasePricePluginTest extends WebTestCase
{
    public function testPluginInit()
    {
        $plugin = new EcocodeSyliusBasePricePlugin();

        $this->assertIsObject($plugin);
    }
}
