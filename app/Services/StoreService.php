<?php

namespace App\Services;

use App\RepositoryInterface\StoreInterface;
use App\Services\Interface\StoreServiceInterface;

class StoreService implements StoreServiceInterface
{
    protected $storeRepository;

    public function __construct(StoreInterface $storeRepository)
    {
        $this->storeRepository = $storeRepository;
    }


    public function createStore(array $data)
    {
        return $this->storeRepository->createStore($data);
    }

    public function getAllStores()
    {
        return $this->storeRepository->getAllStores();
    }

    public function getStoreById($id)
    {
        // 🟢 ត្រូវប្តូរពី get($id) មកជា getStoreById($id) វិញ
        return $this->storeRepository->getStoreById($id);
    }
   
    public function updateStore($id, array $data)
    {
        return $this->storeRepository->updateStore($id, $data);
    }

    public function deleteStore($id)
    {
        return $this->storeRepository->deleteStore($id);
    }
}