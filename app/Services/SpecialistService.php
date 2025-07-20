<?php

namespace App\Services;

use App\Repositories\SpecialistRepositories;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class SpecialistService
{
    private SpecialistRepositories $specialist;

    public function __construct(SpecialistRepositories $specialist)
    {
        $this->specialist = $specialist;
    }

    public function getAll(array $fields)
    {
        $specialist = $this->specialist->getAll($fields);
        return $specialist;
    }

    public function getById(int $id, array $fields)
    {
        return $this->specialist->getById($id, $fields);
    }


    public function create(array $data)
    {
        if (isset($data['photo']) && $data['photo'] instanceof UploadedFile) {
            $data['photo'] = $this->uploadPhoto($data['photo']);
        }
        return $this->specialist->create($data);
    }

    public function update(int $id, array $data)
    {
        $fields = ['*'];
        $specialist = $this->specialist->getById($id, $fields);
        if (isset($data['photo']) && $data['photo'] instanceof UploadedFile) {
            if (!empty($specialist->photo)) {
                $this->deletePhoto($specialist->photo);
            }
            $data['photo'] = $this->uploadPhoto($data['photo']);
        }
        return $this->specialist->update($id, $data);
    }

    public function delete(int $id)
    {
        $fields = ['*'];
        $specialist = $this->specialist->getById($id, $fields);
        if ($specialist->photo) {
            $this->deletePhoto($specialist->photo);
        }
        return $this->specialist->delete($id);
    }

    private function uploadPhoto(UploadedFile $photo) : string
    {
        if ($photo) {
            $path = $photo->store('specialists', 'public');
            return $path;
        }
        return null;
    }

    private function deletePhoto(string $photoPath)
    {
        $relativePath = 'specialists/' . basename($photoPath);
        if (Storage::disk('public')->exists($relativePath)) {
            Storage::disk('public')->delete($relativePath);
        }
    }
}
