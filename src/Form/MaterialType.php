<?php

namespace App\Form;

use App\Entity\Material;
use App\Entity\Vat;
use App\Repository\VatRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MaterialType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'common.name',
            ])
            ->add('priceTaxFree', MoneyType::class, [
                'label' => 'material.price_tax_free',
                'currency' => 'EUR',
            ])
            ->add('vat', EntityType::class, [
                'class' => Vat::class,
                'choice_label' => 'label',
                'label' => 'material.vat',
                'choice_attr' => function(Vat $vat) {
                    return ['data-rate' => $vat->getValue()];
                },
                'query_builder' => function (VatRepository $vatRepository) {
                    return $vatRepository->createQueryBuilder('v')->orderBy('v.value', 'ASC');
                },
            ])
            ->add('priceTaxIncluded', MoneyType::class, [
                'label' => 'material.price_tax_included',
                'currency' => 'EUR',
                'attr' => ['readonly' => true],
                'disabled' => true,
            ])
            ->add('quantity', IntegerType::class, [
                'label' => 'material.quantity',
            ])
            ->add('save', SubmitType::class, [
                'label' => 'common.save',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Material::class,
        ]);
    }
}
