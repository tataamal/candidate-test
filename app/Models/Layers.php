<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Layers extends Model
{
    protected $table = 'clt_layers';

    protected $fillable = ['layup_id', 'layer_order', 'thickness', 'width', 'angle'];

    public function layup(): BelongsTo
    {
        return $this->belongsTo(Layup::class, 'layup_id');
    }
}
