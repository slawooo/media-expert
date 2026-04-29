<?php

namespace App\Factory;

use App\Dto\RecordSearchCriteriaDto;
use DateTimeImmutable;
use Exception;
use Symfony\Component\HttpFoundation\Request;

class RecordSearchCriteriaFactory
{
    /**
     * @throws Exception
     */
    public function createFromRequest(Request $request): RecordSearchCriteriaDto
    {
        $criteria = new RecordSearchCriteriaDto();
        $criteria->number = $request->query->get('number');
        $criteria->currentStatus = $request->query->get('currentStatus');
        $criteria->historicalStatus = $request->query->get('historicalStatus');

        $createdFrom = $request->query->get('createdFrom');
        if (is_string($createdFrom) && $createdFrom !== '') {
            $criteria->createdFrom = new DateTimeImmutable($createdFrom);
        }

        $createdTo = $request->query->get('createdTo');
        if (is_string($createdTo) && $createdTo !== '') {
            $criteria->createdTo = new DateTimeImmutable($createdTo);
        }

        return $criteria;
    }
}
