<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class ResaleItem extends Model
{
    protected $table = 'resaleitems';
    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $fillable = [
        'shopid',
        'itemid',
        'currentbid',
        'forcebuyprice',
        'resalestatus',

    ];
}
