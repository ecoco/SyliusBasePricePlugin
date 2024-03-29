@ui_base_price_pdp
Feature: Product Base Price
    Should be visible
    In product detail page and cart
    And its values must be correct


    @api @ui
    Scenario Outline: Headless - Product detail page and cart shows base price value in simple store setup
        Given the store operates on a channel named "Web" in <currency> currency
        Given the store has a product <product> priced at <price>
        Given the product <product> has base price unit <unit> and value <value>
        When I check this product's details
        Then I should see the product base price attribute <base_price_attr>
        When I add product <product> to the cart
        And this item should have name <product>
        Then I should see in cart product <product> base price <base_price>

        Examples:
            | product       | currency | price   | unit | value | base_price       | base_price_attr            |
            | "Milk 0.5L"   |  "USD"   | "$0.50" | "mL" | 500   | "$1.00 / l"     | '["$1.00 \/ l"]'           |
            | "Milk 1L"     |  "USD"   | "$1.00" | "L"  | 1     | "$1.00 / l"     | '["$1.00 \/ l"]'           |
            | "Milk 2L"     |  "EUR"   | "€1.50" | "L"  | 2     | "€0.75 / l"      | '["\u20ac0.75 \/ l"]'      |
            | "Milk 3L"     |  "GBP"   | "£1.90" | "L"  | 3     | "£0.63 / l"      | '["\u00a30.63 \/ l"]'      |
            | "Sugar 500g"  |  "USD"   | "$1.00" | "g"  | 500   | "$2.00 / kg"     | '["$2.00 \/ kg"]'       |
            | "Sugar 1kg"   |  "EUR"   | "€1.40" | "kg" | 1     | "€1.40 / kg"  | '["\u20ac1.40 \/ kg"]'  |
            | "Sugar 1500g" |  "GBP"   | "£1.99" | "g"  | 1500  | "£1.32 / kg"     | '["\u00a31.32 \/ kg"]'     |
            | "Wire 1m"     |  "GBP"   | "£100"  | "m"  | 1     | "£100.00 / m" | '["\u00a3100.00 \/ m"]'     |
            | "Tiles 11 m2" |  "EUR"   | "€18"   | "m2" | 11    | "€1.63 / m2"     | '["\u20ac1.63 \/ m2"]'     |


    @javascript @ui
    Scenario Outline: Product detail page and cart shows base price value in simple store setup
        Given the store operates on a channel named "Web" in <currency> currency
        Given the store has a product <product> priced at <price>
        Given the product <product> has base price unit <unit> and value <value>
        When I check this product's details
        Then I should see the product base price <base_price>
        When I add product <product> to the cart
        Then I should see in cart product <product> base price <base_price>

        Examples:
            | product      | currency | price   | unit  | value   | base_price       |
            | "Milk 1L"    |  "USD"   | "$1.00" | "L"   | 1       | "$1.00 / l"      |
            | "Milk 1L"    |  "EUR"   | "€1.00" | "L"   | 1       | "€1.00 / l"      |
            | "Milk 1L"    |  "GBP"   | "£1.00" | "L"   | 1       | "£1.00 / l"      |
            | "Milk 200mL" |  "USD"   | "$1.00" | "mL"  | 200     | "$0.50 / 100 ml" |
            | "Milk 200mL" |  "EUR"   | "€1.00" | "mL"  | 200     | "€0.50 / 100 ml" |
            | "Milk 200mL" |  "GBP"   | "£1.00" | "mL"  | 200     | "£0.50 / 100 ml" |


    @javascript @ui
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
        When I add product <product> to the cart
        And this item should have name <product>
        Then I should see in cart product <product> base price <base_price>

        Examples:
            | product      | currency | other_currency | price   | unit  | value   |  base_price       |
            | "Milk 1L"    |  "USD"   | "EUR"          | "$1.00" | "L"   | 1       | "$1.00 / l"       |
            | "Milk 1L"    |  "EUR"   | "GBP"          | "€1.00" | "L"   | 1       | "€1.00 / l"       |
            | "Milk 1L"    |  "GBP"   | "EUR"          | "£1.00" | "L"   | 1       | "£1.00 / l"       |
            | "Milk 200mL" |  "USD"   | "EUR"          | "$1.00" | "mL"  | 200     | "$0.50 / 100 ml"  |
            | "Milk 200mL" |  "EUR"   | "GBP"          | "$1.00" | "mL"  | 200     | "€0.50 / 100 ml"  |
            | "Milk 200mL" |  "GBP"   | "EUR"          | "£1.00" | "mL"  | 200     | "£0.50 / 100 ml"  |
