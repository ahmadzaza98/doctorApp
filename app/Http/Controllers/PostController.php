<?php

namespace App\Http\Controllers;
use App\Traits\GeneralTrait;
use App\Models\Comment;
use App\Models\Post;
use App\Models\Admin;
use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;


class PostController extends Controller
{
    use GeneralTrait;
    public function __construct()
    {
       // $this -> middleware('assign.guard:admin-api');
    }


    public function index(){
        $posts = Post::all();
        foreach($posts as $post){
            $comments = $post->comments();
            return response() -> json([
                'posts' => $this -> transformCollection($posts),
            ]);
        }
    }

    public function showComments($id){
        $post = Post::with('comments')->find($id);

        return response() -> json ([
            'result' => $post,
        ],200);
    }

    public function store(Request $request)
    {
        $this-> validate($request , [
            'title' => 'required|max:255',
            'content' => 'required',
        ]);
        $token= $request -> header('auth-token');
        $input = Arr::except($request->all() , array('_token'));
        $po = Post::create([
            'title' => $request->title,
            'content' => $request -> content,
            'patient_id' => 1,
            'created_at' => $request->created_at,
            'updated_at' => $request->updated_at,
        ]);


        // $notification = DB::table('admins')->get();
        // foreach ($notification as $key => $value) {
        //     $this->notification($value->token, $request->get('title'));
        // }
        $this -> notification($token , $request->get('title'));

       // Session::put('success','Post store and send notification successfully!!');

        return $this -> returnSuccessMessage('the save has done' , 'E000');


    }





    public function notification($token, $title)
    {
        $fcmUrl = 'https://fcm.googleapis.com/fcm/send';
        $token=$token;

        $notification = [
            'title' => $title,
            'sound' => true,
        ];

        $extraNotificationData = ["message" => $notification,"moredata" =>'dd'];

        $fcmNotification = [
            //'registration_ids' => $tokenList, //multple token array
            'to'        => $token, //single token
            'notification' => $notification,
            'data' => $extraNotificationData
        ];

        $headers = [
            'Authorization: key=Legacy server key',
            'Content-Type: application/json'
        ];


        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$fcmUrl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fcmNotification));
        $result = curl_exec($ch);
        curl_close($ch);

        return true;
    }




}
