<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Driver extends Model
{
    use HasFactory;

    protected $fillable = ['status'];

    protected $casts = [
        'created_at' => 'datetime: Y-m-d H:i:s',
        'updated_at' => 'datetime: Y-m-d H:i:s',
    ];

    private $driverStatus = ['free', 'shipping'];

    protected function status(): Attribute
    {
        return Attribute::make(
            get: fn (bool $value) => $this->driverStatus[$value]
        );
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}
