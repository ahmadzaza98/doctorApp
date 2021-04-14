<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use app\Models\User;


class DrugController extends Controller
{
    public function AddDrugToPatient(Request $request , $id){
        $validator = Validator::make($request->all(),[
            'name' => 'required|string',
            'company' => 'required|string',
            'scientific_name' => 'required|string',
        ]);
        if($validator->fails()){
            return response()->json($validator->errors()->toJson() , 400);
        }


    }

    public function getPermanent($id){
        $user = User::with('allergen_drugs')->find($id);

        return response()->json([
            'Patien with his permanent drugs' => $user,
        ],200);
    }
}
