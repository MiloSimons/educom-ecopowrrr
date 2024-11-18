<?php

namespace App\Entity;

use App\Repository\StatusRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StatusRepository::class)]
class Status
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 15)]
    private ?string $status = null;

    /**
     * @var Collection<int, OverallDevice>
     */
    #[ORM\OneToMany(targetEntity: OverallDevice::class, mappedBy: 'status')]
    private Collection $overallDevices;

    /**
     * @var Collection<int, DeviceMetrics>
     */
    #[ORM\OneToMany(targetEntity: DeviceMetrics::class, mappedBy: 'status')]
    private Collection $deviceMetrics;

    public function __construct()
    {
        $this->overallDevices = new ArrayCollection();
        $this->deviceMetrics = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return Collection<int, OverallDevice>
     */
    public function getOverallDevices(): Collection
    {
        return $this->overallDevices;
    }

    public function addOverallDevice(OverallDevice $overallDevice): static
    {
        if (!$this->overallDevices->contains($overallDevice)) {
            $this->overallDevices->add($overallDevice);
            $overallDevice->setStatus($this);
        }

        return $this;
    }

    public function removeOverallDevice(OverallDevice $overallDevice): static
    {
        if ($this->overallDevices->removeElement($overallDevice)) {
            // set the owning side to null (unless already changed)
            if ($overallDevice->getStatus() === $this) {
                $overallDevice->setStatus(null);
            }
        }

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
            $deviceMetric->setStatus($this);
        }

        return $this;
    }

    public function removeDeviceMetric(DeviceMetrics $deviceMetric): static
    {
        if ($this->deviceMetrics->removeElement($deviceMetric)) {
            // set the owning side to null (unless already changed)
            if ($deviceMetric->getStatus() === $this) {
                $deviceMetric->setStatus(null);
            }
        }

        return $this;
    }
}
