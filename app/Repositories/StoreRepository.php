<?php

namespace App\Repositories;

use App\RepositoryInterface\StoreInterface;
use Illuminate\Support\Facades\File;
use App\Models\Store;

class StoreRepository implements StoreInterface
{
    protected $store;

    public function __construct(Store $store)
    {
        $this->store = $store;
    }

    public function getAllStores()
    {
        return $this->store->all();
    }

    public function getStoreById($id)
    {
        return $this->store->findOrFail($id);
    }

    public function createStore(array $data)
    {
        return $this->store->create($data);
    }

    public function updateStore($id, array $data)
    {
        $store = $this->store->findOrFail($id);
        $store->update($data);
        return $store;
    }

    public function deleteStore($id)
    {
        $store = $this->getStoreById($id); 
        if ($store->logo && File::exists(public_path($store->logo))) {
            File::delete(public_path($store->logo));
        }
        return $store->delete();
    }
}
