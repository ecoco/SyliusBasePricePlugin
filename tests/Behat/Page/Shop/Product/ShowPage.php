<?php

declare(strict_types=1);

namespace Tests\Ecocode\SyliusBasePricePlugin\Behat\Page\Shop\Product;

use FriendsOfBehat\PageObjectExtension\Page\SymfonyPage;

/**
 * Class ShowPage
 * @package Tests\Ecocode\SyliusBasePricePlugin\Behat\Page\Shop\Product
 */
class ShowPage extends SymfonyPage implements ShowPageInterface
{
    public function getRouteName(): string
    {
        return 'sylius_shop_product_show';
    }

    public function getBasePriceAttribute(): string
    {
        return $this->getBasePriceElement()->getAttribute('data-base-price-text');
    }

    public function getBasePrice(): string
    {
        return $this->getBasePriceElement()->getText();
    }

    public function hasBasePriceMessage($text): bool
    {
        return $this->getBasePrice() === $text;
    }

    public function hasBasePrice(): bool
    {
        return $this->hasElement('base_price');
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'base_price' => '#product-price-base_price',
        ]);
    }

    private function getBasePriceElement()
    {
        if (!$this->hasBasePrice()) {
            throw new \RuntimeException('Element not found');
        }

        $element = $this->getElement('base_price');

        return $element;
    }
}
