<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExternalSpecRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return "ADMIN" === Auth::user()->user_type;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $id = Request::input("id");
        $rules_for_spec_revision_update = [
            'name'              => "required|unique:customer_specs,name,{$id}|max:200",
            'spec_no'           => "required|unique:customer_specs,spec_no,{$id}|max:100",
            'revision'          => "required|min:2|max:5",
            'document'          => 'required|mimes:pdf',
            'revision_date'     => "required|date",
            'reviewer'          => "required|max:100",
            'customer_name'     => 'required|max:100',
            'cc.*'              => 'email'
        ];

        $rules_for_spec_status_update = [
            "is_reviewed"   => "required|boolean",
            'revision'      => "required|min:2|max:5",
        ];

        return Request::input("is_reviewed") ? $rules_for_spec_status_update : $rules_for_spec_revision_update;
    }
}
