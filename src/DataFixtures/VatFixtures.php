<?php

namespace App\DataFixtures;

use App\Entity\Vat;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class VatFixtures extends Fixture
{
    public const REFERENCE = "vat_";
    public const VATS = [
        [
            "label" => "TVA 20%",
            "value" => 0.20
        ],
        [
            "label" => "TVA 10%",
            "value" => 0.10
        ],
        [
            "label" => "TVA 5.5%",
            "value" => 0.055
        ],
        [
            "label" => "TVA 2.1%",
            "value" => 0.021
        ],
        [
            "label" => "TVA 0%",
            "value" => 0.0
        ]
    ];

    public function load(ObjectManager $manager): void {
        foreach (self::VATS as $index => $vatData) {
            $vat = new Vat();
            $vat->setLabel($vatData["label"]);
            $vat->setValue($vatData["value"]);

            $manager->persist($vat);

            $this->addReference(self::REFERENCE.$index, $vat);
        }
        $manager->flush();
    }
}
