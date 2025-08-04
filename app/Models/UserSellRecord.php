<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class UserSellRecord extends Model
{
    protected $table = 'usersellrecords';
    protected $primaryKey = 'sl';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = false;
    protected $fillable = [
        'userid',
        'sellrequestserial',
    ];
}
