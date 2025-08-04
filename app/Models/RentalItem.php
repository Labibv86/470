<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class RentalItem extends Model
{
protected $table = 'rentalitems';
protected $primaryKey = 'id';
public function item()
{
return $this->belongsTo(Item::class, 'ItemID', 'ItemSerial');
}
}
