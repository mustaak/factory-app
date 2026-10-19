<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vendor extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',

        // Business information
        'business_name',
        'email',
        'phone',
        'country_code',

        // Business address
        'address_line_1',
        'address_line_2',
        'city',
        'state',
        'postal_code',
        'country',

        // Vendor status
        'verification_status',
        'status',
        'approved_at',
    ];

    protected function casts(): array
    {
        return [
            'approved_at' => 'datetime',
        ];
    }

    /**
     * Vendor ka user account.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Vendor ke products.
     * Product model mein vendor_id hona chahiye.
     */
    // public function products(): HasMany
    // {
    //     return $this->hasMany(Product::class);
    // }
}
