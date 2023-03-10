<?php

namespace Tests\Feature;

use App\Billing\PaymentGateway;
use App\Models\Concert;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Billing\FakePaymentGateway;


class PurchaseTicketsTest extends TestCase
{
    use DatabaseMigrations;
    public $payment_gateway;

    protected function setUp(): void
    {
        parent::setUp(); // TODO: Change the autogenerated stub
        $this->payment_gateway=new FakePaymentGateway;
        //ne znam šta ovo znači.negde na početku drugog odeljka
        $this->app->instance(PaymentGateway::class, $this->payment_gateway);
    }

    private function orderTickets($concert, $params){
        $this->json('POST', "/concerts/{$concert->id}/orders", $params);
    }

    private function customAssertValidationError($field){
        $this->assertResponseStatus(422);
        //umesto this decodeResponseJson()
        //dd($this->response->assertJsonValidationErrors('email'));
        //$this->assertArrayHasKey('email', $this->decodeResponseJson()); - zastarelo, treba ovo dole
        $this->response->assertJsonValidationErrors($field);
    }

    public function test_customer_can_purchase_published_concert_ticket()
    {
        //$this->disableExceptionHandling();
        $concert=Concert::factory()->published()->create(['ticket_price'=>3250])->addTickets(4);


        $this->orderTickets($concert, [
            'email'=>'john@gmail.com',
            'ticket_quantity'=>3,
            'payment_token'=>$this->payment_gateway->getValidToken()
        ]);

        $this->assertResponseStatus(201);
        //znači da je naplaćeno kupcu

        $this->assertEquals(9750, $this->payment_gateway->totalCharges());
        $this->assertTrue($concert->hasOrderFor('john@gmail.com'));
        $this->assertEquals(3, $concert->ordersFor('john@gmail.com')->first()->ticketsQuantity());


    }

    public function test_cannot_purchase_tickets_to_unpublished_concert(){

        //dole skidamo komentar ako želimo da vidimo koji je exception (bio je model not foun)
        //a pošto želimo da laravel prebaci u 404, ne treba disableExceptionHandling
        //$this->disableExceptionHandling();
        $concert=Concert::factory()->unpublished()->create()->addTickets(3);

        $this->orderTickets($concert, [
            'email'=>'john@gmail.com',
            'ticket_quantity'=>3,
            'payment_token'=>$this->payment_gateway->getValidToken()
        ]);

        $this->assertResponseStatus(404);
        $this->assertFalse($concert->hasOrderFor('john@gmail.com'));

        //znači da nije naplaćeno kupcu
        $this->assertEquals(0, $this->payment_gateway->totalCharges());

    }

    public function test_email_is_required_to_purchase_tickets(){
        //$this->disableExceptionHandling();

        $concert=Concert::factory()->published()->create()->addTickets(3);

        $this->orderTickets($concert, [
            'ticket_quantity'=>3,
            'payment_token'=>$this->payment_gateway->getValidToken()
        ]);

        $this->customAssertValidationError('email');


    }

    public function test_email_must_be_valid_to_purchase_tickets(){
        $concert=Concert::factory()->published()->create();
        $concert->addTickets(3);

        $this->orderTickets($concert, [
            'email'=>'nije mail',
            'ticket_quantity'=>3,
            'payment_token'=>$this->payment_gateway->getValidToken()
        ]);

        $this->customAssertValidationError('email');
    }

    public function test_ticket_quantity_is_required_to_purchase_tickets(){
        $concert=Concert::factory()->published()->create();
        $concert->addTickets(3);

        $this->orderTickets($concert, [
            'email'=>'nije mail',
            'payment_token'=>$this->payment_gateway->getValidToken()
        ]);

        $this->customAssertValidationError('ticket_quantity');
    }

    public function test_ticket_quantity_must_be_at_least_one_to_purchase_tickets(){
        $concert=Concert::factory()->published()->create();
        $concert->addTickets(3);

        $this->orderTickets($concert, [
            'email'=>'jeste@email.com',
            'ticket_quantity'=>0,
            'payment_token'=>$this->payment_gateway->getValidToken()
        ]);

        $this->customAssertValidationError('ticket_quantity');
    }

    public function test_payment_token_is_required_to_purchase_tickets(){
        $concert=Concert::factory()->published()->create();
        $concert->addTickets(3);

        $this->orderTickets($concert, [
            'email'=>'jeste@email.com',
            'ticket_quantity'=>3,
        ]);

        $this->customAssertValidationError('payment_token');
    }

    public function test_order_is_not_created_if_payment_fails(){

        //kad javi da očekujemo 422 a lara da 500 onda moramo da enejblujemo ovo ispod
        //sprečavamo laravel da konvertuje exception u http response
        $this->disableExceptionHandling();

        $concert=Concert::factory()->published()->create(['ticket_price'=>3250])->addTickets(2);

        $this->orderTickets($concert, [
            'email'=>'john@gmail.com',
            'ticket_quantity'=>3,
            'payment_token'=>'invalid-token'
        ]);

        $this->assertResponseStatus(422);
       // $order=$concert->orders()->where('email', 'john@gmail.com')->first();
        $this->assertFalse($concert->hasOrderFor('john@gmail.com'));
    }

    public function test_cannot_purchase_more_tickets_than_remain(){
        //$this->disableExceptionHandling();
        $concert=Concert::factory()->published()->create()->addTickets(50);

        $this->orderTickets($concert, [
            'email'=>'john@gmail.com',
            'ticket_quantity'=>51,
            'payment_token'=>$this->payment_gateway->getValidToken()
        ]);

        $this->assertResponseStatus(422);
        $this->assertFalse($concert->hasOrderFor('john@gmail.com'));///proveravamo da nije kreiran order
        $this->assertEquals(0, $this->payment_gateway->totalCharges());//da nije naplaćeno korisniku
        $this->assertEquals(50, $concert->ticketsRemaining()); //da je ostalo i dalje 50 karata
    }


}
