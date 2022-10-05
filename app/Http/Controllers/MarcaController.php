<?php

namespace App\Http\Controllers;

use App\Models\Marca;
use Illuminate\Http\Request;

class MarcaController extends Controller
{

    public function __construct(Marca $marca)
    {
        $this->marca = $marca;
    }

    public function index()
    {
        $marcas = $this->marca->all();

        return response()->json($marcas, 200);
    }



    public function store(Request $request)
    {

        

        

        $request->validate($this->marca->rules(), $this->marca->feedback());

        $marca = $this->marca->create($request->all());
        
        return response()->json($marca, 201);
    }


    public function show($id)
    {
        if(!$marca = $this->marca->find($id)){
            return response()->json(['msg' => 'Marca não encontrada'], 404);
        }
        return response()->json($marca, 200);
    }



    public function update(Request $request, $id)
    {
        if(!$marca = $this->marca->find($id)){
            return response()->json(['msg' => 'Marca não encontrada'], 404);
        }

        $marca->update($request->all());
        return response()->json($marca, 200);
    }


    public function destroy($id)
    {
        if(!$marca = $this->marca->find($id)){
            return response()->json(['msg' => 'Marca não encontrada'], 404);
        }

        $marca->delete();
        return response()->json(['msg' => 'Marca excluída com sucesso'], 200);
        
    }
}
