<?php

namespace App\Dto;

class RecordSearchCriteriaDto
{
    public ?string $number = null;
    public ?\DateTimeImmutable $createdFrom = null;
    public ?\DateTimeImmutable $createdTo = null;
    public ?string $currentStatus = null;
    public ?string $historicalStatus = null;
}
