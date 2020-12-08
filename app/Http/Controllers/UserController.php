<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{

    protected $validation = [
        "email" => "required",
        "password" => "required",
        "name" => "required"
    ];

    /**
     * Get all users
     *
     * @return Product[]|array|\Illuminate\Database\Eloquent\Collection
     */
    public function all(){
        if(!ResponseController::validationUser()){
            return ResponseController::returnApi(false, null, "Autenticação Invállida");
        }

        $users = User::all();

        return ResponseController::returnApi(true, $users);
    }

    /**
     * Creating or update resource.
     *
     * @return User
     */
    public function save($data)
    {
        $user = null;

        if($data->id == null || $data->id == ""){
            $user = new User;
        }else{
            $user = User::find($data->id);
        }

        if($user){
            $user->name = $data->name;
            $user->email = $data->email;
            $user->password = bcrypt($data->password);
            $user->save();

            $user->password = "";

            return $user;
        }else{
            return false;
        }
    }

    /**
     *
     * Registes new User
     *
     * @param Request $request
     * @return array
     */

    public function register(Request $request){

        $validation = Validator::make($request->all(), $this->validation);

        if($validation->fails()){
            return ResponseController::returnApi(false, null, null, $validation->errors());
        }

        $validatedData = $request->validate([
            'email' => 'required',
            'name' => 'required',
            'password' => 'required',
        ]);

        $validateEmails = User::where([
            ["email", "=", $request->email]
        ])->get();

        if(count($validateEmails) > 0){
            return ResponseController::returnApi(false, null, "E-mail já foi utilizado");
        }

        $user = $this->save($request);

        return ResponseController::returnApi(true, $user);
    }

    /**
     * Update usuario
     *
     * @param Request $request
     * @param $id
     * @return array
     */
    public function store(Request $request, $id){
        if(!ResponseController::validationUser()){
            return ResponseController::returnApi(false, null, "Autenticação Invállida");
        }

        $validatedData = $request->validate([
            'email' => 'required',
            'name' => 'required',
            'password' => 'required',
        ]);

        $request->id = $id;
        $user = $this->save($request);

        if($user){
            return ResponseController::returnApi(true, $user);
        }else{
            return ResponseController::returnApi(false, null, "Esse usuário não existe");
        }
    }

    /**
     * Delete user
     *
     * @param Request $request
     * @param $id
     * @return array
     */
    public function delete(Request $request, $id){
        if(!ResponseController::validationUser()){
            return ResponseController::returnApi(false, null, "Autenticação Invállida");
        }

        $user = User::find($id);

        if($user){
            $user->delete();
            return ResponseController::returnApi(true, null, "Usuario excluido com sucesso");
        }else{
            return ResponseController::returnApi(false, null, "Esse usuário não existe");
        }
    }

    /**
     * Return login route
     * @return array
     */
    public function login(){
        return ResponseController::returnApi(false, null, "Usuário inválido");
    }

    /**
     * Remove user by email
     *
     * @param Request $request
     * @return array
     */
    public function remove(Request $request){
        if(!ResponseController::validationUser()){
            return ResponseController::returnApi(false, null, "Autenticação Invállida");
        }

        User::where([
            ["email", "=", $request->email]
        ])->delete();

        return ResponseController::returnApi(true, null, "Usuário removido");
    }
}
