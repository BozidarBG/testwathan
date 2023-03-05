<?php

namespace Tests\Unit;

use App\Models\Concert;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseMigrations;
//use PHPUnit\Framework\TestCase;
use Tests\TestCase;

class ConcertTest extends TestCase
{
    use DatabaseMigrations; //logika:nije nam potrebno da bilo šta upisijemo u DB za ove testove
    //pa možemo samo da kreiramo objekat sa make

    public function test_can_get_formated_date()
    {
        //kreiraj koncert sa poznatim datumom
        //uzeti formatiran datum
        //verifikuj da je datum formatiran kako očekujemo

        $concert=Concert::factory()->make([
            'date'=>Carbon::parse('2016-12-01 8:00pm')
        ]);



        $this->assertEquals('December 1, 2016', $concert->formatted_date);
    }

    public function test_can_get_formatted_start_time(){
        $concert=Concert::factory()->make([
            'date'=>Carbon::parse('2016-12-01 17:00:00')
        ]);

        $this->assertEquals('5:00pm', $concert->formatted_start_time);
    }

    public function test_can_get_ticket_price_in_dollars(){
        $concert=Concert::factory()->make([
            'ticket_price'=>2000
        ]);

        $this->assertEquals('20.00', $concert->ticket_price_in_dollars);
    }

    public function test_user_can_order_tickets(){
        $concert=Concert::factory()->published()->create();

        $order=$concert->orderTickets('glup@gmail.com', 3);

        $this->assertEquals('glup@gmail.com', $order->email);
        $this->assertEquals(3, $order->tickets()->count());
    }
}
