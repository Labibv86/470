<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class User extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'userid';
    public $timestamps = false;



    protected $fillable = [
        'firstname',
        'lastname',
        'email',
        'password',
        'phone',
        'nid',
        'dob',
        'address',
        'points'
    ];

    protected $hidden = ['password'];
}
