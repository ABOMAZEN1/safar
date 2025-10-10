<?php

declare(strict_types=1);

namespace App\Services\BusType;


use Illuminate\Support\Collection;
use App\Repositories\Eloquent\BusTypeRepository;

final readonly class BusTypeService
{
    public function __construct(
        private BusTypeRepository $busTypeRepository,
    ) {}

    public function getAllBusTypes(): Collection
    {
        return $this->busTypeRepository->getAllBusTypes();
    }
}
