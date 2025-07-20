<?php

namespace App\Repositories;

use App\Models\Doctor;

class DoctorRepositories
{
    public function getAll(array $fields)
    {
        return Doctor::select($fields)->latest()->with(['hospital', 'specialist'])->paginate(10);
    }

    public function getById(int $id, array $fields)
    {
        return Doctor::select($fields)
           ->with(['bookingTransactions.user','specialist', 'hospital'])
            ->findOrFail($id);
    }


    public function create(array $data)
    {
        return Doctor::create($data);
    }

    public function update(int $id, array $data)
    {
        $doctor = Doctor::findOrFail($id);
        $doctor->update($data);
        return $doctor;
    }

    public function delete(int $id)
    {
        $doctor = Doctor::findOrFail($id);
        return $doctor->delete();
    }

    public function filterBySpecialistAndHospital(int $specialistId, int $hospitalId)
    {
        return Doctor::with(['specialist', 'hospital'])
            ->where('specialist_id', $specialistId)
            ->where('hospital_id', $hospitalId)
            // ->paginate(10)
            ->get();
    }
}
