<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $guarded=[];

    public function tickets(){
        return $this->hasMany(Ticket::class);
    }

    public function cancel(){
        //prvo brišemo sve kupljene tikete za ovaj order
        foreach($this->tickets as $ticket){
            $ticket->release();
        }

        //onda brišemo i ovaj order
        $this->delete();
    }

    public function ticketsQuantity(){
        return $this->tickets()->count();
    }
}
