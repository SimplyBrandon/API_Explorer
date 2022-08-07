<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Provider extends Model
{
    use HasFactory;

    protected $casts = [
        'info' => 'array',
    ];

    protected $appends = [
        'is_outdated'
    ];

    public function updateFromAPI($attributes, $options = [])
    {
        $this->name = $attributes['name'];
        $this->type = $attributes['type'];
        $this->postcode = $attributes['postalCode'];
        $this->info = $attributes;
        $this->updated_at = now();
        $this->save($options);
    }

    public function getIsOutdatedAttribute()
    {
        return $this->updated_at->diffInDays(now()) > 7;
    }
}