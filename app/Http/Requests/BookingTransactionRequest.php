<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;

class BookingTransactionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            //
            "doctor_id"=> "required|exists:doctors,id",
            "started_at" => [
                "required",
                "date",
                function ($attribute, $value, $fail) {
                    $date = Carbon::parse($value);
                    $min = now()->addDay()->startOfDay();
                    $max = now()->addDays(3)->endOfDay();

                    if($date->lt($min) && $date->gt($max)){
                        $fail("The $attribute must be between $min and $max.");
                    }
                }
            ],
            "time_at"=> [
                "required",
                "date_format:H:i",
                Rule::in(['10:30', '11:00', '11:30', '12:00', '12:30', '13:00', '13:30', '14:00', '14:30', '15:00', '15:30', '16:00'])
            ],
            'proof'=> 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ];
    }
}
