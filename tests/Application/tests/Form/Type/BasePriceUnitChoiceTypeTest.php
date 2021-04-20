<?php
declare(strict_types=1);

namespace Tests\Ecocode\SyliusBasePricePlugin\Application\Tests\Form\Type;

use Ecocode\SyliusBasePricePlugin\Form\Type\BasePriceUnitChoiceType;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Contracts\Translation\TranslatorTrait;
use UnitConverter\Unit\Mass\Kilogram;
use UnitConverter\UnitConverter;

class BasePriceUnitChoiceTypeTest extends TestCase
{
    public function testGetParent()
    {
        $choice = new BasePriceUnitChoiceType(
            $this->getTranslator(),
            $this->getUnitConverter()
        );

        $response = $choice->getParent();
        $this->assertEquals(ChoiceType::class, $response);
    }

    public function getTranslator()
    {
        return new class() implements TranslatorInterface {
            use TranslatorTrait;
        };
    }

    public function getUnitConverter(): UnitConverter
    {
        $converter = UnitConverter::createBuilder()
            ->addSimpleCalculator()
            ->addRegistryWith([new Kilogram()])
            ->build();

        return $converter;
    }

    public function testBlockPrefix()
    {
        $choice = new BasePriceUnitChoiceType(
            $this->getTranslator(),
            $this->getUnitConverter()
        );

        $response = $choice->getBlockPrefix();
        $this->assertEquals('sylius_base_price_unit_choice', $response);
    }

    public function testConfigureOptions()
    {
        $choice = new BasePriceUnitChoiceType(
            $this->getTranslator(),
            $this->getUnitConverter()
        );

        $resolver = $this->createPartialMock(OptionsResolver::class, ['setDefaults']);
        $resolver->expects($this->once())->method('setDefaults')->with(
            [
                'choice_translation_domain' => false,
                'choices'                   => [
                    'mass' => ['kilogram (kg)' => 'kg']
                ]
            ]
        );

        $choice->configureOptions($resolver);
    }
}
