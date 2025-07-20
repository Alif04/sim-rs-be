<?php

namespace App\Http\Controllers;

use App\Http\Requests\HospitalRequest;
use App\Http\Resources\HospitalResource;
use App\Services\HospitalService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class HospitalController extends Controller
{
    //
    private HospitalService $hospital;

    public function __construct(HospitalService $hospital)
    {
        $this->hospital = $hospital;
    }

    public function index()
    {
        $fields = ['id', 'name', 'photo', 'city', 'phone'];
        $hospitals = $this->hospital->getAll($fields);
        return response()->json(HospitalResource::collection($hospitals));
    }

    public function show(int $id)
    {
        try {
            $fields = ['*'];
            $hospital = $this->hospital->getById($id, $fields);
            return response()->json(new HospitalResource($hospital));
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Hospital not found'], 404);
        }
    }

    public function store(HospitalRequest $request)
    {
        $data = $this->hospital->create($request->validated());

        return response()->json(new HospitalResource($data), 201);
    }

    public function update(int $id,HospitalRequest $request)
    {
        try {
            $data = $this->hospital->update($id, $request->validated());
            return response()->json(new HospitalResource($data));
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Hospital not found'], 404);
        }
    }

    public function destroy(int $id)
    {
        try {
            $this->hospital->delete($id);
            return response()->json(['message' => 'Hospital deleted successfully']);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Hospital not found'], 404);
        }
    }
}
