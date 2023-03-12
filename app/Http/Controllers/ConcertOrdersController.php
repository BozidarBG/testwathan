<?php

namespace App\Http\Controllers;

use App\Billing\FakePaymentGateway;
use App\Billing\PaymentFailedException;
use App\Billing\PaymentGateway;
use App\Exceptions\NotEnoughTicketsException;
use App\Models\Concert;
use Illuminate\Http\Request;

class ConcertOrdersController extends Controller
{
    //

    private $payment_gateway;

    public function __construct(PaymentGateway $payment_gateway)
    {
        $this->payment_gateway=$payment_gateway;
    }

    public function store(Request $request, $concertId){
        //nećemo validaciju ako ne postoji ovaj koncert
        $concert=Concert::published()->findOrFail($concertId);

        $this->validate($request, [
            'email'=>'required|email',
            'ticket_quantity'=>'required|integer|min:1',
            'payment_token'=>'required'
        ]);

        try{
            //create order
            $order=$concert->orderTickets($request->email, $request->ticket_quantity);
            //charging customer
            $this->payment_gateway->charge($request->ticket_quantity * $concert->ticket_price, $request->payment_token);

            return response()->json([], 201);
        }catch(PaymentFailedException $e){
            $order->cancel();
            return response()->json([], 422);
        }catch(NotEnoughTicketsException $e){
            return response()->json([], 422);
        }

    }
}
