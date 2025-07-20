<?php

namespace App\Http\Controllers;

use App\Services\HospitalService;
use Illuminate\Http\Request;

class HospitalSpecialistController extends Controller
{
    //

    private HospitalService $hospitalService;

    public function __construct(HospitalService $hospitalService)
    {
        $this->hospitalService = $hospitalService;
    }

    public function attach(Request $request, int $hospitalId)
    {
        $request->validate([
            'specialist_id' => 'required|exists:specialists,id',
        ]);
        $this->hospitalService->attachSpecialist($hospitalId, $request->input('specialist_id'));
        return response()->json(['message' => 'Specialist attached to hospital successfully'], 200);
    }

    public function detach(int $hospitalId, int $specialistId)
    {
        $this->hospitalService->detachSpecialist($hospitalId, $specialistId);
        return response()->json(['message' => 'Specialist detached from hospital successfully'], 200);
    }
}
