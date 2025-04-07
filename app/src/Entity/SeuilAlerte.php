<?php

namespace App\Entity;

use App\Repository\SeuilAlerteRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SeuilAlerteRepository::class)]
class SeuilAlerte
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $seuilCrit = null;

    #[ORM\OneToOne(inversedBy: 'seuilAlerte', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Services $service = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSeuilCrit(): ?int
    {
        return $this->seuilCrit;
    }

    public function setSeuilCrit(int $seuilCrit): static
    {
        $this->seuilCrit = $seuilCrit;

        return $this;
    }

    public function getService(): ?Services
    {
        return $this->service;
    }

    public function setService(Services $service): static
    {
        $this->service = $service;

        return $this;
    }
}
