<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class SellRequest extends Model


{
    protected $table = 'sellrequests';
    protected $primaryKey = 'serial';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = false;

    protected $fillable = [
        'shopid',
        'itemname',
        'itemmodel',
        'itemcategory',
        'itemdescription',
        'originalprice',
        'askingprice',
        'itemimage',
        'itemstatus',
    ];
}
