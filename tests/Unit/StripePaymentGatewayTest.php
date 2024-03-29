<?php

namespace Tests\Unit;

use App\Billing\FakePaymentGateway;
use App\Billing\PaymentFailedException;
use App\Billing\StripePaymentGateway;
use Carbon\Carbon;
use Tests\TestCase;

class StripePaymentGatewayTest extends TestCase
{
    private $lastCharge;

    public function setUp(): void
    {
        parent::setUp(); // TODO: Change the autogenerated stub
        $this->lastCharge=$this->lastCharge();
    }

    private function lastCharge(){
        return \Stripe\Charge::all(
            ['limit'=>1],
            ['api_key'=>config('services.stripe.secret')]
        )['data'][0];
    }

    private function newCharges(){
        return \Stripe\Charge::all(
            [
                'limit'=>1,
                'ending_before'=>$this->lastCharge->id
            ],
            ['api_key'=>config('services.stripe.secret')]
        )['data'];
    }

    private function validToken(){
        return \Stripe\Token::create([
            'card'=>[
                'number'=>"4242424242424242",
                'exp_month'=>1,
                'exp_year'=>date('Y') + 1,
                'cvc'=>'321'
            ]
        ],
            ['api_key'=>config('services.stripe.secret')]
        )->id;
    }

    protected function getPaymentGateway(){
        return new StripePaymentGateway(config('services.stripe.secret'));
    }

    public function test_charges_with_valid_payment_token_are_successful()
    {
       $payment_gateway=$this->getPaymentGateway();

       $newCharges=$payment_gateway->newChargesDuring(function ($payment_gateway){
           $payment_gateway->charge(2500, $payment_gateway->getValidTestToken());
       });

        $this->assertCount(1,$newCharges);
        $this->assertEquals(2500, $newCharges->sum());
    }

    public function test_charges_with_invalid_payment_token_failed(){

        $payment_gateway=new StripePaymentGateway(config('services.stripe.secret'));

        $result=$payment_gateway->charge(2500, 'pogrešan-tonek');
        $this->assertFalse($result);
    }
}
