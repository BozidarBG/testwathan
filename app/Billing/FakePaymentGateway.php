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
    private $beforeFirstChargeCallback;


    public function __construct()
    {
        $this->charges=collect();
    }

    public function getValidToken(){
        return 'valid-token';
    }

    public function charge($amount, $token){

        if($this->beforeFirstChargeCallback !==null){
            $callback=$this->beforeFirstChargeCallback;
            $this->beforeFirstChargeCallback=null;
            $callback($this);
        }

        if($token !== $this->getValidToken()){
            throw new PaymentFailedException;
        }
        $this->charges[]=$amount;
    }

    public function totalCharges(){
        return $this->charges->sum();
    }

    public function beforeFirstCharge($callback){
        $this->beforeFirstChargeCallback=$callback;

    }
}
