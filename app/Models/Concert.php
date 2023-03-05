<?php

namespace App\Models;

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

    public function orderTickets($email, $ticket_quantity){
        $order=$this->orders()->create(['email'=>$email]);

        foreach (range(1, $ticket_quantity) as $i){
            $order->tickets()->create([

            ]);
        }
        return $order;
    }
}
