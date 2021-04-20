<?php
declare(strict_types=1);

namespace Tests\Ecocode\SyliusBasePricePlugin\Application\Tests\Menu;

use Ecocode\SyliusBasePricePlugin\Menu\AdminProductVariantFormMenuListener;
use Knp\Menu\MenuFactory;
use Knp\Menu\MenuItem;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\AdminBundle\Event\ProductVariantMenuBuilderEvent;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Contracts\Translation\TranslatorTrait;

class AdminProductVariantFormMenuListenerTest extends TestCase
{
    public function testAddItems()
    {
        $event = $this->createPartialMock(
            ProductVariantMenuBuilderEvent::class,
            ['getMenu']
        );

        $formFactory = new MenuFactory();
        $menuItem    = new MenuItem('test', $formFactory);
        $event->expects($this->once())->method('getMenu')->willReturn($menuItem);

        $listener = new AdminProductVariantFormMenuListener($this->getTranslator());

        $listener->addItems($event);

        $this->assertEquals('test', $menuItem->getName());
        $this->assertInstanceOf(MenuItem::class, $menuItem->getChild('base_price'));
        $this->assertEquals('base_price', $menuItem->getChild('base_price')->getName());

        $this->assertEquals(
            'ecocode_sylius_base_price_plugin.admin.tab',
            $menuItem->getChild('base_price')->getLabel()
        );

        $this->assertEquals(
            '@EcocodeSyliusBasePricePlugin/Resources/views/Admin/ProductVariant/Tab/baseprice.html.twig',
            $menuItem->getChild('base_price')->getAttribute('template')
        );
    }

    public function getTranslator()
    {
        return new class() implements TranslatorInterface {
            use TranslatorTrait;
        };
    }
}
