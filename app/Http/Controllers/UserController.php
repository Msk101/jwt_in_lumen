<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\User;
use Auth;
class UserController extends Controller
{
    private $salt;
    public function __construct()
    {
        $this->salt="userloginregister";
    }
    public function login(Request $request){
      if ($request->has('email') && $request->has('password')) {
        $params['email'] = $request->input('email');
        $params['password'] = sha1($this->salt.$request->input('password'));
        $user = User::loginUser($params); 
        if ($user) {
          $token=str_random(60);
          $user->api_token=$token;
          $user->save();
          return $user;
        } else {
          return "Successfully login";
        }
      } else {
        return "criendial not match";
      }
    }

    public function register(Request $request){
      if ($request->has('firstName') && $request->has('lastName') && $request->has('email') && $request->has('password') ) {
        $user = new User;
        $user->firstName=$request->input('firstName');
        $user->lastName=$request->input('lastName');
        $user->email=$request->input('email');
        $user->password=sha1($this->salt.$request->input('password'));
        $user->active=1;
        $user->api_token=str_random(60);
        if($user->save()){
          return "User registration is successful!";
        } else {
          return "User registration failed!";
        }
      } else {
        return "Please enter the complete user information!";
      }
    }
    public function info(){
      return Auth::user();
    }
}
