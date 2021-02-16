@ui_base_price_pdp
Feature: Product Base Price
    Should be visible
    In product detail page and cart

    Background:
        Given the store operates on a single channel in "United States"

    @javascript
    Scenario: Product detail page shows base price value
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
