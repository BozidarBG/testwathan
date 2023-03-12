<?php

namespace App\Models;

use App\Exceptions\NotEnoughTicketsException;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Concert extends Model
{
    use HasFactory;
    protected $guarded=[];

    public function scopePublished($query){
        return $query->whereNotNull('published_at');
    }

    protected $casts=['date'=>'datetime:F j, Y g:ia'];

    public function getFormattedDateAttribute($value){
        return $this->date->format('F j, Y');
    }

    public function getFormattedStartTimeAttribute($value){
        return $this->date->format('g:ia');
    }


    public function getTicketPriceInDollarsAttribute($value)  {
        return number_format($this->ticket_price/100, 2);
    }

    public function orders(){
        return $this->hasMany(Order::class);
    }

    //vrv samo za testiranje
    public function hasOrderFor($customer_email){
        return $this->orders()->where('email', $customer_email)->count() > 0;
    }
    //vrv samo za testiranje
    public function ordersFor($customer_email){
        return $this->orders()->where('email', $customer_email)->get();
    }

    public function tickets(){
        return $this->hasMany(Ticket::class);
    }

    public function orderTickets($email, $ticket_quantity){

        $tickets=$this->tickets()->available()->take($ticket_quantity)->get();

        if($tickets->count() < $ticket_quantity){
            throw new NotEnoughTicketsException;
        }

        $order=$this->orders()->create(['email'=>$email]);

        foreach ($tickets as $ticket){
            $order->tickets()->save($ticket);
        }
        return $order;
    }

    public function addTickets($quantity){
        foreach (range(1, $quantity) as $i) {
            $this->tickets()->create([

            ]);
        }
        return $this;
    }

    public function ticketsRemaining(){
        return $this->tickets()->available()->count();
    }
}
