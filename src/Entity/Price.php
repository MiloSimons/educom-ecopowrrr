<?php

namespace App\Entity;

use App\Repository\PriceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PriceRepository::class)]
class Price
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?float $buyInPrice = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $startDate = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $endDate = null;

    /**
     * @var Collection<int, DeviceMetrics>
     */
    #[ORM\OneToMany(targetEntity: DeviceMetrics::class, mappedBy: 'price')]
    private Collection $deviceMetrics;

    public function __construct()
    {
        $this->deviceMetrics = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBuyInPrice(): ?float
    {
        return $this->buyInPrice;
    }

    public function setBuyInPrice(float $buyInPrice): static
    {
        $this->buyInPrice = $buyInPrice;

        return $this;
    }

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTimeInterface $startDate): static
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->endDate;
    }

    public function setEndDate(?\DateTimeInterface $endDate): static
    {
        $this->endDate = $endDate;

        return $this;
    }

    /**
     * @return Collection<int, DeviceMetrics>
     */
    public function getDeviceMetrics(): Collection
    {
        return $this->deviceMetrics;
    }

    public function addDeviceMetric(DeviceMetrics $deviceMetric): static
    {
        if (!$this->deviceMetrics->contains($deviceMetric)) {
            $this->deviceMetrics->add($deviceMetric);
            $deviceMetric->setPrice($this);
        }

        return $this;
    }

    public function removeDeviceMetric(DeviceMetrics $deviceMetric): static
    {
        if ($this->deviceMetrics->removeElement($deviceMetric)) {
            // set the owning side to null (unless already changed)
            if ($deviceMetric->getPrice() === $this) {
                $deviceMetric->setPrice(null);
            }
        }

        return $this;
    }
}
