<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class NewEventRequest extends Request
{
    /**
     * Authorization logic is taken care of by jwt.auth in routes.php
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
        return [
            'event.title' => 'required|max:255',
            'event.start_time' => 'required|date|after:now',
            'event.end_time' => 'date|after:now',
            'event.location' => 'max:255',
            'event.link' => 'url|max:255',
            'event.body' => 'required|max:5000',
        ];
    }
}
