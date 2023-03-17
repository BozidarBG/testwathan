<?php

namespace Tests\Unit;

use App\Billing\FakePaymentGateway;
use App\Billing\PaymentFailedException;
use PHPUnit\Framework\TestCase;

class FakePaymentGatewayTest extends TestCase
{

    public function test_charges_with_valid_payment_token_are_successfull()
    {
        $payment_gateway=new FakePaymentGateway();

        $payment_gateway->charge(2500, $payment_gateway->getValidToken());

        $this->assertEquals(2500, $payment_gateway->totalCharges());
    }

    //da očekujemo da bude exception
    protected function test_charges_with_invalid_payment_token_failed(){
        try{
            $payment_gateway=new FakePaymentGateway();

            $payment_gateway->charge(2500, 'pogrešan-tonek');

        }catch (PaymentFailedException $e){
            return;
        }

        $this->fail();
    }

    public function test_running_a_hook_before__the_first_charge(){
        $payment_gateway=new FakePaymentGateway();
        $timesCallbackRan=0;

        $payment_gateway->beforeFirstCharge(function($payment_gateway) use(&$timesCallbackRan){
            $payment_gateway->charge(2500, $payment_gateway->getValidToken());

            $timesCallbackRan ++;
            $this->assertEquals(2500, $payment_gateway->totalCharges());
        });


        $payment_gateway->charge(2500, $payment_gateway->getValidToken());
        $this->assertEquals(1, $timesCallbackRan);
        $this->assertEquals(5000, $payment_gateway->totalCharges());
    }
}
