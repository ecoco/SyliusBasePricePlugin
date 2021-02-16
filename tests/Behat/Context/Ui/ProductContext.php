<?php

declare(strict_types=1);

namespace Tests\Ecocode\SyliusBasePricePlugin\Behat\Context\Ui;

use Behat\Behat\Context\Context;
use Webmozart\Assert\Assert;
use Behat\Mink\Element\Element;
use Tests\Ecocode\SyliusBasePricePlugin\Behat\Page\Shop\Product\ShowPageInterface;

/**
 * Class ProductContext
 * @package Tests\Ecocode\SyliusBasePricePlugin\Behat\Context\Ui
 */
final class ProductContext implements Context
{
    /** @var ShowPageInterface */
    private $showPage;

    public function __construct(ShowPageInterface $showPage)
    {
        $this->showPage = $showPage;
    }

    /**
     * @Then I should see the product base price :baseprice
     */
    public function iShouldSeeTheProductBasePrice($baseprice)
    {
        Assert::same($this->showPage->getBasePrice(), $baseprice);
    }

    /**
     * @Then I should see the product base price attribute :attributevalue
     */
    public function iShouldHaveBasePriceElementAttribute($attributevalue)
    {
        Assert::same($this->showPage->getBasePriceAttribute(), $attributevalue);
    }
}
