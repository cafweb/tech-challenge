<?php

namespace App\Http\Requests;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MovieRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $movie = $this->route('movie');

        return [
            'name' => [
                'required',
                'string',
                'min:3',
                'max:255',
                Rule::unique('movies','name')->ignore($movie)
            ],
            'year' => 'required|date_format:Y|before_or_equal:' . Carbon::now(),
            'synopsis' => 'required|string|max:255',
            'runtime' => 'required|integer',
            'released_at' => 'required|date|before_or_equal:' . Carbon::now(),
            'cost' => 'required|integer',
            'genre_id' => 'required|exists:genres,id'
        ];
    }
}
