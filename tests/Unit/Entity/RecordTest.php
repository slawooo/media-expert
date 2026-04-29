<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Record;
use App\Entity\StatusLog;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class RecordTest extends TestCase
{
    public function testAddStatusLog(): void
    {
        // Given:
        $record = new Record();
        $statusLog = new StatusLog();
        $statusLog->setStatus('new');
        $statusLog->setCreatedAt(new DateTimeImmutable('2025-01-01 10:00:00'));

        // When:
        $record->addStatusLog($statusLog);

        // Then:
        self::assertCount(1, $record->getStatusHistory());
        self::assertSame($record, $statusLog->getRecord());

        $statusLogs = $record->getStatusHistory()->toArray();
        self::assertSame($statusLog, $statusLogs[0]);
    }

    public function testChangeStatus(): void
    {
        // Given:
        $record = new Record();
        $record->setNumber('REC-001');
        $record->setCreatedAt(new DateTimeImmutable('2025-01-01 09:00:00'));
        $record->setCurrentStatus('new');

        $changedAt = new DateTimeImmutable('2025-01-01 10:00:00');

        // When:
        $record->changeStatus('processing', $changedAt);

        // Then:
        self::assertSame('processing', $record->getCurrentStatus());
        self::assertCount(1, $record->getStatusHistory());

        $statusLogs = $record->getStatusHistory()->toArray();
        $statusLog = $statusLogs[0];

        self::assertInstanceOf(StatusLog::class, $statusLog);
        self::assertSame('processing', $statusLog->getStatus());
        self::assertSame('2025-01-01 10:00:00', $statusLog->getCreatedAt()?->format('Y-m-d H:i:s'));
        self::assertSame($record, $statusLog->getRecord());
    }
}
