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

    public function getItemBasePrice(string $productName): string
    {
        $basePrice = $this->getElement('product_base_price', ['%name%' => $productName]);

        return trim($basePrice->getText());
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'product_base_price' => '[data-test-cart-product-row="%name%"] [data-test-cart-product-base-price]',
        ]);
    }
}
