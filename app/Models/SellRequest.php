<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
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
