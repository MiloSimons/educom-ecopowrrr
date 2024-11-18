<?php

namespace App\Entity;

use App\Repository\DeviceMetricsRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DeviceMetricsRepository::class)]
class DeviceMetrics
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'deviceMetrics')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Device $device = null;

    #[ORM\ManyToOne(inversedBy: 'deviceMetrics')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Status $status = null;

    #[ORM\ManyToOne(inversedBy: 'deviceMetrics')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Price $price = null;

    #[ORM\Column]
    private ?float $totalYield = null;

    #[ORM\Column]
    private ?float $monthlyYield = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDevice(): ?Device
    {
        return $this->device;
    }

    public function setDevice(?Device $device): static
    {
        $this->device = $device;

        return $this;
    }

    public function getStatus(): ?Status
    {
        return $this->status;
    }

    public function setStatus(?Status $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getPrice(): ?Price
    {
        return $this->price;
    }

    public function setPrice(?Price $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getTotalYield(): ?float
    {
        return $this->totalYield;
    }

    public function setTotalYield(float $totalYield): static
    {
        $this->totalYield = $totalYield;

        return $this;
    }

    public function getMonthlyYield(): ?float
    {
        return $this->monthlyYield;
    }

    public function setMonthlyYield(float $monthlyYield): static
    {
        $this->monthlyYield = $monthlyYield;

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
}
