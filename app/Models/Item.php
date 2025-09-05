<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $table = 'items';
    protected $primaryKey = 'itemserial';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = false;

    protected $fillable = [
        'shopid',
        'itemname',
        'itemmodel',
        'itemcategory',
        'itemdescription',
        'resaleprice',
        'rentalprice',
        'biddingprice',
        'itemimage',
        'itemuse',
        'itemstatus',
        'itemcondition',
        'itemgender',
        'totalcopies',

    ];
    protected $casts = [
        'resaleprice'   => 'integer',
        'rentalprice'   => 'integer',
        'biddingprice'  => 'integer',
        'totalcopies'   => 'integer',
        'shopid'        => 'integer',
    ];


    public function shop()
    {
        return $this->belongsTo(Shop::class, 'shopid', 'shopid');
    }
    public function rentalItems()
    {
        return $this->hasMany(RentalItem::class, 'itemid', 'itemserial');
    }
    public function resaleItems()
    {
        return $this->hasMany(ResaleItem::class, 'itemid', 'itemserial');
    }

}
