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

//    public static function fromReservation($reservation){
//        $order=self::create([
//            'email'=>$reservation->email(),
//            'amount'=>$reservation->totalCost()
//        ]);
//
//        $order->tickets()->saveMany($reservation->tickets());
//
//
//        return $order;
//    }

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
