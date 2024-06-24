<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Praise extends Model
{
    use HasFactory;
    protected $guarded = [];



    public function praiseCategory()
    {
        return $this->hasOne(PraiseCategory::class, 'id');
    }
    public function Sender()
    {
        return $this->hasOne(User::class, 'id', 'sender_id');
    }

    public function Receiver()
    {
        return $this->hasOne(User::class, 'id', 'receiver_id');
    }
}
