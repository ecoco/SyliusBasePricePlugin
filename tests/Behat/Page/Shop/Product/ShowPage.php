<?php

declare(strict_types=1);

namespace Tests\Ecocode\SyliusBasePricePlugin\Behat\Page\Shop\Product;

use FriendsOfBehat\PageObjectExtension\Page\SymfonyPage;
use Behat\Mink\Element\NodeElement;

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

    public function getBasePriceText(): string
    {
        return $this->getBasePriceElement()->getText();
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'base_price' => '#product-price-base_price',
        ]);
    }

    private function getBasePriceElement(): NodeElement
    {
        if (!$this->hasBasePrice()) {
            throw new \RuntimeException('Element not found');
        }

        return $this->getElement('base_price');
    }

    private function hasBasePrice(): bool
    {
        return $this->hasElement('base_price');
    }
}
