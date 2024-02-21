<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    use HasFactory;

    protected $fillable = ['driver_id', 'progress', 'status'];

    protected $casts = [
        'created_at' => 'datetime: Y-m-d H:i:s',
        'updated_at' => 'datetime: Y-m-d H:i:s',
    ];

    private $orderStatus = ['ordered', 'shipping', 'completed', 'canceled.'];

    protected function status(): Attribute
    {
        return Attribute::make(
            get: fn (int $value) => $this->orderStatus[$value]
        );
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class);
    }
}
