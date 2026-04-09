<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Layers extends Model
{
    protected $table = "clt_layers";
    protected $fillable = ["layup_id", "layer_order", "thickness", "width", "angle"];

    public function layup()
    {
        return $this->belongsTo(Layups::class, "layup_id");
    }
}
