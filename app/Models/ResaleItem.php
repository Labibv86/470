<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class ResaleItem extends Model

{

    protected $table = 'resaleitems';
    protected $primaryKey = 'resaleserial';
    public $timestamps = false;
    protected $fillable = [
        'shopid',
        'itemid',
        'currentbid',
        'forcebuyprice',
        'resalestatus',
        'lastbidderid',

    ];

    public function item()
    {
        return $this->belongsTo(Item::class, 'itemid', 'itemserial');
    }

    public function shop()
    {
        return $this->belongsTo(Shop::class, 'shopid', 'shopid');
    }
}

