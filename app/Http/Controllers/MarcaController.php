<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use App\Models\Marca;
use App\Repositories\MarcaRepository;
use Illuminate\Http\Request;

class MarcaController extends Controller
{

    public function __construct(Marca $marca)
    {
        $this->marca = $marca;
    }

    public function index(Request $request)
    {

        $marcaRepository = new MarcaRepository($this->marca);


        if($request->has('atributos_modelos')){

            $atributos_modelos = 'modelos:id,'.$request->atributos_modelos;
            
            $marcaRepository->selectAtributosRegistrosRelacionados($atributos_modelos);

        }else{
            $marcaRepository->selectAtributosRegistrosRelacionados('modelos');
        }

        if($request->has('filtro')){
            $marcaRepository->filtro($request->filtro);
        }


        if($request->has('atributos')){
            $marcaRepository->selectAtributos($request->atributos);
        }
  
        return response()->json($marcaRepository->getResultado(), 200);
    }



    public function store(Request $request)
    {

        $request->validate($this->marca->rules(), $this->marca->feedback());

        $image = $request->file('imagem');
        $imagem_urn = $image->store('imagens', 'public');
        

        $marca = $this->marca->create([
            'nome' => $request->nome,
            'imagem' => $imagem_urn
        ]);

        return response()->json($marca, 201);
    }


    public function show($id)
    {
        if (!$marca = $this->marca->with('modelos')->find($id)) {
            return response()->json(['msg' => 'Marca não encontrada'], 404);
        }
        return response()->json($marca, 200);
    }



    public function update(Request $request, $id)
    {
        if (!$marca = $this->marca->find($id)) {
            return response()->json(['msg' => 'Marca não encontrada'], 404);
        }

        if ($request->method() === 'PATCH') {

            $regrasDinamicas = array();

            //percorrendo todas as regras deinidas no Model
            foreach($marca->rules() as $input => $regra){

                //Coletar apenas as regras aplícaveis aos parâmetros parciais da requisição
                if(array_key_exists($input, $request->all())){
                    $regrasDinamicas[$input] = $regra;
                }
            }
            $request->validate($regrasDinamicas, $marca->feedback());

        } else {
            $request->validate($marca->rules(), $marca->feedback());
        }

        //remove a imagem antiga caso a nova tenha sido enviada no request
        if($request->file('imagem')){
            Storage::disk('public')->delete($marca->imagem);
        }


        $image = $request->file('imagem');
        $imagem_urn = $image->store('imagens', 'public');
        
        $marca->fill($request->all());
        $marca->imagem = $imagem_urn;

        $marca->save();

        return response()->json($marca, 200);
    }


    public function destroy($id)
    {
        if (!$marca = $this->marca->find($id)) {
            return response()->json(['msg' => 'Marca não encontrada'], 404);
        }

        //remove a imagem antiga
        Storage::disk('public')->delete($marca->imagem);
        

        $marca->delete();
        return response()->json(['msg' => 'Marca excluída com sucesso'], 200);
    }
}
