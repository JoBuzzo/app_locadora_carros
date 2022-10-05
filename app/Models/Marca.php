<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Marca extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome',
        'imagem'
    ];


    //validações
    public function rules()
    {
        return [
            'nome' => 'required|unique:marcas,nome,'.$this->id.'|min:3',
            'imagem' => 'required|file|mimes:png',
        ];
    }

    public function feedback()
    {
        return [
            'required' => 'O campo :attribute é orbigatório',
            'nome.unique' => 'O nome dessa marca já existe',
            'nome.min' => 'O nome deve conter no minimo 3 caracteres',
            'imagem.mimes' => 'O nome deve ser do tipo png'
        ];
    }


    
}
