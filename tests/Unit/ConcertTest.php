<?php

namespace Tests\Unit;

use App\Exceptions\NotEnoughTicketsException;
use App\Models\Concert;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseMigrations;
//use PHPUnit\Framework\TestCase;
use Tests\TestCase;

class ConcertTest extends TestCase
{
    use DatabaseMigrations; //logika:nije nam potrebno da bilo šta upisijemo u DB za ove testove
    //pa možemo samo da kreiramo objekat sa make

    public function test_can_get_formatted_date()
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
        $concert=Concert::factory()->published()->create()->addTickets(3);

        $order=$concert->orderTickets('glup@gmail.com', 3);

        $this->assertEquals('glup@gmail.com', $order->email);
        $this->assertEquals(3, $order->ticketsQuantity());
    }

    public function test_can_add_tickets(){
        $concert=Concert::factory()->published()->create()->addTickets(50);

        $this->assertEquals(50, $concert->ticketsRemaining());

    }

    public function test_tickets_remaining_does_not_include_tickets_associated_with_an_order(){
        $concert=Concert::factory()->published()->create();
        $concert->addTickets(50);
        $concert->orderTickets('glup@gmail.com', 30);

        $this->assertEquals(20, $concert->ticketsRemaining());
    }

    public function test_trying_to_purchase_more_tickets_than_remaining_throws_an_ecxeption(){
        $concert=Concert::factory()->published()->create()->addTickets(10);


        try{
            $concert->orderTickets('glup@gmail.com', 11);
        }catch(NotEnoughTicketsException $e){
            $this->assertFalse($concert->hasOrderFor('glup@gmail.com'));

            $this->assertEquals(10, $concert->ticketsRemaining());

            return;
        }

        $this->fail('order succeded iako nemamo dovoljno karata');
    }

    public function test_cannot_order_tickets_that_have_already_been_purchsed(){
        $concert=Concert::factory()->published()->create()->addTickets(10);

        $concert->orderTickets('glup@gmail.com', 8);


        try{
            $concert->orderTickets('lebron@gmail.com', 3);
        }catch(NotEnoughTicketsException $e){
            $this->assertFalse($concert->hasOrderFor('lebron@gmail.com'));

            $this->assertEquals(2, $concert->ticketsRemaining());

            return;
        }

        $this->fail('order succeded iako nemamo dovoljno karata 2');

    }

    public function test_can_reserve_available_tickets(){
        $concert=Concert::factory()->published()->create()->addTickets(3);
        $this->assertEquals(3, $concert->ticketsRemaining());

        $reservation=$concert->reserveTickets(2, 'john@gmail.com');

        $this->assertCount(2, $reservation->tickets());
        $this->assertEquals('john@gmail.com', $reservation->email());
        $this->assertEquals(1,$concert->ticketsRemaining());

    }

    public function test_cannot_reserve_tickets_that_are_purchased(){
        $concert=Concert::factory()->published()->create()->addTickets(3);
        $concert->orderTickets('bo@gmail.com', 2);

        try{
            $concert->reserveTickets(2, 'john@gmail.com');
        }catch (NotEnoughTicketsException $e){
            $this->assertEquals(1, $concert->ticketsRemaining());
            return;
        }

        $this->fail('reserving tickets ok even tho the thickets are sold');
    }

    public function test_cannot_reserve_tickets_that_are_reserved(){
        $concert=Concert::factory()->published()->create()->addTickets(3);
        $concert->reserveTickets(2, 'boloen@gmail.com');

        try{
            $concert->reserveTickets(2, 'john@gmail.com');
        }catch (NotEnoughTicketsException $e){
            $this->assertEquals(1, $concert->ticketsRemaining());
            return;
        }

        $this->fail('reserving tickets ok even tho the thickets are reserved');
    }
}
