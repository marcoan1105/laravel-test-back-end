<?php

namespace App\Http\Controllers;

use App\Color;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ColorController extends Controller
{
    /**
     * Get all colors.
     *
     * @return array
     */
    protected $validation = [
        "description" => "required",
    ];

    /**
     * Get all colors
     *
     * @return array
     */

    public function all(){
        if(!ResponseController::validationUser()){
            return ResponseController::returnApi(false, null, "Autenticação Invállida");
        }

        return ResponseController::returnApi(true, Color::all());
    }

    /**
     * Save the color
     *
     * @param $data
     * @return Color|null
     */

    public function save($request){

        if(!ResponseController::validationUser()){
            return ResponseController::returnApi(false, null, "Autenticação Invállida");
        }

        $validation = Validator::make($request->all(), $this->validation);

        if($validation->fails()){
            return ResponseController::returnApi(false, null, null, $validation->errors());
        }
        $where = [["description", "=", $request->description]];

        if ($request->id != null && $request->id != ""){
            $where[] = ["id", "<>", $request->id];
        }

        $colorValidation = Color::where($where)->get();

        if(count($colorValidation)){
            return ResponseController::returnApi(false, null, "Cor já cadastrada no sistema.");
        }

        $color = null;

        if ($request->id == null || $request->id == ""){
            $color = new Color();
        }else{
            $color = Color::find($request->id);

            if($color == null){
                return ResponseController::returnApi(false, null, "Cor não encontrada.");
            }
        }

        $color->description = $request->description;
        $color->save();

        return ResponseController::returnApi(true, $color);
    }

    /**
     * Save color
     *
     * @param Request $request
     * @return array
     *
     */
    public function store(Request $request){
        return $this->save($request);
    }

    /**
     * Delete color
     *
     * @param Request $request
     * @param $id
     * @return array
     */
    public function delete(Request $request, $id){
        if(!ResponseController::validationUser()){
            return ResponseController::returnApi(false, null, "Autenticação Invállida");
        }

        $color =  Color::find($id);

        if($color){

            $products = $color->products();

            if($products){
                if(count($products) > 0){
                    return ResponseController::returnApi(false, null, "Não é possível deletar essa cor, ela está sendo utilizada por um produto.");
                }
            }

            $color->delete();
            return ResponseController::returnApi(true, null, "Cor deletada com sucesso.");
        }else{
            return ResponseController::returnApi(false, null, "Cor não encontrada.");
        }
    }

    /**
     * Update color
     *
     * @param Request $request
     * @param $id
     * @return Color
     */
    public function update(Request $request, $id){
        $request->id = $id;
        return $this->save($request);
    }
}
