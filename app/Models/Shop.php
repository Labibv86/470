<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
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
    ];
    public function getLogoUrlAttribute()
    {
        // If it's already a full URL (Supabase), return it directly
        if ($this->shoplogo && filter_var($this->shoplogo, FILTER_VALIDATE_URL)) {
            return $this->shoplogo;
        }

        // If it's a local path, generate the storage URL
        if ($this->shoplogo) {
            return Storage::disk('public')->url($this->shoplogo);
        }

        // Fallback to default image
        return asset('images/default-shop.png');
    }
}


