<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\User;
use App\Traits\GeneralTrait;
use Illuminate\Http\Request;
use App\Providers\RouteServiceProvider;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Contracts\JWTSubject;

class AuthController extends Controller
{
    use GeneralTrait;

    public function register(Request $request){
        $validator = Validator::make($request->all(),[
            'name' => 'required|string',
            'email' => 'required|email|max:255',
            'password' => 'required|string|max:8|confirmed',
        ]);

        if($validator -> fails()){
            return response() -> json ($validator -> errors() -> toJson() , 400 );
        }

        $admin = Admin::create([
            'name' => $request->get('name'),
            'email' => $request->get('email'),
            'password' => bcrypt($request->get('password')),
        ]);
        //->sendEmailVerificationNotification();

        $token = JWTAuth::fromUser($admin);

        return response() -> json(compact('admin' , 'token'), 200);
    }

    public function UserRegister(Request $request){
        $validator = Validator::make($request->all(),[
            'name' => 'required|string',
            'email' => 'required|email|max:255',
            'password' => 'required|string|max:8|confirmed',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors()->toJson() , 400);
        }

        $user = User::create([
            'name' => $request->get('name'),
            'email' => $request->get('email'),
            'password' => bcrypt($request->get('password')),
        ]);

        $token = JWTAuth::fromUser($user);

        return response() -> json(compact('user' , 'token'),200);
    }


    public function login(Request $request){

        try{


        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'password' => 'required'
        ]);

        if($validator -> fails()){

            $code = $this -> returnCodeAccordingToInput($validator);
            return $this -> returnValidationError($code , $validator);
        }

        $credentials = $request->only(['email', 'password']);

        $token = Auth::guard('admin-api')->attempt($credentials);
        //return response() -> json($token);
        if (!$token)
            return $this->returnError('E001', 'بيانات الدخول غير صحيحة');

        //return response()->json($token);
        $admin = Auth::guard('admin-api')->user();
        $admin->api_token = $token;
        //return token
        return $this->returnData('admin', $admin);

    }catch(\Exception $ex){
        //return response()->json($validator);
        return $this -> returnError($ex->getCode(), $ex->getMessage());
    }

    }

    // LOGOUT FUNCTION
    public function logout(Request $request)
    {

         $token = $request -> header('auth-token');
        //return response() -> json($token);
         if($token){
            try {

                JWTAuth::setToken($token)->invalidate(); //logout
            }catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e){
                return  $this -> returnError('','some thing went wrongs');
            }
            return $this->returnSuccessMessage("Logged out successfully" , 'E000');
        }else{
            $this -> returnError('','some thing went wrongs');
        }

    }
}
