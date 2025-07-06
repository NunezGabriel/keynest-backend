<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;



class Property extends Model
{
    use HasFactory;
    protected $primaryKey = 'property_id';

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'property_type',
        'price',
        'maintenance_cost',
        'is_rent',
        'square_meters',
        'bedrooms',
        'bathrooms',
        'pets_allowed',
        'location',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function images()
    {
        return $this->hasMany(PropertyImage::class, 'property_id');
    }

    public function favorites()
    {
        return $this->hasMany(Favorite::class, 'property_id');
    }
}
