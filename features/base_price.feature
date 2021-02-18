@ui_base_price_pdp
Feature: Product Base Price
    Should be visible
    In product detail page and cart

    @javascript
    Scenario: Product detail page and cart shows base price value in simple store setup
        Given the store operates on a single channel in "United States"
        Given the store has a product "Milk 1l" priced at "$1.00"
        Given the product "Milk 1l" has base price unit "L" and value "1"
        When I check this product's details
        Then I should see the product base price attribute '["$0.10 \/ 100 ml"]'
        Then I should see the product base price "$0.10 / 100 ml"
        When I add product "Milk 1l" to the cart
        Then I should be on my cart summary page
        And I should be notified that the product has been successfully added
        And there should be one item in my cart
        And this item should have name "Milk 1l"
        Then I should see in cart product "Milk 1l" base price "$0.10 / 100 ml"


    @javascript
    Scenario Outline: Product detail page and cart shows base price value in multi currency store
        Given the store operates on a channel named "Web Channel"
        And that channel allows to shop using "EUR", "USD" and "GBP" currencies
        Given the store has a product <product> priced at <price>
        Given the channel "Web Channel" is enabled
        Given the product <product> has base price unit <unit> and value <value>
        When I switch to the <other_currency> currency
        When I switch to the <currency> currency
        When I check this product's details
        Then I should see the product base price <base_price>
        When I add product "Milk 1l" to the cart
        Then I should be on my cart summary page
        And I should be notified that the product has been successfully added
        And there should be one item in my cart
        And this item should have name <product>
        Then I should see in cart product <product> base price <base_price>

        Examples:
            | product   | currency | other_currency | price   | unit | value |  base_price       |
            | "Milk 1L" |  "USD"   | "EUR"          | "$1.00" | "L"  | 1     | "$0.10 / 100 ml"  |
            | "Milk 1L" |  "EUR"   | "GBP"          | "€1.00" | "L"  | 1     | "€0.10 / 100 ml"  |
            | "Milk 1L" |  "GBP"   | "EUR"          | "£1.00" | "L"  | 1     | "£0.10 / 100 ml"  |
