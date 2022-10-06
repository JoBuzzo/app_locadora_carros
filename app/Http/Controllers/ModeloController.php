<?php

namespace App\Http\Controllers;

use App\Models\Modelo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ModeloController extends Controller
{
    public function __construct(Modelo $modelo)
    {
        $this->modelo = $modelo;
    }

    public function index(Request $request)
    {
        $modelos = array();

        if($request->has('atributos')){

            $atributos = $request->atributos;
            $modelos = $this->modelo->selectRaw($atributos)->with('marca')->get();

        }else{
            return response()->json($this->modelo->with('marca')->get(), 200);
        }

        return response()->json($modelos, 200);
    }

   
    public function store(Request $request)
    {

        $request->validate($this->modelo->rules());

        $image = $request->file('imagem');
        $imagem_urn = $image->store('imagens/modelos', 'public');
        

        $modelo = $this->modelo->create([
            'marca_id' => $request->marca_id,
            'nome' => $request->nome,
            'imagem' => $imagem_urn,
            'numero_portas' => $request->numero_portas,
            'lugares' => $request->lugares,
            'air_bag' => $request->air_bag,
            'abs' => $request->abs
        ]);

        return response()->json($modelo, 201);
    }

    public function show($id)
    {
        if (!$modelo = $this->modelo->with('marca')->find($id)) {
            return response()->json(['msg' => 'Modelo não encontrada'], 404);
        }
        return response()->json($modelo, 200);
    }

    public function update(Request $request, $id)
    {
        if (!$modelo = $this->modelo->find($id)) {
            return response()->json(['msg' => 'Modelo não encontrada'], 404);
        }

        if ($request->method() === 'PATCH') {

            $regrasDinamicas = array();

            //percorrendo todas as regras deinidas no Model
            foreach($modelo->rules() as $input => $regra){

                //Coletar apenas as regras aplícaveis aos parâmetros parciais da requisição
                if(array_key_exists($input, $request->all())){
                    $regrasDinamicas[$input] = $regra;
                }
            }
            $request->validate($regrasDinamicas);

        } else {
            $request->validate($modelo->rules());
        }

        //remove a imagem antiga caso a nova tenha sido enviada no request
        if($request->file('imagem')){
            Storage::disk('public')->delete($modelo->imagem);
        }


        $image = $request->file('imagem');
        $imagem_urn = $image->store('imagens/modelos', 'public');
        
        $modelo->fill($request->all());
        $modelo->imagem = $imagem_urn;

        $modelo->save();

        return response()->json($modelo, 200);
    }

    public function destroy($id)
    {
        if (!$modelo = $this->modelo->find($id)) {
            return response()->json(['msg' => 'Modelo não encontrada'], 404);
        }

        //remove a imagem antiga
        Storage::disk('public')->delete($modelo->imagem);
        

        $modelo->delete();
        return response()->json(['msg' => 'Modelo excluída com sucesso'], 200);
    }
}
