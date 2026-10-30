<?php

namespace App\Http\Controllers;

use App\Services\Paystack\Plan;
use App\Services\Paystack\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function subscribe(Request $request)
    {
        $reference = $request->input('reference');
        $volume = $request->input('volume');
        $issue = $request->input('issue');

        if (!$reference)
            abort(401);

        $transaction = (new Transaction())->verify($reference);
        if (!$transaction)
            abort(401, 'failed to verify');

        $amount = $transaction->amount; // get amount paid (in kobo)
        // todo: FURTHER VALIDATION OF AMOUNT
        if(
            !(($amount == issue_price() && $issue != 0)
                || ($amount == volume_price_multi() &&  Auth()->user()->corporate)
                || $amount == volume_price())
                                                ){
            return response()->json(['error' => 'Invalid Payment'], 401);
        }

        $request->user()->subscriptions()->create([
            'name' => 'none', // todo
            'paystack_id' => $transaction->id,
            'amount' => $amount,
            'volume' => $volume,
            'issue' => $issue
        ]);

        // Update User Card
        $request->user()->update([
            'paystack_id' => $transaction->customer->customer_code,
            'card' => $transaction->authorization
        ]);

        return 'OK';
    }

    public function download($id)
    {
        return $id;
    }
}
