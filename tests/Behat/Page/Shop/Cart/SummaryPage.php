<?php

declare(strict_types=1);

namespace Tests\Ecocode\SyliusBasePricePlugin\Behat\Page\Shop\Cart;

use FriendsOfBehat\PageObjectExtension\Page\SymfonyPage;

/**
 * Class SummaryPage
 * @package Tests\Ecocode\SyliusBasePricePlugin\Behat\Page\Shop\Cart
 */
class SummaryPage extends SymfonyPage implements SummaryPageInterface
{
    public function getRouteName(): string
    {
        return 'sylius_shop_cart_summary';
    }

    public function getItemBasePriceText(string $productName): string
    {
        return $this->getBasePriceElement($productName)->getText();
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'product_base_price' => '[data-test-cart-product-row="%name%"] [data-test-cart-product-base-price]',
        ]);
    }

    private function hasBasePrice(string $productName): bool
    {
        return $this->hasElement('product_base_price', ['%name%' => $productName]);
    }

    private function getBasePriceElement(string $productName)
    {
        if (!$this->hasBasePrice($productName)) {
            throw new \RuntimeException('Element not found');
        }

        $element = $this->getElement('product_base_price', ['%name%' => $productName]);

        return $element;
    }
}
