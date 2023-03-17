<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $guarded=[];

    public static function forTickets($tickets, $email, $amount){
        $order=self::create([
            'email'=>$email,
            'amount'=>$amount
        ]);

        foreach ($tickets as $ticket){
            $order->tickets()->save($ticket);
        }

        return $order;
    }

    public function tickets(){
        return $this->hasMany(Ticket::class);
    }

    public function concert(){
        return $this->belongsTo(Concert::class);
    }



    public function ticketsQuantity(){
        return $this->tickets()->count();
    }

    public function toArray(){
        return [
            'email'=>$this->email,
            'ticket_quantity'=>$this->ticketsQuantity(),
            'amount'=>$this->amount
        ];
    }
}
