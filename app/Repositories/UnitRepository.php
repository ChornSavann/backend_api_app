<?php

namespace App\Repositories;

use App\Models\Units;
use App\RepositoryInterface\UnitInterface;

class UnitRepository implements UnitInterface
{
    public function getAllUnits()
    {
        return Units::all();
    }

    public function getUnitById($id)
    {
        return Units::findOrFail($id);
    }

    public function createUnit(array $data)
    {
        return Units::create($data);
    }

    public function updateUnit($id, array $data)
    {
        $unit = $this->getUnitById($id);
        $unit->update($data);
        return $unit;
    }

    public function deleteUnit($id)
    {
        $unit = Units::findOrFail($id);
        return $unit->delete();
    }
}