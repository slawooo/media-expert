<?php

namespace App\Tests\Unit\Mapper;

use App\Entity\Record;
use App\Entity\StatusLog;
use App\Mapper\RecordResponseMapper;
use DateTimeImmutable;
use DateTimeZone;
use Exception;
use PHPUnit\Framework\TestCase;

class RecordResponseMapperTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testMap(): void
    {
        // Given:
        $record = new Record();
        $record->setNumber('REC-001');
        $record->setCreatedAt(new DateTimeImmutable('2025-01-01 10:00:00', new DateTimeZone('UTC')));
        $record->setCurrentStatus('new');

        $mapper = new RecordResponseMapper();

        // When:
        $result = $mapper->map($record);

        // Then:
        self::assertSame([
            'id' => null,
            'number' => 'REC-001',
            'createdAt' => '2025-01-01T10:00:00+00:00',
            'currentStatus' => 'new',
        ], $result);
    }

    /**
     * @throws Exception
     */
    public function testMapWithStatusHistory(): void
    {
        // Given:
        $record = new Record();
        $record->setNumber('REC-002');
        $record->setCreatedAt(new DateTimeImmutable('2025-01-02 12:00:00', new DateTimeZone('UTC')));
        $record->setCurrentStatus('processing');

        $firstStatusLog = new StatusLog();
        $firstStatusLog->setStatus('new');
        $firstStatusLog->setCreatedAt(new DateTimeImmutable('2025-01-02 12:00:00', new DateTimeZone('UTC')));

        $secondStatusLog = new StatusLog();
        $secondStatusLog->setStatus('processing');
        $secondStatusLog->setCreatedAt(new DateTimeImmutable('2025-01-02 13:00:00', new DateTimeZone('UTC')));

        $record->addStatusLog($firstStatusLog);
        $record->addStatusLog($secondStatusLog);

        $mapper = new RecordResponseMapper();

        // When:
        $result = $mapper->map($record, true);

        // Then:
        self::assertSame([
            'id' => null,
            'number' => 'REC-002',
            'createdAt' => '2025-01-02T12:00:00+00:00',
            'currentStatus' => 'processing',
            'statusHistory' => [
                [
                    'id' => null,
                    'status' => 'new',
                    'createdAt' => '2025-01-02T12:00:00+00:00',
                ],
                [
                    'id' => null,
                    'status' => 'processing',
                    'createdAt' => '2025-01-02T13:00:00+00:00',
                ],
            ],
        ], $result);
    }
}
