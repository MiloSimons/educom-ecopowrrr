<?php

namespace App\Entity;

use App\Repository\OverallDeviceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OverallDeviceRepository::class)]
class OverallDevice
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Client $client = null;

    #[ORM\ManyToOne(inversedBy: 'overallDevices')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Status $status = null;

    #[ORM\Column]
    private ?float $totalKwHUsed = null;

    #[ORM\Column]
    private ?float $monthlyKwHUsed = null;

    /**
     * @var Collection<int, Device>
     */
    #[ORM\OneToMany(targetEntity: Device::class, mappedBy: 'overallDevice')]
    private Collection $devices;

    public function __construct()
    {
        $this->devices = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(Client $client): static
    {
        $this->client = $client;

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

    public function getTotalKwHUsed(): ?float
    {
        return $this->totalKwHUsed;
    }

    public function setTotalKwHUsed(float $totalKwHUsed): static
    {
        $this->totalKwHUsed = $totalKwHUsed;

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

    /**
     * @return Collection<int, Device>
     */
    public function getDevices(): Collection
    {
        return $this->devices;
    }

    public function addDevice(Device $device): static
    {
        if (!$this->devices->contains($device)) {
            $this->devices->add($device);
            $device->setOverallDevice($this);
        }

        return $this;
    }

    public function removeDevice(Device $device): static
    {
        if ($this->devices->removeElement($device)) {
            // set the owning side to null (unless already changed)
            if ($device->getOverallDevice() === $this) {
                $device->setOverallDevice(null);
            }
        }

        return $this;
    }
}
