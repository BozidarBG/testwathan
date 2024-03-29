<?php

namespace App\Http\Controllers;

use App\Billing\FakePaymentGateway;
use App\Billing\PaymentFailedException;
use App\Billing\PaymentGateway;
use App\Exceptions\NotEnoughTicketsException;
use App\Models\Concert;
use App\Models\Order;
use App\Reservation;
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

            $reservation=$concert->reserveTickets($request->ticket_quantity, $request->email);
            //create order
            $order=$reservation->complete($this->payment_gateway, $request->payment_token);

            return response()->json($order, 201);
        }catch(PaymentFailedException $e){
            $reservation->cancel();

            return response()->json([], 422);
        }catch(NotEnoughTicketsException $e){
            //dd(request()->all());
            return response()->json([], 422);
        }

    }
}
