<?php

namespace App\Tests\Functional\Api;

use App\Entity\Record;
use App\Entity\StatusLog;
use App\Tests\Functional\FunctionalTestCase;
use DateTimeImmutable;
use JsonException;

class RecordControllerTest extends FunctionalTestCase
{
    /**
     * @throws JsonException
     */
    public function testCreateRecord(): void
    {
        // Given:
        $payload = [
            'number' => 'REC-100',
            'status' => 'new',
        ];

        // When:
        $this->client->request(
            'POST',
            '/api/records',
            server: [
                'PHP_AUTH_USER' => 'api',
                'PHP_AUTH_PW' => 'secret',
                'CONTENT_TYPE' => 'application/json',
            ],
            content: json_encode($payload, JSON_THROW_ON_ERROR)
        );

        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        // Then:
        self::assertResponseStatusCodeSame(201);
        self::assertIsArray($responseData);
        self::assertSame('REC-100', $responseData['number']);
        self::assertSame('new', $responseData['currentStatus']);
        self::assertArrayHasKey('statusHistory', $responseData);
        self::assertCount(1, $responseData['statusHistory']);
        self::assertSame('new', $responseData['statusHistory'][0]['status']);

        $record = $this->entityManager
            ->getRepository(Record::class)
            ->findOneBy(['number' => 'REC-100']);

        self::assertInstanceOf(Record::class, $record);
        self::assertSame('new', $record->getCurrentStatus());
        self::assertCount(1, $record->getStatusHistory());
    }

    public function testGetRecordsReturnsPersistedRecords(): void
    {
        // Given:
        $firstRecord = new Record();
        $firstRecord->setNumber('REC-001');
        $firstRecord->setCreatedAt(new DateTimeImmutable());
        $firstRecord->setCurrentStatus('new');

        $firstStatusLog = new StatusLog();
        $firstStatusLog->setStatus('new');
        $firstStatusLog->setCreatedAt(new DateTimeImmutable());

        $firstRecord->addStatusLog($firstStatusLog);

        $secondRecord = new Record();
        $secondRecord->setNumber('REC-002');
        $secondRecord->setCreatedAt(new DateTimeImmutable());
        $secondRecord->setCurrentStatus('processing');

        $secondStatusLog = new StatusLog();
        $secondStatusLog->setStatus('processing');
        $secondStatusLog->setCreatedAt(new DateTimeImmutable());

        $secondRecord->addStatusLog($secondStatusLog);

        $this->entityManager->persist($firstRecord);
        $this->entityManager->persist($secondRecord);
        $this->entityManager->flush();

        // When:
        $this->client->request(
            'GET',
            '/api/records',
            server: [
                'PHP_AUTH_USER' => 'api',
                'PHP_AUTH_PW' => 'secret',
            ]
        );

        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        // Then:
        self::assertResponseStatusCodeSame(200);
        self::assertIsArray($responseData);
        self::assertCount(2, $responseData);

        $numbers = [];
        foreach ($responseData as $recordData) {
            $numbers[] = $recordData['number'];
        }

        self::assertContains('REC-001', $numbers);
        self::assertContains('REC-002', $numbers);
    }
}
