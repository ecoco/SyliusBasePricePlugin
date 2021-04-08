<?php

declare(strict_types=1);

namespace Tests\Ecocode\SyliusBasePricePlugin\Behat\Context\Ui\Shop;

use Behat\Behat\Context\Context;
use Tests\Ecocode\SyliusBasePricePlugin\Behat\Page\Shop\StaticWelcomePage;
use Tests\Ecocode\SyliusBasePricePlugin\Behat\Page\Shop\DynamicWelcomePage;
use Webmozart\Assert\Assert;

final class WelcomeContext implements Context
{
    /**
     * @var StaticWelcomePage
     */
    private $staticWelcomePage;

    /**
     * @var StaticWelcomePage
     */
    private $dynamicWelcomePage;

    /**
     * @param StaticWelcomePage  $staticWelcomePage
     * @param DynamicWelcomePage $dynamicWelcomePage
     */
    public function __construct(StaticWelcomePage $staticWelcomePage, DynamicWelcomePage $dynamicWelcomePage)
    {
        $this->staticWelcomePage = $staticWelcomePage;
        $this->dynamicWelcomePage = $dynamicWelcomePage;
    }

    /**
     * @When a customer with an unknown name visits static welcome page
     */
    public function customerWithUnknownNameVisitsStaticWelcomePage(): void
    {
        $this->staticWelcomePage->open();
    }

    /**
     * @When a customer named :name visits static welcome page
     */
    public function namedCustomerVisitsStaticWelcomePage(string $name): void
    {
        $this->staticWelcomePage->open(['name' => $name]);
    }

    /**
     * @Then they should be statically greeted with :greeting
     */
    public function theyShouldBeStaticallyGreetedWithGreeting(string $greeting): void
    {
        Assert::same($this->staticWelcomePage->getGreeting(), $greeting);
    }

    /**
     * @When a customer with an unknown name visits dynamic welcome page
     */
    public function customerWithUnknownNameVisitsDynamicWelcomePage(): void
    {
        $this->dynamicWelcomePage->open();
    }

    /**
     * @When a customer named :name visits dynamic welcome page
     */
    public function namedCustomerVisitsDynamicWelcomePage(string $name): void
    {
        $this->dynamicWelcomePage->open(['name' => $name]);
    }

    /**
     * @Then they should be dynamically greeted with :greeting
     */
    public function theyShouldBeDynamicallyGreetedWithGreeting(string $greeting): void
    {
        Assert::same($this->dynamicWelcomePage->getGreeting(), $greeting);
    }
}
