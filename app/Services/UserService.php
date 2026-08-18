<?php

namespace App\Services;

use App\RepositoryInterface\UserInterface;
use App\Services\Interface\UserServiceInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserService implements UserServiceInterface
{
    protected $userRepository;

    public function __construct(UserInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function getAllUsers()
    {
        return $this->userRepository->getAllUsers();
    }

    public function getUserById($id)
    {
        return $this->userRepository->getUserById($id);
    }

    public function createUser(array $userDetails, ?UploadedFile $image = null)
    {
        if ($image) {
            $imageName = time() . '_' . $image->getClientOriginalName();
            $image->move(public_path('users'), $imageName);
            
            $userDetails['image'] = 'users/' . $imageName;
        }

        return $this->userRepository->createUser($userDetails);
    }



    public function updateUser($id, array $userDetails, ?UploadedFile $image = null)
    {
        // if (!empty($userDetails['password'])) {
        //     $userDetails['password'] = Hash::make($userDetails['password']);
        // } else {
        //     unset($userDetails['password']);
        // }
        if ($image) {
        
            $existingUser = User::find($id);
            if ($existingUser && $existingUser->image && file_exists(public_path($existingUser->image))) {
                @unlink(public_path($existingUser->image));
            }

            $imageName = time() . '_' . $image->getClientOriginalName();
            $image->move(public_path('users'), $imageName);
            
            $userDetails['image'] = 'users/' . $imageName;
        }

        return $this->userRepository->updateUser($id, $userDetails);
    }

    public function deleteUser($id)
    {
        $user = User::find($id);
        
        if (!$user) {
            return false;
        }

        if ($user->image && file_exists(public_path($user->image))) {
            @unlink(public_path($user->image));
        }
        return $this->userRepository->deleteUser($id);
    }

    public function loginUser(array $credentials)
    {
        return $this->userRepository->loginUser($credentials);
    }
}