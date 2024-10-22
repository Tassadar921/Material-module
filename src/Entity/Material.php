<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;
use Doctrine\DBAL\Types\Types;
use App\Repository\MaterialRepository;
use App\Entity\Trait\Timestampable;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: MaterialRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Material
{
    use Timestampable;

    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'NONE')]
    private Uuid $id;

    #[ORM\Column(type: Types::STRING, length: 255)]
    #[Assert\NotBlank]
    private ?string $name = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\NotBlank]
    private ?float $priceTaxFree = null;

    #[ORM\ManyToOne(targetEntity: Vat::class, inversedBy: 'materials')]
    #[Assert\NotBlank]
    private ?Vat $vat = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\NotBlank]
    private ?float $priceTaxIncluded = null;

    #[ORM\Column(type: Types::INTEGER)]
    private int $quantity = 0;

    public function __construct()
    {
        $this->id = Uuid::v4();
    }

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getPriceTaxFree(): ?float
    {
        return $this->priceTaxFree;
    }

    public function setPriceTaxFree(float $priceTaxFree): self
    {
        $this->priceTaxFree = $priceTaxFree;
        $this->setPriceTaxIncluded();

        return $this;
    }

    public function getVat(): ?Vat
    {
        return $this->vat;
    }

    public function setVat(?Vat $vat): self
    {
        $this->vat = $vat;
        $this->setPriceTaxIncluded();

        return $this;
    }

    public function getPriceTaxIncluded(): ?float
    {
        return $this->priceTaxIncluded;
    }

    private function setPriceTaxIncluded(): void
    {
        if ($this->priceTaxFree !== null && $this->vat !== null) {
            $this->priceTaxIncluded = $this->priceTaxFree * (1 + $this->vat->getValue());
        }
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function setQuantity(?int $quantity): self
    {
        $this->quantity = $quantity ?? 0;

        return $this;
    }

    public function decrementQuantity(): self
    {
        if ($this->quantity > 0) {
            $this->quantity--;
        }

        return $this;
    }

    public function incrementQuantity(): self
    {
        $this->quantity++;

        return $this;
    }
}
