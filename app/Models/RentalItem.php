<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RentalItem extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'rentalitems';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'rentalserial';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'itemid',
        'shopid',
        'renterid',
        'rentpaid',
        'rentdate',
        'returndate',
        'itemstatus'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'rentdate' => 'date',
        'returndate' => 'date',
        'rentpaid' => 'integer',
        'itemid' => 'integer',
        'shopid' => 'integer',
        'renterid' => 'integer',

    ];

    /**
     * Get the item associated with the rental.
     */
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'itemid', 'itemserial');
    }

    /**
     * Get the shop associated with the rental.
     */
    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class, 'shopid', 'shopid');
    }

    /**
     * Get the renter (user) associated with the rental.
     */
    public function renter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'renterid', 'userid');
    }

    /**
     * Scope a query to only include active rentals.
     */
    public function scopeActive($query)
    {
        return $query->where('itemstatus', 'Active');
    }

    /**
     * Scope a query to only include completed rentals.
     */
    public function scopeCompleted($query)
    {
        return $query->where('itemstatus', 'Completed');
    }

    /**
     * Scope a query to only include rentals for a specific user.
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('renterid', $userId);
    }

    /**
     * Check if the rental is currently active.
     */
    public function isActive(): bool
    {
        return $this->itemstatus === 'Active' &&
            $this->returndate > now();
    }

    /**
     * Check if the rental is overdue.
     */
    public function isOverdue(): bool
    {
        return $this->itemstatus === 'Active' &&
            $this->returndate < now();
    }

    /**
     * Calculate the remaining days for this rental.
     */
    public function getRemainingDaysAttribute(): int
    {
        if (!$this->returndate) {
            return 0;
        }

        return now()->diffInDays($this->returndate, false);
    }

    /**
     * Get the rental duration in days.
     */
    public function getRentalDurationAttribute(): int
    {
        if (!$this->rentdate || !$this->returndate) {
            return 0;
        }

        return $this->rentdate->diffInDays($this->returndate);
    }
}
