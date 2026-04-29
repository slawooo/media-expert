<?php

namespace App\Mapper;

use App\Entity\Record;
use App\Entity\StatusLog;

class RecordResponseMapper
{
    public function map(Record $record, bool $withHistory = false): array
    {
        $data = [
            'id' => $record->getId(),
            'number' => $record->getNumber(),
            'createdAt' => $record->getCreatedAt()?->format(DATE_ATOM),
            'currentStatus' => $record->getCurrentStatus(),
        ];

        if (!$withHistory) {
            return $data;
        }

        $statusHistory = [];

        foreach ($record->getStatusHistory() as $statusLog) {
            $statusHistory[] = $this->mapStatusLog($statusLog);
        }

        $data['statusHistory'] = $statusHistory;

        return $data;
    }

    private function mapStatusLog(StatusLog $statusLog): array
    {
        return [
            'id' => $statusLog->getId(),
            'status' => $statusLog->getStatus(),
            'createdAt' => $statusLog->getCreatedAt()?->format(DATE_ATOM),
        ];
    }
}
