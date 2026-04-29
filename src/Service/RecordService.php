<?php

namespace App\Service;

use App\Entity\Record;
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
        $record->changeStatus($status, $now);

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
        $record->changeStatus($status);

        $this->entityManager->flush();

        return $record;
    }

    public function delete(Record $record): void
    {
        $this->entityManager->remove($record);
        $this->entityManager->flush();
    }
}
