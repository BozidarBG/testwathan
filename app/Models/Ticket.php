<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;

    protected $guarded=[];

    public function scopeAvailable($query){
        return $query->whereNull('order_id');
    }

    public function release(){
        $this->update(['order_id'=>null]);
    }
}
