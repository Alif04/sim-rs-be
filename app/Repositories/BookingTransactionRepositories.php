<?php

namespace App\Repositories;

use App\Models\BookingTransaction;

class BookingTransactionRepositories
{
    public function getAll()
    {
        return BookingTransaction::with(['doctor', 'doctor.hospital', 'doctor.specialist', 'user'])
            ->latest()
            ->paginate(10);
    }

    public function getAllForUser(int $userId)
    {
        return BookingTransaction::with(['doctor', 'doctor.hospital', 'doctor.specialist'])
            ->where('user_id', $userId)
            ->latest()
            ->paginate(10);
    }

    public function getByIdForManager(int $id)
    {
        return BookingTransaction::with(['doctor', 'doctor.hospital', 'doctor.specialist', 'user'])
            ->findOrFail($id);
    }

    public function getById(int $id, int $userId)
    {
        return BookingTransaction::with(['doctor', 'doctor.hospital', 'doctor.specialist'])
            ->where('id', $id)
            ->where('user_id', $userId)
            ->firstOrFail();
    }

    public function create(array $data)
    {
        return BookingTransaction::create($data);
    }

    public function updateStatus(int $id, string $status)
    {
        $bookingTransaction = $this->getByIdForManager($id);
        $bookingTransaction->update(['status'=> $status]);
        return $bookingTransaction;
    }

    public function isTimeSlotTakenForDoctor(int $doctorId, string $date, string $time): bool
    {
        return BookingTransaction::where('doctor_id', $doctorId)
            ->where('started_at', $date)
            ->where('time_at', $time)
            ->exists();
    }
}
