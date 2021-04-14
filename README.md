# Ecocode Sylius Base Price Plugin

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE)
[![Code Coverage](https://scrutinizer-ci.com/g/ecoco/SyliusBasePricePlugin/badges/coverage.png?b=main)](https://scrutinizer-ci.com/g/ecoco/SyliusBasePricePlugin/?branch=main)
[![Build Status](https://scrutinizer-ci.com/g/ecoco/SyliusBasePricePlugin/badges/build.png?b=main)](https://scrutinizer-ci.com/g/ecoco/SyliusBasePricePlugin/build-status/main)

[![SymfonyInsight](https://insight.symfony.com/projects/caed611a-2891-4653-8c22-06d96c210c6f/big.svg)](https://insight.symfony.com/projects/caed611a-2891-4653-8c22-06d96c210c6f)

Plugin that calculates and shows product base price

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

or update schema without defining migration

```shell
bin/console -e test doctrine:schema:update --dump-sql
bin/console -e test doctrine:schema:update --dump-sql --force
```


### Clear cache
```shell
bin/console cache:clear
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


# Testing


## Using docker

Simplest way to test pack (excluding behat) is to run library using given Dockerfile

```
docker build . -t sylius-base-price-plugin
docker run -v $(pwd)/build:/var/www/html/build -e XDEBUG_MODE=coverage sylius-base-price-plugin composer test
```

```
docker-compose up -d
docker-compose exec phpfpm console doctrine:schema:create
docker-compose exec phpfpm console sylius:fixtures:load
docker-compose exec phpfpm console doctrine:schema:update --dump-sql --force

```


## Setup

1) You will need to have database running. You can use locally installed one or run it using docker.

     ```
     docker run -d --rm -p 3306:3306 -e MYSQL_ROOT_PASSWORD=test -e MYSQL_DATABASE=sylius -e MYSQL_USER=app -e MYSQL_PASSWORD=test mariadb:10.5.9
     ```

2) Then edit and make sure database configuration is correct: 

Edit tests/Application/.env.test file. And add database configuration there

```
DATABASE_URL=mysql://root:root@127.0.0.1/sylius

# if you're running mariadb
DATABASE_URL=mysql://root:root@0.0.0.0/sylius?serverVersion=mariadb-10.3.25
```

3) Export test env (then commands dont require `-e test`)

```bash
export APP_ENV=test
```

## Installation

Continue with setup

```bash
composer install
cd tests/Application
yarn install
yarn run gulp
bin/console assets:install public -e test
bin/console cache:clear -e test
#bin/console doctrine:database:drop --force -e test
#bin/console doctrine:database:create -e test
bin/console doctrine:schema:create -e test
bin/console sylius:fixtures:load -e test
bin/console doctrine:schema:update --dump-sql --force -e test




# If you want to develop something this is one way of starting dev server.
# Later on we will use symfony server because it can handle https way easier.
#bin/console server:run 127.0.0.1:8080 -d public -e test
```

Come back to root directory
```bash
cd ../../
```

Then you should be able to run phpunit and other static code analysis checks.

## PhpUnit

```bash
vendor/bin/phpunit
```


## Static code analysis

### Psalm

```bash
vendor/bin/psalm
```

### PHPStan

```bash
vendor/bin/phpstan analyse -c phpstan.neon -l max src/
```


## Behat (JS scenarios)

    1. [Install Symfony CLI command](https://symfony.com/download).

    2. Start Chrome (all other chrome sessions needs to be closed first):

     Headless
     ```bash
     google-chrome-stable --enable-automation --disable-background-networking --no-default-browser-check --no-first-run --disable-popup-blocking --disable-default-apps --allow-insecure-localhost --disable-translate --disable-extensions --no-sandbox --enable-features=Metal --headless --remote-debugging-port=9222 --window-size=2880,1800 --proxy-server='direct://' --proxy-bypass-list='*' http://127.0.0.1
     ```

     Normal
     ```bash
     google-chrome-stable --enable-automation --disable-background-networking --no-default-browser-check --no-first-run --disable-popup-blocking --disable-default-apps --allow-insecure-localhost --disable-translate --disable-extensions --no-sandbox --enable-features=Metal --remote-debugging-port=9222 --window-size=2880,1800 --proxy-server='direct://' --proxy-bypass-list='*' http://127.0.0.1
     ```

    3. Install SSL certificates (only once needed) and run test application's webserver on `127.0.0.1:8080`:

     ```bash
     symfony server:ca:install
     APP_ENV=test symfony server:start --port=8080 --dir=tests/Application/public --daemon
     ```

    4. Run Behat:

     ```bash
     vendor/bin/behat --strict --tags="@javascript"
     ```


[ico-version]: https://img.shields.io/packagist/v/ecoco/sylius-base-price-plugin.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[link-packagist]: https://packagist.org/packages/ecoco/sylius-base-price-plugin
