<?php

namespace App\Tests\Functional\Api;

use App\Entity\Record;
use App\Tests\Functional\FunctionalTestCase;
use DateTimeImmutable;
use Exception;
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

    /**
     * @throws Exception
     */
    public function testGetRecords(): void
    {
        // Given:
        $this->createRecordWithStatus('REC-001', 'new', '2025-01-01 10:00:00');
        $this->createRecordWithStatus('REC-002', 'processing', '2025-01-01 11:00:00');
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

    /**
     * @throws Exception
     */
    public function testGetRecordsFiltered(): void
    {
        // Given:
        $this->createRecordWithStatus('REC-NEW', 'new', '2025-01-01 10:00:00');
        $this->createRecordWithStatus('REC-PROCESSING', 'processing', '2025-01-01 11:00:00');
        $this->entityManager->flush();

        // When:
        $this->client->request(
            'GET',
            '/api/records?currentStatus=processing',
            server: [
                'PHP_AUTH_USER' => 'api',
                'PHP_AUTH_PW' => 'secret',
            ]
        );

        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        // Then:
        self::assertResponseStatusCodeSame(200);
        self::assertIsArray($responseData);
        self::assertCount(1, $responseData);
        self::assertSame('REC-PROCESSING', $responseData[0]['number']);
        self::assertSame('processing', $responseData[0]['currentStatus']);
    }

    /**
     * @throws Exception
     */
    private function createRecordWithStatus(string $number, string $status, string $createdAt): void
    {
        $record = new Record();
        $record->setNumber($number);
        $record->setCreatedAt(new DateTimeImmutable($createdAt));
        $record->changeStatus($status, new DateTimeImmutable($createdAt));

        $this->entityManager->persist($record);
    }
}
