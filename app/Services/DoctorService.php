<?php

namespace App\Services;

use App\Repositories\DoctorRepositories;
use App\Repositories\HospitalSpecialistRepositories;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\ValidationException;

class DoctorService
{
    private DoctorRepositories $doctor;
    private HospitalSpecialistRepositories $hospitalSpecialist;

    public function __construct(DoctorRepositories $doctor, HospitalSpecialistRepositories $hospitalSpecialist)
    {
        $this->doctor = $doctor;
        $this->hospitalSpecialist = $hospitalSpecialist;
    }

    public function getAll(array $fields)
    {
        $specialist = $this->doctor->getAll($fields);
        return $specialist;
    }

    public function getById(int $id, array $fields)
    {
        return $this->doctor->getById($id, $fields);
    }


    public function create(array $data)
    {
        if(!$this->hospitalSpecialist->existsForHospitalandSpecialist($data['hospital_id'], $data['specialist_id'])) {
            throw ValidationException::withMessages([
                'hospital_id' => 'The selected hospital does not have the specified specialist.',
                'specialist_id' => 'The selected specialist is not available in the specified hospital.'
            ]);
        }
        if (isset($data['photo']) && $data['photo'] instanceof UploadedFile) {
            $data['photo'] = $this->uploadPhoto($data['photo']);
        }
        return $this->doctor->create($data);
    }

    public function update(int $id, array $data)
    {
         if(!$this->hospitalSpecialist->existsForHospitalandSpecialist($data['hospital_id'], $data['specialist_id'])) {
            throw ValidationException::withMessages([
                'hospital_id' => 'The selected hospital does not have the specified specialist.',
                'specialist_id' => 'The selected specialist is not available in the specified hospital.'
            ]);
        }

        $fields = ['*'];
        $doctor = $this->doctor->getById($id, $fields);
        if (isset($data['photo']) && $data['photo'] instanceof UploadedFile) {
            if (!empty($doctor->photo)) {
                $this->deletePhoto($doctor->photo);
            }
            $data['photo'] = $this->uploadPhoto($data['photo']);
        }
        return $this->doctor->update($id, $data);
    }

    public function delete(int $id)
    {
        $fields = ['*'];
        $doctor = $this->doctor->getById($id, $fields);
        if ($doctor->photo) {
            $this->deletePhoto($doctor->photo);
        }
        return $this->doctor->delete($id);
    }

    private function uploadPhoto(UploadedFile $photo) : string
    {
        if ($photo) {
            $path = $photo->store('doctors', 'public');
            return $path;
        }
        return null;
    }

    private function deletePhoto(string $photoPath)
    {
        $relativePath = 'doctors/' . basename($photoPath);
        if (Storage::disk('public')->exists($relativePath)) {
            Storage::disk('public')->delete($relativePath);
        }
    }

    public function filterBySpecialistAndHospital(int $specialistId, int $hospitalId)
    {
        return $this->doctor->filterBySpecialistAndHospital($specialistId, $hospitalId);
    }

    public function getAvailableSlots(int $doctorId)
    {
        $doctor = $this->doctor->getById($doctorId, ['id']);
        $dates = collect([
            now()->addDay()->startOfDay(),
            now()->addDays(2)->startOfDay(),
            now()->addDays(3)->startOfDay(),
        ]);

        $timeSlots = ['10:30', '11:00', '11:30', '12:00', '12:30', '13:00', '13:30', '14:00', '14:30', '15:00', '15:30', '16:00'];

        $availability = [];

        foreach ($dates as $date) {
            $dateStr = $date->toDateString();
            $availability[$dateStr] = [];
            foreach ($timeSlots as $time) {
                $isTaken = $doctor->bookingTransactions()
                    ->where('started_at', $dateStr)
                    ->where('time_at', $time)
                    ->exists();
                if (!$isTaken) {
                    $availability[$dateStr][] = $time;
                }
            }
        }
        // Assuming you have a method to get available slots for a doctor on a specific date
        return $availability;
    }
}
