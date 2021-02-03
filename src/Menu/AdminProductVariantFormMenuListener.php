<?php

declare(strict_types=1);

namespace Ecocode\SyliusBasePricePlugin\Menu;

use Sylius\Bundle\AdminBundle\Event\ProductVariantMenuBuilderEvent;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Adds new tab in admin -> product -> variants -> edit page
 *
 * Class AdminProductVariantFormMenuListener
 * @package Ecocode\SyliusBasePricePlugin\Menu
 */
final class AdminProductVariantFormMenuListener
{
    /** @var TranslatorInterface */
    private $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function addItems(ProductVariantMenuBuilderEvent $event): void
    {
        $menu = $event->getMenu();

        $view = '@EcocodeSyliusBasePricePlugin/Resources/views/Admin/ProductVariant/Tab/baseprice.html.twig';
        $menu
            ->addChild('base_price', ['position' => 1])
            ->setAttribute('template', $view)
            ->setLabel($this->translator->trans('ecocode_sylius_base_price_plugin.admin.tab'))
            ->setLabelAttribute('icon', 'weight')
        ;
    }
}
