<?php

declare(strict_types=1);

namespace Ecocode\SyliusBasePricePlugin;

use Sylius\Bundle\CoreBundle\Application\SyliusPluginTrait;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class EcocodeSyliusBasePricePlugin
 * @package Ecocode\SyliusBasePricePlugin
 */
final class EcocodeSyliusBasePricePlugin extends Bundle
{
    use SyliusPluginTrait;
}
