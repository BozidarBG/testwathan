<?php

namespace Tests\Unit;

use App\Models\Concert;
use App\Models\Ticket;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class TicketTest extends TestCase
{
    use DatabaseMigrations;

    public function test_ticket_can_be_reserved(){
        $ticket=Ticket::factory()->create();

        $this->assertNull($ticket->reserved_at);

        $ticket->reserve();

        $this->assertNotNull($ticket->fresh()->reserved_at);
    }

    /* obrisao je ceo ovaj i napravio drugačije
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
    */
    public function test_ticket_can_be_released(){
        $ticket=Ticket::factory()->reserved()->create();

        $this->assertNotNull($ticket->reserved_at);

        $ticket->release();//act faza

        $this->assertNull($ticket->fresh()->reserved_at);
    }

}
