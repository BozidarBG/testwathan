<?php

namespace App\Billing;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Stripe\Charge;
use Stripe\Exception\InvalidRequestException;

class StripePaymentGateway implements PaymentGateway
{
    //use HasFactory;
    private $api_key;

    public function __construct($api_key){
        $this->api_key=$api_key;
    }



    public function charge($amount, $token){
        try{
            Charge::create([
                'amount'=>$amount,
                'source'=>$token,
                'currency'=>'usd',
            ], ['api_key'=>$this->api_key]);
        }catch(InvalidRequestException $e){
            //throw new PaymentFailedException();
            return false;
        }

    }

    public function getValidTestToken(){
        return \Stripe\Token::create([
            'card'=>[
                'number'=>"4242424242424242",
                'exp_month'=>1,
                'exp_year'=>date('Y') + 1,
                'cvc'=>'321'
            ]
        ],
            ['api_key'=>$this->api_key]
        )->id;
    }

    public function newChargesDuring($callback){
        $latestCharge=$this->lastCharge();
        $callback($this);
        return $this->newChargesSince($latestCharge)->pluck('amount');
    }

    private function lastCharge(){
        return \Stripe\Charge::all(
            ['limit'=>1],
            ['api_key'=>$this->api_key]
        )['data'][0];
    }

    private function newChargesSince($charge=null){
        $new_charges= \Stripe\Charge::all(
            [
                'ending_before'=>$charge ? $charge->id : null
            ],
            ['api_key'=>$this->api_key]
        )['data'];

        return collect($new_charges);
    }
}
