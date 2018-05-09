<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;

class User extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'firstName','lastName', 'email', 'active',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password','api_token',
    ];
    
    protected function getUserByEmail($email){
	return User::where('email',$email)->first();
    }
	
    protected function loginUser($params){
	return User::where("email", "=", $params['email'])
                      ->where("password", "=", $params['password'])
                      ->first();
    }
	function add($data = array())
    {
        return DB::table($this->table)->insert($data);
    }
}
