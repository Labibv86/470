<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    protected $table = 'cart';

    protected $primaryKey = 'sl';

    public $timestamps = false;

    protected $fillable = [
        'userid',
        'itemid',
        'shopid',
        'totalamount',
        'paymentstatus',
        'rentalid',
    ];


    public function item()
    {
        return $this->belongsTo(Item::class, 'itemid', 'itemserial');
    }


    public function user()
    {
        return $this->belongsTo(User::class, 'userid', 'userid');
    }
}
