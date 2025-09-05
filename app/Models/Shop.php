<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Shop extends Model
{
    protected $table = 'shops';
    protected $primaryKey = 'shopid';

    public $timestamps = false;

    protected $fillable = [
        'shopname',
        'shopemail',
        'shoppassword',
        'shopphone',
        'license',
        'officeaddress',
        'shoplogo',
        'userid',
        'points',
    ];

}


