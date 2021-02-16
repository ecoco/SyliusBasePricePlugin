<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Ecocode\SyliusBasePricePlugin\Behat\Page\Shop\Cart;

use FriendsOfBehat\PageObjectExtension\Page\PageInterface;

interface SummaryPageInterface extends PageInterface
{
    public function getItemBasePrice(string $productName): string;
}
