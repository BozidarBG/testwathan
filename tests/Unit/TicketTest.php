<?php

namespace Tests\Unit;

use App\Models\Concert;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class TicketTest extends TestCase
{
    use DatabaseMigrations;


    public function test_ticket_can_be_released(){
        $concert=Concert::factory()->create();
        $concert->addTickets(1);
        $order=$concert->orderTickets('jane@gmail.com', 1);
        $ticket=$order->tickets()->first();
        $this->assertEquals($order->id, $ticket->order_id);

        $ticket->release();

        //fresh() će dati svežu instancu tickets tabele u bazi pa .... ne znam
        $this->assertNull($ticket->fresh()->order_id);


    }

}
