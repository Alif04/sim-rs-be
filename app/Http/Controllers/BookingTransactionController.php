<?php

namespace App\Http\Controllers;

use App\Http\Resources\TransactionResource;
use App\Services\BookingTransactionService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class BookingTransactionController extends Controller
{
    //
    private BookingTransactionService $bookingTransactionService;

    public function __construct(BookingTransactionService $bookingTransactionService)
    {
        $this->bookingTransactionService = $bookingTransactionService;
    }

    public function index()
    {
        $transactions = $this->bookingTransactionService->getAll();
        return response()->json(TransactionResource::collection($transactions), 200);
    }

    public function show(int $id)
    {
        try {
            $transaction = $this->bookingTransactionService->getById($id);

            return response()->json(new TransactionResource($transaction), 200);

        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Transaction not found'], 404);
        }

    }

    public function updaeStatus(Request $request, int $id)
    {
        $validated = $request->validate([
            'status' => 'required|string|in:Approved,Rejected'
        ]);
        try {
            $transaction = $this->bookingTransactionService->updateStatus($id, $validated['status']);

            return response()->json(new TransactionResource($transaction), 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Transaction not found'], 404);
        }
    }
}
