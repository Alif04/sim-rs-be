<?php

namespace App\Services;

use App\Repositories\BookingTransactionRepositories;
use App\Repositories\DoctorRepositories;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\ValidationException;

class BookingTransactionService
{
    private BookingTransactionRepositories $bookingTransaction;
    private DoctorRepositories $doctor;

    public function __construct(BookingTransactionRepositories $bookingTransaction, DoctorRepositories $doctor)
    {
        $this->bookingTransaction = $bookingTransaction;
        $this->doctor = $doctor;
    }

    // Manager services
    public function getAll()
    {
        return $this->bookingTransaction->getAll();
    }

    public function getById($id)
    {
        return $this->bookingTransaction->getByIdForManager($id);
    }

    public function updateStatus(int $id, string $status)
    {
        if (!in_array($status, [ 'Approved', 'Rejected'])) {
            throw ValidationException::withMessages([
                'status' => 'Invalid status provided.'
            ]);
        }
        return $this->bookingTransaction->updateStatus($id, $status);
    }

    // User services
    public function getAllForUser(int $userId)
    {
        return $this->bookingTransaction->getAllForUser($userId);
    }

    public function getByIdForUser(int $id, int $userId)
    {
        return $this->bookingTransaction->getById($id, $userId);
    }

    public function create(array $data)
    {
        $data['user_id'] = auth()->id();
        if ($this->bookingTransaction->isTimeSlotTakenForDoctor($data['doctor_id'], $data['started_at'], $data['time_at'])) {
           throw ValidationException::withMessages([
                'time_at' => ['The selected time slot is already taken by another booking.'],
                'started_at'=> ['The selected date is not available.'],
            ]);
        }

        $doctor = $this->doctor->getById($data['doctor_id'], ['*']);

        $price = $doctor->specialist->price;
        $tax = (int) round($price * 0.11); // Assuming a 10% tax rate
        $grand = $price + $tax;

        $data['sub_total'] = $price;
        $data['tax_total'] = $tax;
        $data['grand_total'] = $grand;
        $data['status'] = 'Waiting';

        if (isset($data['proof']) && $data['proof'] instanceof UploadedFile) {
            $data['proof'] = $this->uploadProof($data['proof']);
        }

        return $this->bookingTransaction->create($data);

    }


    private function uploadProof(UploadedFile $proof): string
    {
        if ($proof) {
            $path = $proof->store('proofs', 'public');
            return $path;
        }
        return null;
    }
}
