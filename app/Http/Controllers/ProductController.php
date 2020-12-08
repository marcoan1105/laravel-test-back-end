<?php

namespace App\Http\Controllers;

use App\Product;
use App\ProductColor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    protected $validation = [
        "description" => "required",
        "price" => "required",
        "colors" => "required"
    ];

    /**
     * Get all product
     *
     * @return Product[]|array|\Illuminate\Database\Eloquent\Collection
     */
    public function all(){
        if(!ResponseController::validationUser()){
            return ResponseController::returnApi(false, null, "Autenticação Invállida");
        }

        return ResponseController::returnApi(true, Product::all());
    }

    /**
     * Get one product
     *
     * @param Request $request
     * @param $id
     * @return array
     */
    public function getOneProduct(Request $request, $id){
        if(!ResponseController::validationUser()){
            return ResponseController::returnApi(false, null, "Autenticação Invállida");
        }

        $product = Product::find($id);

        if($product){
            $product->colors = $product->colors();

            return ResponseController::returnApi(true, $product);
        }else{
            return ResponseController::returnApi(true, null, "Produto não encontrado");
        }
    }

    /**
     * Save product
     *
     * @param $data
     * @return Product|null
     */
    public function save($request){

        if(!ResponseController::validationUser()){
            return ResponseController::returnApi(false, null, "Autenticação Invállida");
        }

        $validation = Validator::make($request->all(), $this->validation);

        if($validation->fails()){
            return ResponseController::returnApi(false, null, null, $validation->errors());
        }

        $product = null;

        if($request->id == null || $request->id == ""){
            $product = new Product;
        }else{
            $product = Product::find($request->id);

            if($product){
                ProductColor::where([
                    ["product_id", "=", $request->id]
                ])->delete();
            }else{
                return ResponseController::returnApi(true, null, "Produto não encontrado");
            }
        }

        $product->description = $request->description;
        $product->price = $request->price;
        $product->save();

        foreach ($request->colors as $color){
            $productColor = new ProductColor();
            $productColor->product_id = $product->id;
            $productColor->color_id = $color;
            $productColor->save();
        }

        $product->colors = $product->colors();

        return ResponseController::returnApi(true, $product);
    }

    /**
     * API Create / Update Product
     *
     * @param Request $request
     * @return array
     */
    public function store(Request $request){
        return $this->save($request);
    }

    /**
     * Delete product
     *
     * @param Request $request
     * @param $id
     * @return array
     */
    public function delete(Request $request, $id){
        if(!ResponseController::validationUser()){
            return ResponseController::returnApi(false, null, "Autenticação Invállida");
        }

        $product = Product::find($id);
        if($product){
            ProductColor::where([
                ["product_id", "=", $id]
            ])->delete();

            $product->delete();

            return ResponseController::returnApi(true, null, "Produto excluido com sucesso");
        }else {
            return ResponseController::returnApi(true, null, "Produto não encontrado");
        }
    }

    /**
     * Update product
     *
     * @param Request $request
     * @param $id
     */
    public function update(Request $request, $id){
        $request->id = $id;
        return $this->save($request);
    }
}
