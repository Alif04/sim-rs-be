<?php

namespace App\Services;

use App\Repositories\HospitalRepositories;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class HospitalService
{
    private HospitalRepositories $hospital;

    public function __construct(HospitalRepositories $hospital)
    {
        $this->hospital = $hospital;
    }

    public function getAll(array $fields)
    {
        $hospital = $this->hospital->getAll($fields);
        return $hospital;
    }

    public function getById(int $id, array $fields)
    {
        return $this->hospital->getById($id, $fields);
    }


    public function create(array $data)
    {
        if (isset($data['photo']) && $data['photo'] instanceof UploadedFile) {
            $data['photo'] = $this->uploadPhoto($data['photo']);
        }
        return $this->hospital->create($data);
    }

    public function update(int $id, array $data)
    {
        $fields = ['*'];
        $hospital = $this->hospital->getById($id, $fields);
        if (isset($data['photo']) && $data['photo'] instanceof UploadedFile) {
            if (!empty($hospital->photo)) {
                $this->deletePhoto($hospital->photo);
            }
            $data['photo'] = $this->uploadPhoto($data['photo']);
        }
        return $this->hospital->update($id, $data);
    }

    public function delete(int $id)
    {
        $fields = ['*'];
        $hospital = $this->hospital->getById($id, $fields);
        if ($hospital->photo) {
            $this->deletePhoto($hospital->photo);
        }
        return $this->hospital->delete($id);
    }

    private function uploadPhoto(UploadedFile $photo) : string
    {
        if ($photo) {
            $path = $photo->store('hospitals', 'public');
            return $path;
        }
        return null;
    }

    private function deletePhoto(string $photoPath)
    {
        $relativePath = 'hospitals/' . basename($photoPath);
        if (Storage::disk('public')->exists($relativePath)) {
            Storage::disk('public')->delete($relativePath);
        }
    }

    public function attachSpecialist(int $id, int $specialistId)
    {
        $hospital = $this->hospital->getById($id, ['id']);
        $hospital->specialists()->syncWithoutDetaching($specialistId);
        return $hospital;
    }

    public function detachSpecialist(int $id, int $specialistId)
    {
        $hospital = $this->hospital->getById($id, ['id']);
        $hospital->specialists()->detach($specialistId);
        return $hospital;
    }
}
