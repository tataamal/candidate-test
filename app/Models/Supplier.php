<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Supplier extends Model
{
    protected $table = 'suppliers';

    protected $fillable = ['name'];

    public function layups(): HasMany
    {
        return $this->hasMany(Layup::class, 'supplier_id');
    }
}
