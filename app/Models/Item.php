<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
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
    public function getImageUrlAttribute()
    {
        // If it's already a full URL (Supabase), return it directly
        if ($this->itemimage && filter_var($this->itemimage, FILTER_VALIDATE_URL)) {
            return $this->itemimage;
        }

        // If it's a local path, generate the storage URL
        if ($this->itemimage) {
            return Storage::disk('public')->url($this->itemimage);
        }

        // Fallback to default image
        return asset('images/default-item.png');
    }
}
