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

    public function test_customer_can_purchase_ticket()
    {
        $payment_gateway=new FakePaymentGateway;
        //kad
        $this->app->instance(PaymentGateway::class, $payment_gateway);

        $concert=Concert::factory()->create(['ticket_price'=>3250]);

        $this->json('POST', "/concerts/{$concert->id}/orders", [
            'email'=>'john@gmail.com',
            'ticket_quantity'=>3,
            'payment_token'=>$payment_gateway->getValidToken()
        ]);

        $this->assertResponseStatus(201);
        $this->assertEquals(9750, $payment_gateway->totalCharges());



        $order=$concert->orders()->where('email', 'john@gmail.com')->first();
        $this->assertNotNull($order);
        $this->assertEquals('3', $order->tickets->count());

    }
}
