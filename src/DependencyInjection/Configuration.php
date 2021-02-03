<?php

declare(strict_types=1);

namespace Ecocode\SyliusBasePricePlugin\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

/**
 * Class Configuration
 * @package Ecocode\SyliusBasePricePlugin\DependencyInjection
 */
final class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('ecocode_sylius_base_price');
        $rootNode    = $treeBuilder->getRootNode();

        if ($rootNode instanceof ArrayNodeDefinition) {
            $this->addMainConfig($rootNode);
            $this->addUnitMappingConfig($rootNode);
        }

        return $treeBuilder;
    }

    protected function addMainConfig(ArrayNodeDefinition $rootNode): self
    {
        $rootNode->children()->booleanNode('use_short_unit_name')->defaultTrue()->end();

        return $this;
    }

    protected function addUnitMappingConfig(ArrayNodeDefinition $rootNode): self
    {
        // @phpstan-ignore-next-line
        $rootNode
            ->children()
                ->arrayNode('mapping')
                    ->useAttributeAsKey('key')->cannotBeEmpty()
                    ->arrayPrototype()
                        ->arrayPrototype()
                            ->children()
                                ->scalarNode('unit')->cannotBeEmpty()->isRequired()->end()
                                ->floatNode('ifMoreThan')->defaultValue(0)->end()
                                ->floatNode('mod')->defaultValue(1)->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $this;
    }
}

