<?php

declare(strict_types=1);

namespace Ecocode\SyliusBasePricePlugin\Form\Extension;

use Ecocode\SyliusBasePricePlugin\Form\Type\BasePriceUnitChoiceType;
use Sylius\Bundle\ProductBundle\Form\Type\ProductVariantType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class ProductVariantTypeExtension
 * @package Ecocode\SyliusBasePricePlugin\Form\Extension
 */
class ProductVariantTypeExtension extends AbstractTypeExtension
{
    /**
     * @param FormBuilderInterface<FormBuilder> $builder
     * @param array<string, mixed>              $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add(
            'basePriceUnit',
            BasePriceUnitChoiceType::class,
            [
                'required'    => false,
                'placeholder' => '---',
                'label'       => 'ecocode_sylius_base_price_plugin.admin.unit_label',
            ]
        );

        $builder->add(
            'basePriceValue',
            IntegerType::class,
            [
                'required' => false,
                'label'    => 'ecocode_sylius_base_price_plugin.admin.value_label',
                'help'     => 'ecocode_sylius_base_price_plugin.admin.value_label_help',
            ]
        );
    }

    /**
     * @return string[]
     */
    public static function getExtendedTypes(): array
    {
        return [
            ProductVariantType::class
        ];
    }
}
