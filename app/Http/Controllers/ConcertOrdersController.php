<?php

namespace App\Http\Controllers;

use App\Billing\FakePaymentGateway;
use App\Billing\PaymentGateway;
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

        $concert=Concert::find($concertId);

        //charging customer
        $this->payment_gateway->charge($request->ticket_quantity * $concert->ticket_price, $request->token);


        //create order
        $order=$concert->orderTickets($request->email, $request->ticket_quantity);
        return response()->json([], 201);
    }
}
