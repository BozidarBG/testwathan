<?php

namespace Tests\Unit;

use App\Models\Ticket;
use App\Reservation;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Mockery;
use Tests\TestCase;

class ReservationTest extends TestCase
{



    public function test_calculating_total_cost(){
//        $concert=Concert::factory()->create(['ticket_price'=>1200])->addTickets(3);
//
//        $tickets=$concert->findTickets(3);

        $tickets=collect([
            (object) ['price'=>1200],
            (object) ['price'=>1200],
            (object) ['price'=>1200],

        ]);

        $reservation=new Reservation($tickets);

        $this->assertEquals(3600, $reservation->totalCost());
    }

    public function test_reserved_tickets_Are_released_when_reservation_ijs_canceled(){
        //$ticket1=Mockery::mock(Ticket::class);
        //$ticket1->shouldReceive('release')->once();
        //ovo dole je isti kurac kao ovo gore x3

        //od Mockery:: do once() vraća samo mockery expectation a ako stavimo getMock, onda vraća mock a ne expectation
        //Mockery::mock(Ticket::class)->shouldReceive('release')->once()->getMock();


//        $tickets=collect([
//            Mockery::mock(Ticket::class)->shouldReceive('release')->once()->getMock(),
//            Mockery::mock(Ticket::class)->shouldReceive('release')->once()->getMock(),
//            Mockery::mock(Ticket::class)->shouldReceive('release')->once()->getMock()
//        ]);
        $tickets=collect([
            Mockery::spy(Ticket::class),
            Mockery::spy(Ticket::class),
            Mockery::spy(Ticket::class),
        ]);

        $reservation=new Reservation($tickets);

        $reservation->cancel();

        foreach ($tickets as $ticket){
            $ticket->shouldHaveReceived('release');
        }
    }
}
