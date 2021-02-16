<?php

declare(strict_types=1);

namespace Tests\Ecocode\SyliusBasePricePlugin\Behat\Context\Ui;

use Behat\Behat\Context\Context;
use Ecocode\SyliusBasePricePlugin\Entity\Product\ProductVariantInterface;
use Tests\Ecocode\SyliusBasePricePlugin\Behat\Page\Shop\Cart\SummaryPageInterface;
use Webmozart\Assert\Assert;


final class CartContext implements Context
{
    /** @var SummaryPageInterface */
    private $summaryPage;

    public function __construct(SummaryPageInterface $summaryPage)
    {
        $this->summaryPage = $summaryPage;
    }

    /**
     * @Then I should see in cart product :productName base price :baseprice
     */
    public function iShouldSeeTheProductBasePrice(string $productName, string $baseprice)
    {
        Assert::same($this->summaryPage->getItemBasePrice($productName), $baseprice);
    }
}
