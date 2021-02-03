<?php

declare(strict_types=1);

namespace Ecocode\SyliusBasePricePlugin\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Installs base_price_unit and base_price_value columns in sylius_product_variant table
 *
 * Class Version20210128101647
 * @package Ecocode\SyliusBasePricePlugin\Migrations
 */
final class Version20210128101647 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '@EcocodeSyliusBasePricePlugin installation. Extending table "sylius_product_variant".';
    }

    public function up(Schema $schema) : void
    {
        $this->addSql('ALTER TABLE sylius_product_variant ADD base_price_unit VARCHAR(8) DEFAULT NULL, ADD base_price_value INT DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        $this->addSql('ALTER TABLE sylius_product_variant DROP base_price_unit, DROP base_price_value');
    }
}
