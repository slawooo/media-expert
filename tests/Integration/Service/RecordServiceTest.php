<?php

namespace App\Tests\Integration\Service;

use App\Entity\Record;
use App\Entity\StatusLog;
use App\Service\RecordService;
use App\Tests\Integration\IntegrationTestCase;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;

class RecordServiceTest extends IntegrationTestCase
{
    private EntityManagerInterface $entityManager;
    private RecordService $recordService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->entityManager = self::getContainer()->get(EntityManagerInterface::class);
        $this->recordService = self::getContainer()->get(RecordService::class);
    }

    /**
     * @throws ORMException
     */
    public function testChangeStatus(): void
    {
        // Given:
        $record = new Record();
        $record->setNumber('TEST-001');
        $record->setCreatedAt(new DateTimeImmutable());
        $record->setCurrentStatus('new');

        $initialStatusLog = new StatusLog();
        $initialStatusLog->setStatus('new');
        $initialStatusLog->setCreatedAt(new DateTimeImmutable());

        $record->addStatusLog($initialStatusLog);

        $this->entityManager->persist($record);
        $this->entityManager->flush();

        // When:
        $this->recordService->changeStatus($record, 'processing');
        $this->entityManager->refresh($record);

        // Then:
        self::assertSame('processing', $record->getCurrentStatus());
        self::assertCount(2, $record->getStatusHistory());

        $statuses = [];
        foreach ($record->getStatusHistory() as $statusLog) {
            $statuses[] = $statusLog->getStatus();
        }

        self::assertSame(['new', 'processing'], $statuses);
    }
}
