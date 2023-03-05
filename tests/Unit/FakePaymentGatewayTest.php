<?php

namespace Tests\Unit;

use App\Billing\FakePaymentGateway;
use PHPUnit\Framework\TestCase;

class FakePaymentGatewayTest extends TestCase
{

    public function test_charges_with_valid_payment_token_are_successfull()
    {
        $payment_gateway=new FakePaymentGateway();

        $payment_gateway->charge(2500, $payment_gateway->getValidToken());

        $this->assertEquals(2500, $payment_gateway->totalCharges());
    }
}
