<?php
/**
 * Created by PhpStorm.
 * User: mitrovic
 * Date: 3.3.23.
 * Time: 12.58
 */

namespace App\Billing;


class FakePaymentGateway implements PaymentGateway
{

    private $charges;


    public function __construct()
    {
        $this->charges=collect();
    }

    public function getValidToken(){
        return 'valid-token';
    }

    public function charge($amount, $token){
        if($token !== $this->getValidToken()){
            throw new PaymentFailedException;
        }
        $this->charges[]=$amount;
    }

    public function totalCharges(){
        return $this->charges->sum();
    }
}
