<?php

namespace App\Http\Controllers;

use App\Http\Requests\SpecialistRequest;
use App\Http\Resources\SpecialistResource;
use App\Services\SpecialistService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class SpecialistController extends Controller
{
    //
    private SpecialistService $specialist;

    public function __construct(SpecialistService $specialist)
    {
        $this->specialist = $specialist;
    }

    public function index()
    {
        $fields = ['id', 'name', 'photo', 'price'];
        $specialists = $this->specialist->getAll($fields);
        return response()->json(SpecialistResource::collection($specialists));
    }

    public function show(int $id)
    {
        try {
            $fields = ['*'];
            $specialist = $this->specialist->getById($id, $fields);
            return response()->json(new SpecialistResource($specialist));
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Specialist not found'], 404);
        }
    }

    public function store(SpecialistRequest $request)
    {
        $data = $this->specialist->create($request->validated());

        return response()->json(new SpecialistResource($data), 201);
    }

    public function update(SpecialistRequest $request, int $id)
    {
        try {
            $data = $this->specialist->update($id, $request->validated());
            return response()->json(new SpecialistResource($data));
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Specialist not found'], 404);
        }
    }

    public function destroy(int $id)
    {
        try {
            $this->specialist->delete($id);
            return response()->json(['message' => 'Specialist deleted successfully']);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Specialist not found'], 404);
        }
    }
}
