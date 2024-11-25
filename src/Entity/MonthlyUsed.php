<?php

namespace App\Entity;

use App\Repository\MonthlyUsedRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MonthlyUsedRepository::class)]
class MonthlyUsed
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'monthlyUseds')]
    #[ORM\JoinColumn(nullable: false)]
    private ?OverallDevice $overallDevice = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column]
    private ?float $monthlyKwHUsed = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOverallDevice(): ?OverallDevice
    {
        return $this->overallDevice;
    }

    public function setOverallDevice(?OverallDevice $overallDevice): static
    {
        $this->overallDevice = $overallDevice;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getMonthlyKwHUsed(): ?float
    {
        return $this->monthlyKwHUsed;
    }

    public function setMonthlyKwHUsed(float $monthlyKwHUsed): static
    {
        $this->monthlyKwHUsed = $monthlyKwHUsed;

        return $this;
    }
}
