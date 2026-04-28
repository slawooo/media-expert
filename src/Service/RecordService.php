<?php

namespace App\Service;

use App\Entity\Record;
use App\Entity\StatusLog;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;

readonly class RecordService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function create(string $number, string $status): Record
    {
        $now = new DateTimeImmutable();

        $record = new Record();
        $record->setNumber($number);
        $record->setCreatedAt($now);
        $record->setCurrentStatus($status);

        $statusLog = new StatusLog();
        $statusLog->setStatus($status);
        $statusLog->setCreatedAt($now);

        $record->addStatusLog($statusLog);

        $this->entityManager->persist($record);
        $this->entityManager->flush();

        return $record;
    }

    public function update(Record $record, string $number): Record
    {
        $record->setNumber($number);

        $this->entityManager->flush();

        return $record;
    }

    public function changeStatus(Record $record, string $status): Record
    {
        $record->setCurrentStatus($status);

        $statusLog = new StatusLog();
        $statusLog->setStatus($status);
        $statusLog->setCreatedAt(new DateTimeImmutable());

        $record->addStatusLog($statusLog);

        $this->entityManager->flush();

        return $record;
    }

    public function delete(Record $record): void
    {
        $this->entityManager->remove($record);
        $this->entityManager->flush();
    }
}
