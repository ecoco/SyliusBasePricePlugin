<?php

declare(strict_types=1);

namespace Ecocode\SyliusBasePricePlugin\DependencyInjection;

use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

/**
 * Class EcocodeSyliusBasePriceExtension
 * @package Ecocode\SyliusBasePricePlugin\DependencyInjection
 */
final class EcocodeSyliusBasePriceExtension extends Extension
{
    /**
     * @param array<string, mixed> $config
     * @param ContainerBuilder     $container
     *
     * @throws \Exception
     */
    public function load(array $config, ContainerBuilder $container): void
    {
        // if mapping override is set then drop default config altogether
        if (!empty($config[1]['mapping']) && isset($config[0]['mapping'])) {
            $config[0]['mapping'] = [];
        }

        $config = $this->processConfiguration($this->getConfiguration([], $container), $config);

        $container->setParameter('ecocode_sylius_base_price', $config);
        $container->setParameter('ecocode_sylius_base_price.mapping', $config['mapping']);
        $container->setParameter('ecocode_sylius_base_price.measurements', array_keys($config['mapping']));
        $container->setParameter('ecocode_sylius_base_price.use_short_unit_name', $config['use_short_unit_name']);

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));

        $loader->load('services.yml');
    }

    /**
     * @param array<string, mixed> $config
     * @param ContainerBuilder     $container
     *
     * @return ConfigurationInterface
     */
    public function getConfiguration(array $config, ContainerBuilder $container): ConfigurationInterface
    {
        return new Configuration();
    }
}
