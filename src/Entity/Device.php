<?php

namespace App\Entity;

use App\Repository\DeviceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DeviceRepository::class)]
class Device
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'devices')]
    #[ORM\JoinColumn(nullable: false)]
    private ?OverallDevice $overallDevice = null;

    #[ORM\Column(length: 255)]
    private ?string $serialNumber = null;

    #[ORM\Column(length: 255)]
    private ?string $type = null;

    /**
     * @var Collection<int, DeviceMetrics>
     */
    #[ORM\OneToMany(targetEntity: DeviceMetrics::class, mappedBy: 'device')]
    private Collection $deviceMetrics;

    public function __construct()
    {
        $this->deviceMetrics = new ArrayCollection();
    }

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

    public function getSerialNumber(): ?string
    {
        return $this->serialNumber;
    }

    public function setSerialNumber(string $serialNumber): static
    {
        $this->serialNumber = $serialNumber;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

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
            $deviceMetric->setDevice($this);
        }

        return $this;
    }

    public function removeDeviceMetric(DeviceMetrics $deviceMetric): static
    {
        if ($this->deviceMetrics->removeElement($deviceMetric)) {
            // set the owning side to null (unless already changed)
            if ($deviceMetric->getDevice() === $this) {
                $deviceMetric->setDevice(null);
            }
        }

        return $this;
    }
}
