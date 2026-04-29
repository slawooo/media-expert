<?php

namespace App\Entity;

use App\Repository\RecordRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RecordRepository::class)]
class Record
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $number = null;

    #[ORM\Column]
    private ?DateTimeImmutable $createdAt = null;

    #[ORM\Column(length: 255)]
    private ?string $currentStatus = null;

    /**
     * @var Collection<int, StatusLog>
     */
    #[ORM\OneToMany(targetEntity: StatusLog::class, mappedBy: 'record', cascade: ['persist'], orphanRemoval: true)]
    #[ORM\OrderBy(['createdAt' => 'ASC'])]
    private Collection $statusHistory;

    public function __construct()
    {
        $this->statusHistory = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumber(): ?string
    {
        return $this->number;
    }

    public function setNumber(string $number): static
    {
        $this->number = $number;

        return $this;
    }

    public function getCreatedAt(): ?DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getCurrentStatus(): ?string
    {
        return $this->currentStatus;
    }

    public function setCurrentStatus(string $currentStatus): static
    {
        $this->currentStatus = $currentStatus;

        return $this;
    }

    /**
     * @return Collection<int, StatusLog>
     */
    public function getStatusHistory(): Collection
    {
        return $this->statusHistory;
    }

    public function addStatusLog(StatusLog $statusLog): static
    {
        if (!$this->statusHistory->contains($statusLog)) {
            $this->statusHistory->add($statusLog);
            $statusLog->setRecord($this);
        }

        return $this;
    }

    public function removeStatusLog(StatusLog $statusLog): static
    {
        $this->statusHistory->removeElement($statusLog);

        return $this;
    }

    public function changeStatus(string $status, ?DateTimeImmutable $changedAt = null): void
    {
        $changedAt ??= new DateTimeImmutable();

        $this->currentStatus = $status;

        $statusLog = new StatusLog();
        $statusLog->setStatus($status);
        $statusLog->setCreatedAt($changedAt);

        $this->addStatusLog($statusLog);
    }
}
