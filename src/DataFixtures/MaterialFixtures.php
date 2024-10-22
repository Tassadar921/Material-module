<?php

namespace App\DataFixtures;

use App\Entity\Material;
use App\Entity\Vat;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class MaterialFixtures extends Fixture implements DependentFixtureInterface
{
    public const MATERIALS = [
        [
            "name" => "Papier",
            "priceHT" => 10.00,
            "vat" => 0,
            "quantity" => 100
        ],
        [
            "name" => "Stylo",
            "priceHT" => 1.00,
            "vat" => 1,
            "quantity" => 1000
        ],
        [
            "name" => "Cahier",
            "priceHT" => 2.00,
            "vat" => 2,
            "quantity" => 500
        ],
        [
            "name" => "Crayon",
            "priceHT" => 0.50,
            "vat" => 3,
            "quantity" => 2000
        ],
        [
            "name" => "Gomme",
            "priceHT" => 0.75,
            "vat" => 4,
            "quantity" => 500
        ]
    ];

    public function load(ObjectManager $manager): void {
        foreach (self::MATERIALS as $materialData) {
            $material = new Material();
            $material->setName($materialData["name"]);
            $material->setPriceTaxFree($materialData["priceHT"]);
            $material->setVat($this->getReference(VatFixtures::REFERENCE.$materialData["vat"]));
            $material->setQuantity($materialData["quantity"]);

            $manager->persist($material);
        }
        $manager->flush();
    }

    public function getDependencies(): array {
        return [
            VatFixtures::class
        ];
    }
}
