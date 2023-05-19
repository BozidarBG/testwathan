<?php

namespace Tests\Unit;

use App\Models\Concert;
use App\Models\Order;
use App\Models\Ticket;
use App\Reservation;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use DatabaseMigrations;

    public function test_creating_an_order_from_tickets_email_an_amount(){
        $concert=Concert::factory()->create()->addTickets(5);

        $this->assertEquals(5, $concert->ticketsRemaining());


        $order=Order::forTickets($concert->findTickets(3), 'john@example.com', 3600);

        $this->assertEquals('john@example.com', $order->email);
        $this->assertEquals(3, $order->ticketsQuantity());
        $this->assertEquals(3600, $order->amount);
        $this->assertEquals(2, $concert->ticketsRemaining());
    }

    //obrisao jer ne koristimo više ovakav način kreiranja ordera
//    public function test_creating_an_order_from_reservation(){
//        $concert=Concert::factory()->create(['ticket_price'=>1200]);
//        $tickets=Ticket::factory()->count(3)->create(['concert_id'=>$concert->id]);
//        $reservation=new Reservation($tickets, 'john@example.com',);
//
//        $order=Order::fromReservation($reservation);
//
//        $this->assertEquals('john@example.com', $order->email);
//        $this->assertEquals(3, $order->ticketsQuantity());
//        $this->assertEquals(3600, $order->amount);
//    }

    public function test_converting_to_array(){
        $concert=Concert::factory()->create(['ticket_price'=>1200])->addTickets(5);
        $order=$concert->orderTickets('jo@gmail.com', 5);

        $result=$order->toArray();

        $this->assertEquals([
            'email'=>'jo@gmail.com',
            'ticket_quantity'=>5,
            'amount'=>6000
        ], $result);
    }
/* obrisao je ovo jer aplikacija nema više metodu koja briše order
    public function test_ticket_are_released_when_order_is_canceled(){


        $concert=Concert::factory()->create()->addTickets(10);
        $order=$concert->orderTickets('jo@gmail.com', 5);
        $this->assertEquals(5, $concert->ticketsRemaining());

        $order->cancel();

        $this->assertEquals(10, $concert->ticketsRemaining());
        $this->assertNull(Order::find($order->id));
    }
*/
}
