<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Auth\VerifiesEmails;
use Illuminate\Auth\Events\Verified;

class VerificationController extends Controller
{
    use VerifiesEmails;
    public function __construct()
    {
        $this->middleware('auth:api')->only('resend');
        $this->middleware('signed')->only('verify');
        $this->middleware('throttle:6,1')->only('verify', 'resend');
    }

    // public function verify($user_id, Request $request) {
    //     if (!$request->hasValidSignature()) {
    //         return response()->json(["msg" => "Invalid/Expired url provided."], 401);
    //     }

    //     $user = User::findOrFail($user_id);
    //     return response()->json($user);
    //     if (!$user->hasVerifiedEmail()) {
    //         $user->markEmailAsVerified();
    //     }

    //     return redirect()->to('/');
    // }
    public function verify(Request $request)
    {
        auth()->loginUsingId($request->route('id'));

        if ($request->route('id') != $request->user()->getKey()) {
            throw new AuthorizationException;
        }

        if ($request->user()->hasVerifiedEmail()) {

            return response(['message'=>'Already verified']);

            // return redirect($this->redirectPath());
        }

        if ($request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));
        }

        return response(['message'=>'Successfully verified']);

    }


    public function resend(Request $request) {
        // return $request->all();
        // $token = $request -> header('auth-token');
        // return response() -> json(['token' => $token]);
        if($request -> user()->hasVerifiedEmail()){
            return response()->json(["msg" => "Email already verified."], 400);
        }
        try{
        $request-> user() ->sendEmailVerificationNotification();
        }catch(\Exception $ex){
            return response()->json(["msg" => "there is error in sending email verify"]);
        }
        if (auth()->user()->hasVerifiedEmail()) {
            return response()->json(["msg" => "Email already verified."], 400);
        }

        auth()->user()->sendEmailVerificationNotification();

        return response()->json(["msg" => "Email verification link sent on your email id"]);
    }
}
