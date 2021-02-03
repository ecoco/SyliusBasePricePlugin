<?php

declare(strict_types=1);

namespace Ecocode\SyliusBasePricePlugin\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;
use UnitConverter\UnitConverter;

/**
 * Class BasePriceUnitChoiceType
 * @package Ecocode\SyliusBasePricePlugin\Form\Type
 */
class BasePriceUnitChoiceType extends AbstractType
{
    /** @var TranslatorInterface */
    private $translator;

    /** @var UnitConverter */
    private $converter;

    public function __construct(TranslatorInterface $translator, UnitConverter $converter)
    {
        $this->translator = $translator;
        $this->converter  = $converter;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $translated  = ['choice_translation_domain' => false];
        $registry    = $this->converter->getRegistry();
        $measurments = $registry->listMeasurements();

        foreach ($measurments as $measurement) {
            $units = $registry->listUnits($measurement);
            foreach ($units as $symbol) {
                $unit = $registry->loadUnit($symbol);
                if (!$unit) {
                    continue;
                }

                $symbol = (string)$unit->getSymbol();
                $text   = sprintf('%s (%s)', (string)$unit->getName(), $symbol);

                $translated['choices'][$measurement][$text] = $symbol;
            }
        }

        $resolver->setDefaults($translated);
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }

    public function getBlockPrefix(): string
    {
        return 'sylius_base_price_unit_choice';
    }
}
