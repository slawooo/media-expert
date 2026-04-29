<?php

namespace App\Tests\Unit\Factory;

use App\Factory\RecordSearchCriteriaFactory;
use Exception;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

class RecordSearchCriteriaFactoryTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testCreateFromRequestMapsAllSupportedFilters(): void
    {
        // Given:
        $request = new Request([
            'number' => 'REC',
            'currentStatus' => 'processing',
            'historicalStatus' => 'new',
            'createdFrom' => '2025-01-01 10:00:00',
            'createdTo' => '2025-01-31 18:00:00',
        ]);

        $factory = new RecordSearchCriteriaFactory();

        // When:
        $criteria = $factory->createFromRequest($request);

        // Then:
        self::assertSame('REC', $criteria->number);
        self::assertSame('processing', $criteria->currentStatus);
        self::assertSame('new', $criteria->historicalStatus);
        self::assertSame('2025-01-01 10:00:00', $criteria->createdFrom?->format('Y-m-d H:i:s'));
        self::assertSame('2025-01-31 18:00:00', $criteria->createdTo?->format('Y-m-d H:i:s'));
    }

    /**
     * @throws Exception
     */
    public function testCreateFromRequestLeavesOptionalFiltersAsNullWhenNotProvided(): void
    {
        // Given:
        $request = new Request();
        $factory = new RecordSearchCriteriaFactory();

        // When:
        $criteria = $factory->createFromRequest($request);

        // Then:
        self::assertNull($criteria->number);
        self::assertNull($criteria->currentStatus);
        self::assertNull($criteria->historicalStatus);
        self::assertNull($criteria->createdFrom);
        self::assertNull($criteria->createdTo);
    }
}
