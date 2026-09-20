<?php
namespace App\RepositoryInterface;
interface StoreInterface
{
    public function getAllStores();
    public function getStoreById($id);
    public function createStore(array $data);
    public function updateStore($id, array $data);
    public function deleteStore($id);
}