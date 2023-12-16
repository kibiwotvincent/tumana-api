<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\Models\User;
use App\Models\MpesaDeposit;
use App\Http\Requests\MpesaDepositRequest;
use Illuminate\Support\Facades\Response;
use App\Events\MpesaDepositCompleted;
use Log;
use Carbon\Carbon;

class MpesaDepositController extends Controller
{	
	/**
     * Handle callback from mpesa server.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return Str
     *
     */
    public function processCallback(Request $request)
    {
        $data = $request->all();
        
        Log::info("Callback data.....");
        Log::debug($data);
        
        if(isset($data['Result']) && $data['ResultCode'] == 0) {
            $mpesaDeposit = new MpesaDeposit();
		    if($mpesaDeposit->confirmDeposit($originatorConversationID, $data['Result']['ResultParameters']['ResultParameter'])) {
                //fire mpesa deposit completed event
                event(new MpesaDepositCompleted(MpesaDeposit::where('OriginatorConversationID', $originatorConversationID)->first()));
            } 
        }
        else {
            //log error
            Log::debug($data);
        }
	}
    
    /**
     * Handle an incoming mpesa deposit request.
     *
     * @param  \App\Http\Requests\MpesaDepositRequest  $request
     * @return \Illuminate\Support\Facades\Response
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function send(Request $request)
    {
        $amount = 51;
        $phoneNumber = '0706038461';
		$reference = Carbon::now()->format('YmdHis');
		//initiate send money request
		$mpesaDeposit = new MpesaDeposit();
		$response = $mpesaDeposit->sendMoney($phoneNumber, $amount, $reference);
		if(isset($response['errorCode'])) {
            //log error
			return Response::json(['message' => "Failed to send money. Try again later."], 401);
		}
		else {
            $deposit['TransactionAmount'] = $amount;
            $deposit['ReceiverPhoneNumber'] = $phoneNumber;
            $deposit['OriginatorConversationID'] = $response['OriginatorConversationID'];
            $deposit['ConversationID'] = $response['ConversationID'];
            
            $mpesaDeposit->acknowledgeDeposit($deposit);
			return Response::json(['message' => "Payment has been send to ".$phoneNumber."."], 200);
		}
	}
    
    /**
     * Handle timeout callback from mpesa server.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return Str
     *
     */
    public function processCallback(Request $request)
    {
        $data = $request->all();
        Log::info("timeout");
        Log::debug($data);
    }
}
