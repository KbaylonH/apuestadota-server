<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class UpdateUser extends FormRequest
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
        return [
            'nombre' => 'required',
            'apellido' => 'required',
            'dni' => 'required|unique:usuario',
            'email' => 'required|unique:usuario',
            'dni_file' => 'required|image|max:2048'
        ];
    }

    public function messages(){
        return [
            'nombre.required' => 'El nombre es requerido',
            'apellido.required' => 'El apellido es requerido',
            'dni.required' => 'El DNI requerido',
            'email.required' => 'El email es requerido',
            'dni_file.required' => 'La foto del DNI es requerido',
            'dni_file.image' => 'La foto debe ser una imagen',
            'dni_file.max' => 'El tamaño de la foto no debe ser mayor a 2MB'
        ];
    }

    public function failedValidation(Validator $validator) { 
        $errors = [];
        foreach($validator->errors()->all() as $error){
            array_push($errors, $error);
        }
       throw new HttpResponseException(response()->json(['error'=>implode(',', $errors)], 422)); 
   }
}
