<?php

namespace App\Repositories;

use App\Models\HospitalSpecialist;

class HospitalSpecialistRepositories
{
    public function existsForHospitalandSpecialist(int $hospitalId, int $specialistId): bool
    {
        // Assuming you have a model that relates hospitals and specialists
        return HospitalSpecialist::where('hospital_id', $hospitalId)
            ->where('specialist_id', $specialistId)
            ->exists();
    }
}
