<?php

namespace App\Tests\Unit\Service;

use App\Entity\Record;
use App\Service\RecordService;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;

class RecordServiceTest extends TestCase
{
    /**
     * @throws Exception
     */
    #[DataProvider('statusProvider')]
    public function testChangeStatus(string $initialStatus, string $newStatus): void
    {
        // Given:
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager
            ->expects(self::once())
            ->method('flush');

        $record = new Record();
        $record->setNumber('REC-001');
        $record->setCreatedAt(new DateTimeImmutable('2025-01-01 10:00:00'));
        $record->setCurrentStatus($initialStatus);

        $service = new RecordService($entityManager);

        // When:
        $service->changeStatus($record, $newStatus);

        // Then:
        self::assertSame($newStatus, $record->getCurrentStatus());
        self::assertCount(1, $record->getStatusHistory());

        $statusLogs = $record->getStatusHistory()->toArray();
        self::assertSame($newStatus, $statusLogs[0]->getStatus());
    }

    public static function statusProvider(): array
    {
        return [
            'new to processing' => ['new', 'processing'],
            'processing to completed' => ['processing', 'completed'],
            'new to cancelled' => ['new', 'cancelled'],
        ];
    }
}
