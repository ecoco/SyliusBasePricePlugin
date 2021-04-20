<?php
declare(strict_types=1);

namespace Tests\Ecocode\SyliusBasePricePlugin\Application\Tests\Form\Extension;

use Ecocode\SyliusBasePricePlugin\Form\Extension\ProductVariantTypeExtension;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\ProductBundle\Form\Type\ProductVariantType;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\Form\FormRegistry;
use Symfony\Component\Form\ResolvedFormType;
use Tests\Ecocode\SyliusBasePricePlugin\Application\Entity\Product\ProductVariant;

class ProductVariantTypeExtensionTest extends TestCase
{
    public function testBuildForm()
    {
        $eventDispatcher = $this->createPartialMock(
            EventDispatcher::class,
            ['addListener', 'addSubscriber', 'dispatch']
        );

        $formRegistry = $this->createPartialMock(
            FormRegistry::class,
            ['getType']
        );

        $resolvedFormType = $this->createPartialMock(
            ResolvedFormType::class,
            ['createBuilder', 'buildForm']
        );

        $formFactory = new FormFactory($formRegistry);
        $formBuilder = new FormBuilder(
            'builder_name_test',
            ProductVariant::class,
            $eventDispatcher,
            $formFactory
        );

        $resolvedFormType
            ->expects($this->any())
            ->method('createBuilder')
            ->willReturn($formBuilder);

        $formRegistry
            ->expects($this->any())
            ->method('getType')
            ->willReturn($resolvedFormType);

        $extension = new ProductVariantTypeExtension();
        $extension->buildForm($formBuilder, []);

        $this->assertInstanceOf(FormBuilder::class, $formBuilder->get('basePriceUnit'));
        $this->assertInstanceOf(FormBuilder::class, $formBuilder->get('basePriceValue'));

        $this->assertEquals('builder_name_test', $formBuilder->get('basePriceValue')->getName());
    }

    public function testGetExtendedTypes()
    {
        $formBuilder = $this->createMock(FormBuilder::class);

        $extension = new ProductVariantTypeExtension();
        $extension->buildForm($formBuilder, []);

        $response = $extension->getExtendedTypes();
        $this->assertEquals([ProductVariantType::class], $response);
    }
}
