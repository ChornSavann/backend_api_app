<?php
namespace App\RepositoryInterface;
interface UserInterface
{
    public function getAllUsers();
    public function getUserById($id);
    public function createUser(array $userDetails);
    public function updateUser($id, array $userDetails);
    public function deleteUser($id);
    public function loginUser(array $credentials);
}