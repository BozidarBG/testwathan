<?php

namespace Tests\Unit;

use App\Billing\FakePaymentGateway;
use App\Models\Concert;
use App\Models\Order;
use App\Models\Ticket;
use App\Reservation;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Mockery;
use Tests\TestCase;

class ReservationTest extends TestCase
{
    use DatabaseMigrations;

    public function test_calculating_total_cost(){
//        $concert=Concert::factory()->create(['ticket_price'=>1200])->addTickets(3);
//
//        $tickets=$concert->findTickets(3);

        $tickets=collect([
            (object) ['price'=>1200],
            (object) ['price'=>1200],
            (object) ['price'=>1200],

        ]);

        $reservation=new Reservation($tickets, 'john@gmail.com');

        $this->assertEquals(3600, $reservation->totalCost());
    }

    public function test_reserving_the_reservetions_tickets(){

        $tickets=collect([
            (object) ['price'=>1200],
            (object) ['price'=>1200],
            (object) ['price'=>1200],

        ]);

        $reservation=new Reservation($tickets, 'john@gmail.com');

        $this->assertEquals($tickets, $reservation->tickets());
    }

    public function test_reserving_the_customers_email(){

        $reservation=new Reservation(collect(), 'john@gmail.com');

        $this->assertEquals('john@gmail.com', $reservation->email());
    }

    public function test_reserved_tickets_are_released_when_reservation_is_canceled(){
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

        $reservation=new Reservation($tickets, 'john@gmail.com');

        $reservation->cancel();

        foreach ($tickets as $ticket){
            $ticket->shouldHaveReceived('release');
        }
    }

    public function test_completing_reservation(){
        $concert=Concert::factory()->create(['ticket_price'=>1200]);
        $tickets=Ticket::factory()->count(3)->create(['concert_id'=>$concert->id]);
        $reservation=new Reservation($tickets, 'john@example.com',);
        $payment_gateway=new FakePaymentGateway();

        $order=$reservation->complete($payment_gateway, $payment_gateway->getValidTestToken());

        $this->assertEquals('john@example.com', $order->email);
        $this->assertEquals(3, $order->ticketsQuantity());
        $this->assertEquals(3600, $order->amount);
        $this->assertEquals(3600, $payment_gateway->totalCharges());
    }
}
