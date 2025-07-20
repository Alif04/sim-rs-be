<?php

namespace App\Repositories;

use App\Models\Hospital;

class HospitalRepositories
{
    public function getAll(array $fields)
    {
        return Hospital::select($fields)->latest()->with(['specialists', 'doctors'])->withCount(['doctors', 'specialists'])->paginate(10);
    }

    public function getById(int $id, array $fields)
    {
        return Hospital::select($fields)
            ->with(['doctors.specialist', 'specialists'])
            ->withCount(['doctors', 'specialists'])
            ->findOrFail($id);
    }


    public function create(array $data)
    {
        return Hospital::create($data);
    }

    public function update(int $id, array $data)
    {
        $hospital = Hospital::findOrFail($id);
        $hospital->update($data);
        return $hospital;
    }

    public function delete(int $id)
    {
        $hospital = Hospital::findOrFail($id);
        return $hospital->delete();
    }
}
