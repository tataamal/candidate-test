<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Layup extends Model
{
    protected $table = 'clt_layups';

    protected $fillable = ['supplier_id', 'name'];

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    public function layers(): HasMany
    {
        return $this->hasMany(Layers::class, 'layup_id');
    }
}
