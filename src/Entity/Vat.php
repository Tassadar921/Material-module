<?php

namespace App\Entity;

use App\Entity\Trait\Timestampable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use App\Repository\VatRepository;
use Symfony\Component\Uid\Uuid;
use Doctrine\DBAL\Types\Types;

#[ORM\Entity(repositoryClass: VatRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Vat
{
    use Timestampable;

    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'NONE')]
    private Uuid $id;

    #[ORM\Column(type: Types::STRING, length: 255)]
    private ?string $label = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 3, scale: 2)]
    #[Assert\Range(notInRangeMessage: 'vat.value.range_error', min: 0, max: 1)]
    private ?float $value = null;

    #[ORM\OneToMany(targetEntity: Material::class, mappedBy: 'vat')]
    private Collection $materials;

    public function __construct()
    {
        $this->id = Uuid::v4();
        $materials = new ArrayCollection();
    }

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function getValue(): ?float
    {
        return $this->value;
    }

    public function setValue(float $value): self
    {
        $this->value = $value;

        return $this;
    }

    public function getMaterials(): Collection
    {
        return $this->materials;
    }

    public function addMaterial(Material $material): self
    {
        if (!$this->materials->contains($material)) {
            $this->materials[] = $material;
            $material->setVat($this);
        }

        return $this;
    }

    public function removeMaterial(Material $material): self
    {
        if ($this->materials->removeElement($material)) {
            if ($material->getVat() === $this) {
                $material->setVat(null);
            }
        }

        return $this;
    }
}
