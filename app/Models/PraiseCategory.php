<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PraiseCategory extends Model
{
    use HasFactory;
    protected $guarded=[];

    public function praises()
    {
        return $this->hasMany(Praise::class);
    }
}
