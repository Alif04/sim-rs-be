<?php

namespace App\Http\Controllers;

use App\Http\Requests\DoctorRequest;
use App\Http\Resources\DoctorResource;
use App\Services\DoctorService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class DoctorController extends Controller
{
    //
    private DoctorService $doctor;

    public function __construct(DoctorService $doctor)
    {
        $this->doctor = $doctor;
    }

    public function index()
    {
        $fields = ['id', 'name', 'photo', 'gender', 'about', 'yoe', 'hospital_id', 'specialist_id'];
        $doctors = $this->doctor->getAll($fields);
        return response()->json(DoctorResource::collection($doctors));
    }

    public function show(int $id)
    {
        try {
            $fields = ['*'];
            $doctor = $this->doctor->getById($id, $fields);
            return response()->json(new DoctorResource($doctor));
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Doctor not found'], 404);
        }
    }

    public function store(DoctorRequest $request)
    {
        $data = $this->doctor->create($request->validated());

        return response()->json(new DoctorResource($data), 201);
    }

    public function update(int $id, DoctorRequest $request)
    {
        try {
            $data = $this->doctor->update($id, $request->validated());
            return response()->json(new DoctorResource($data));
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Doctor not found'], 404);
        }
    }

    public function destroy(int $id)
    {
        try {
            $this->doctor->delete($id);
            return response()->json(['message' => 'Doctor deleted successfully']);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Doctor not found'], 404);
        }
    }

    public function filterByHospitalAndSpecialist(Request $request)
    {
       $validated = $request->validate([
            'hospital_id' => 'required|exists:hospitals,id',
            'specialist_id' => 'required|exists:specialists,id',
        ]);

        $hospitalId = $validated['hospital_id'];
        $specialistId = $validated['specialist_id'];

        if (!$hospitalId || !$specialistId) {
            return response()->json(['error' => 'Hospital ID and Specialist ID are required'], 400);
        }
        $doctors = $this->doctor->filterBySpecialistAndHospital($specialistId, $hospitalId);
        return DoctorResource::collection($doctors);
    }

    public function availableSlots(int $doctorId)
    {
        try {
            $availableSlots = $this->doctor->getAvailableSlots($doctorId);
            return response()->json($availableSlots);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Doctor not found'], 404);
        }
    }

}
