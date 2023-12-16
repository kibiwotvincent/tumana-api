<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class MpesaDeposit extends Model
{
    use HasFactory;
	protected $accessTokenUrl;
	protected $initiateSTKPushUrl;
	protected $callBackUrl;
	protected $consumerKey;
	protected $consumerSecret;
	protected $shortCode;
	protected $passkey;
    protected $timeoutUrl;
	
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'OriginatorConversationID',
        'ConversationID',
        'TransactionAmount',
        'ReceiverPhoneNumber',
        'Status'
    ];
    
	public function __construct() {
		$this->accessTokenUrl = "https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials";
		$this->callBackUrl = "https://tumana.ifam.co.ke/api/mpesa/process_callback";
        $this->timeoutUrl = "https://tumana.ifam.co.ke/api/mpesa/timed_out";
		$this->consumerKey = "RTTIOQAWLs2j4L3ZN3GVvfe7cH1cnTAc";
		$this->consumerSecret = "bJ63zwzXEFog1sZx";
		$this->shortCode = "600996";
	}
	
	public function getAccessToken() {
		$curl = curl_init($this->accessTokenUrl);
		curl_setopt($curl, CURLOPT_HTTPHEADER, ['Content-Type:application/json; charset=utf8']);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_HEADER, false);
		curl_setopt($curl, CURLOPT_USERPWD, $this->consumerKey.':'.$this->consumerSecret);
		$response = curl_exec($curl);
		
		$status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		curl_close($curl);
        
		if($status == 200) {
			$response = json_decode($response);
			return $response->access_token;
		}
		
		return null;
	}
	
	public function formatPhoneNumber($phoneNumber) {
		return '254'.substr($phoneNumber, 1);
	}
	
    public function sendMoney($phoneNumber, $amount, $reference) {
		$phoneNumber = $this->formatPhoneNumber($phoneNumber);
		
		//get access token
		$accessToken = $this->getAccessToken();
        
        $postData = [
                       "OriginatorConversationID" => $reference,
                       "InitiatorName" => "testapi",
                       "SecurityCredential" => "EsJocK7+NjqZPC3I3EO+TbvS+xVb9TymWwaKABoaZr/Z/n0UysSs..",
                       "CommandID" => "BusinessPayment",
                       "Amount" => $amount,
                       "PartyA" => $this->shortCode,
                       "PartyB" => $phoneNumber,
                       "Remarks" => "Money received from tumana",
                       "QueueTimeOutURL" => $this->timeoutUrl,
                       "ResultURL" => $this->callBackUrl,
                       "Occassion" => "Money received"
                   ];
		
        $url = "https://sandbox.safaricom.co.ke/mpesa/b2c/v3/paymentrequest";
        
        $response = Http::acceptJson()
                    ->withToken($accessToken)
                    ->withHeaders(['Content-Type' => "application/json"])
                    ->post($url, $postData);
        
        return $response->json();
	}
    
    public function acknowledgeDeposit($deposit) {
        $mpesaDeposit = new MpesaDeposit;
        $mpesaDeposit->OriginatorConversationID = $deposit['OriginatorConversationID'];
        $mpesaDeposit->ConversationID = $deposit['ConversationID'];
        $mpesaDeposit->TransactionAmount = $deposit['TransactionAmount'];
        $mpesaDeposit->ReceiverPhoneNumber = $deposit['ReceiverPhoneNumber'];
        $mpesaDeposit->Status = 'pending';
        $mpesaDeposit->save();
    }
    
    public function confirmDeposit($originatorConversationID, $data) {
        //confirm if deposit had been initiated
        $deposit = MpesaDeposit::where('OriginatorConversationID', $originatorConversationID)->first();
        
        if($deposit->id > 0) {
            //get deposit data
            $deposit->TransactionReceipt = $data['TransactionReceipt'];
            $deposit->ReceiverName = trim(explode('-', $data['ReceiverPartyPublicName'])[1]);
            $deposit->TransactionCompletedDateTime = $data['TransactionCompletedDateTime'];
            $deposit->Status = 'completed';
            return $deposit->save();
        }
        
        return false;
    }
 
}
