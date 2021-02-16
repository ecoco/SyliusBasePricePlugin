<?php

declare(strict_types=1);

namespace Tests\Ecocode\SyliusBasePricePlugin\Behat\Page\Shop\Product;

use FriendsOfBehat\PageObjectExtension\Page\PageInterface;

interface ShowPageInterface extends PageInterface
{
    public function getBasePrice(): string;

    public function getBasePriceAttribute(): string;

    public function hasBasePriceMessage($text): bool;

    public function hasBasePrice(): bool;
}
