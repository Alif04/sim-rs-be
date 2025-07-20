<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\AuthRepositories;
use Illuminate\Http\UploadedFile;

class AuthService
{
    private AuthRepositories $authRepository;

    public function __construct(AuthRepositories $authRepository)
    {
        $this->authRepository = $authRepository;
    }

    public function register(array $data)
    {
        if (isset($data['photo']) && $data['photo'] instanceof UploadedFile) {
            $data['photo'] = $this->uploadPhoto($data['photo']);
        }
        return $this->authRepository->register($data);
    }

    public function login(array $credentials)
    {
        return $this->authRepository->login($credentials);
    }
    public function loginToken(array $credentials)
    {
        return $this->authRepository->tokenLogin($credentials);
    }

    private function uploadPhoto(UploadedFile $file)
    {
        return $file->store('photos', 'public');
    }
}
