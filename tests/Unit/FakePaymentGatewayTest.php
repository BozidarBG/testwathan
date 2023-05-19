<?php

namespace Tests\Unit;

use App\Billing\FakePaymentGateway;
use App\Billing\PaymentFailedException;
use PHPUnit\Framework\TestCase;

class FakePaymentGatewayTest extends TestCase
{

    protected function getPaymentGateway(){
        return new FakePaymentGateway();
    }

    public function test_charges_with_valid_payment_token_are_successful()
    {
        $payment_gateway=$this->getPaymentGateway();

        $payment_gateway->charge(2500, $payment_gateway->getValidTestToken());

        $this->assertEquals(2500, $payment_gateway->totalCharges());
    }

    //da očekujemo da bude exception
    public function atest_charges_with_invalid_payment_token_failed(){
        try{
            $payment_gateway=$this->getPaymentGateway();

            $payment_gateway->charge(2500, 'pogrešan-tonek');

        }catch (PaymentFailedException $e){
            //$this->assertCount(0, $this->newCharges());
            return;
        }

        $this->fail();
    }

    public function test_running_a_hook_before__the_first_charge(){
        $payment_gateway=$this->getPaymentGateway();
        $timesCallbackRan=0;

        $payment_gateway->beforeFirstCharge(function($payment_gateway) use(&$timesCallbackRan){
            $payment_gateway->charge(2500, $payment_gateway->getValidTestToken());

            $timesCallbackRan ++;
            $this->assertEquals(2500, $payment_gateway->totalCharges());
        });


        $payment_gateway->charge(2500, $payment_gateway->getValidTestToken());
        $this->assertEquals(1, $timesCallbackRan);
        $this->assertEquals(5000, $payment_gateway->totalCharges());
    }
}
