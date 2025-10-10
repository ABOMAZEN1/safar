<?php

declare(strict_types=1);

namespace App\Services\Bus;

use Exception;
use App\Models\Bus;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use App\Repositories\Eloquent\BusRepository;

final readonly class BusService
{
    public function __construct(
        private BusRepository $busRepository,
    ) {}

    /**
     * Create a new bus.
     */
    public function createBus(array $data): Bus
    {
        /** @var User $user */
        $user = Auth::user();

        $data['travel_company_id'] = $user->company->id;

        return $this->busRepository->createBus($data);
    }

    /**
     * Update the specified bus.
     */
    public function update(int $id, array $data): ?Bus
    {
        $bus = $this->busRepository->findBusById($id);

        /** @var User $user */
        $user = Auth::user();

        if ($user->company->id !== $bus->travel_company_id) {
            throw new Exception(
                message: 'You are not authorized to update this bus',
                code: Response::HTTP_FORBIDDEN,
            );
        }

        $this->busRepository->updateBus($bus->id, $data);

        return $this->busRepository->findBusById($id);
    }

    /**
     * Get all buses for authenticated company.
     */
    public function getCompanyBuses(): Collection
    {
        /** @var User $user */
        $user = Auth::user();

        return $this->busRepository->getBusesByCompanyId($user->company->id);
    }

    /**
     * Delete the specified bus.
     * 
     * @param int $id The ID of the bus to delete
     * @throws Exception If the user is not authorized to delete the bus
     * @return bool Whether the deletion was successful
     */
    public function deleteBus(int $id): bool
    {
        $bus = $this->busRepository->findBusById($id);

        /** @var User $user */
        $user = Auth::user();

        if ($user->company->id !== $bus->travel_company_id) {
            throw new Exception(
                message: 'You are not authorized to delete this bus',
                code: Response::HTTP_FORBIDDEN,
            );
        }

        return $this->busRepository->deleteBus($bus->id);
    }
}
