<p align="center">
    <a href="https://sylius.com" target="_blank">
        <img src="https://demo.sylius.com/assets/shop/img/logo.png" />
    </a>
</p>

<h1 align="center">Ecocode Sylius Base Price Plugin</h1>

<p align="center">Plugin that calculates and shows product base price.</p>

## Documentation

Sylius Base Price Plugin allows you to add extra information to your shop about product base price. 
After installation you will need to set up product variable mass or volume or other unit.
This value will be used to calculate base unit price and will show up in product detail page and cart.

## Features

* Installs base_price_unit and base_price_value columns in sylius_product_variant table.
* "Base Price" tab in admin product variant page
* Base price display in product detail page
* Base price display in cart for each product

## Installation

### Download and install

```shell
composer require ecoco/sylius-base-price-plugin
```

### Enable plugin 

Register the plugin by adding it to your `config/bundles.php` file

```php
return [
    // ...
    Ecocode\SyliusBasePricePlugin\EcocodeSyliusBasePricePlugin::class => ['all' => true],
];
```


### Configure

```shell
# config/packages/ecocode_sylius_base_price_plugin.yaml

imports:
    - { resource: "@EcocodeSyliusBasePricePlugin/Resources/config/config.yaml" }

# Custom config override
# Mapping config will not be merged so all default mapping will be gone
#ecocode_sylius_base_price:
#    use_short_unit_name: false
#    mapping:
#        UnitConverter\Measure::VOLUME:
#            - { unit: UnitConverter\Unit\Volume\Millilitre, mod: 10 }
```

## Advanced configuration

If you want to have more control over what metrics are visible and how it will be converted then you should update:

`ecocode_sylius_base_price:` (uncomment from above)

Its default values are defined in `src/Resources/config/config.yaml`


### Extend `ProductVariant` entity

Add trait and interface to existing entity.

```php
<?php
# src/Entity/Product/ProductVariant.php
declare(strict_types=1);

namespace App\Entity\Product;

use Doctrine\ORM\Mapping as ORM;
use Ecocode\SyliusBasePricePlugin\Entity\Product\ProductVariantInterface;
use Ecocode\SyliusBasePricePlugin\Entity\Product\ProductVariantTrait;
use Sylius\Component\Core\Model\ProductVariant as BaseProductVariant;

/**
 * @ORM\Entity
 * @ORM\Table(name="sylius_product_variant")
 */
class ProductVariant extends BaseProductVariant implements ProductVariantInterface
{
    use ProductVariantTrait;
}
```

### Run migration

```shell
bin/console doctrine:migrations:diff
bin/console doctrine:migrations:migrate
```


## Usage

##### 1. Select product unit
![Screenshot edit product variant unit type](docs/images/admin-dropdown.jpg)

##### 2. Set product unit value
![Screenshot edit product variant base price](docs/images/admin.jpg)

##### 3. See it in product detail page
![Screenshot see product detail page](docs/images/product-detail-page.jpg)

##### 4. See it in cart
![Screenshot see in cart](docs/images/cart.jpg)


## Testing

Setup
```bash
$ composer install
$ cd tests/Application
$ yarn install
$ yarn run gulp
$ bin/console assets:install public -e test
$ bin/console doctrine:schema:create -e test
$ bin/console server:run 127.0.0.1:8080 -d public -e test
```

Run Tests
```bash
$ vendor/bin/behat
$ vendor/bin/phpunit
$ vendor/bin/phpstan analyse -c phpstan.neon -l max src/
```
