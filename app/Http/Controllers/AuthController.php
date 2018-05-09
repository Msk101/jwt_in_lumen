<?php

namespace App\Http\Controllers;

use Validator;
use App\User;
use Firebase\JWT\JWT;
use Illuminate\Http\Request;
use Firebase\JWT\ExpiredException;
use Illuminate\Support\Facades\Hash;
use Laravel\Lumen\Routing\Controller;

class AuthController extends Controller 
{
    /**
     * The request instance.
     *
     * @var \Illuminate\Http\Request
     */
    private $request;

    /**
     * Create a new controller instance.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    public function __construct(Request $request) {
        $this->request = $request;
    }
    /**
     * 
     * 
     * @param  \App\User   $user
     * @return string
     */
    protected function jwt(User $user) {
        $payload = [
            'iss' => "lumen-jwt", // Issuer of the token
            'sub' => $user->id, // Subject of the token
            'iat' => time(), // Time when JWT was issued. 
            'exp' => time() + 60*60 // Expiration time
        ];
        
        return JWT::encode($payload, env('JWT_SECRET'));
    } 

    /**
     * Authenticate a user and return the token if the provided credentials are correct.
     * 
     * @param  \App\User   $user 
     * @return mixed
     */
    public function authenticate(Request $request) {
        $this->validate($this->request, [
            'email'     => 'required|email',
            'password'  => 'required'
        ]);

        // Find the user by email
       
        $user = User::getUserByEmail($this->request->input('email'));

        if (!$user) {
            
            return response()->json([
                'error' => 'Email does not exist.'
            ], 400);
        }

        // Verify the password and generate the token
        if (Hash::check($this->request->input('password'), $user->password)) {
            return response()->json([
                'api_token' => $this->jwt($user)
            ], 200);
        }

        // Bad Request response
        return response()->json([
            'error' => 'Email or password is wrong.'
        ], 400);
    }


    public function signup(Request $request){
         
         $payload = [
            'iss' => "lumen-jwt", // Issuer of the token
            'sub' =>  $this->request->input('email'), //"ngisecret",//$user->id, // Subjectthe token
            'iat' => time(), // Time when JWT was issued. 
            'exp' => time() + 60 // Expiration time
        ];
        
      $user= $this->validate($this->request, [
            'firstName'     => 'required',
            'lastName'     => 'required',
            'email'     => 'required|email|unique:users',
            'password'  => 'required'
        ]);

       $data = ([

            'firstName'=> $this->request->input('firstName'),
            'lastName' => $this->request->input('lastName'),
            'email' => $this->request->input('email'),
            'password' => hash::make($this->request->input('password')),
            'api_token' =>JWT::encode($payload, env('JWT_SECRET')),
            'active' => 1
   
        ]);
  
            $user = new User;
            $check = $user->add($data); 
        if($check){

            return response()->json([
                'status' => 'succesfully added.'
            ], 200);

        } 

        return response()->json(['error' => 'Please enter the complete user information!.'], 400);
      }   
}

