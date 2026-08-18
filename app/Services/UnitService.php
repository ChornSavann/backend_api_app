<?php

namespace App\Services;

use App\RepositoryInterface\UnitInterface;

class UnitService implements \App\Services\Interface\UnitServiceInterface
{
    protected $unitRepository;

    // ណែនាំឱ្យចាក់បញ្ចូលតាម Interface វិញ ដើម្បីរក្សា Dependency Inversion Principle
    public function __construct(UnitInterface $unitRepository)
    {
        $this->unitRepository = $unitRepository;
    }

    public function getAllUnits()
    {
        return $this->unitRepository->getAllUnits();
    }

    public function getUnitById($id)
    {
        return $this->unitRepository->getUnitById($id);
    }

    public function createUnit(array $data)
    {
        return $this->unitRepository->createUnit($data);
    }

    public function updateUnit($id, array $data)
    {
        return $this->unitRepository->updateUnit($id, $data);
    }

    public function deleteUnit($id)
    {
        return $this->unitRepository->deleteUnit($id);
    }
}