<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function __construct()
    {
        $this -> middleware('assign.guard:admin-api');
    }
    public function index(){
        $admins = Admin::all();
        return response() -> json([
            'data' => $this -> transformCollection($admins)

        ],200);
    }

    public function show($id){
        $admin = Admin::find($id);
        return response() -> json([
            'data' => $admin
        ]);
    }

    private function transformCollection($contacts)
    {
        return array_map([$this, 'transform'], $contacts->toArray());
    }

    private function transform($contact)
    {
        return [
            'id' => $contact['id'],
            'email' => $contact['email'],
            'password' => $contact['password']
        ];
    }
}
