<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Classes\JengaAPI;
use App\Events\EquityTransactionCompleted;
use Illuminate\Support\Facades\Http;

class EquityTransaction extends Model
{
    use HasFactory;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'reference_id',
        'transaction_id',
        'status',
    ];
    
    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'transaction_time' => 'datetime',
    ];
	
    
    public static function sendMoney($amount, $receiver, $reference)
    {
        //check credit status of the parent transaction first
        //check equity transactions if there is a pending transaction of the same parent transaction
        
        $url = config(key: 'jenga.host') . "/v3-apis/transaction-api/v3.0/remittance/sendmobile";
        $apiKey = config(key: 'jenga.key');
        $merchantCode = config(key: 'jenga.merchant');
        $consumerSecret = config(key: 'jenga.secret');
        $token = JengaAPI::getToken();
        $baseUrl = config(key: 'jenga.host');
        
        $amount = floor($amount);
        $receiver = '254'.substr($receiver, 1);
        $receiverName = "Vincent Kibiwot";
        $description = "You have received Kshs ".$amount;
        $date = Date('Y-m-d');
        $currencyCode = "KES";
        $accountNumber = config(key: 'jenga.account');
        $reference = Date('Y').time();
        
        $params = ['amount' => $amount, 'currencyCode' => $currencyCode, 'reference' => $reference, 'accountNumber' => $accountNumber];
        $signature = JengaAPI::getSignature($params);
        
        $response = Http::acceptJson()
                    ->withToken(token: $token)
                    ->withHeaders(headers: ['Signature' => $signature])
                    ->post(url: $url, data: [
                        "source" => [
                            "countryCode" => "KE",
                            "name" => "Vincent Kibiwott Chumba",
                            "accountNumber" => $accountNumber
                        ],
                        "destination" => [
                            "type" => "mobile",
                            "countryCode" => "KE",
                            "name" => $receiverName,
                            "mobileNumber" => $receiver,
                            "walletName" => "Mpesa"
                        ],
                        "transfer" => [
                            "type" => "MobileWallet",
                            "amount" => $amount,
                            "currencyCode" => $currencyCode,
                            "reference" => $reference,
                            "date" => $date,
                            "description" => $description,
                            "callbackUrl" => config('app.url')."/api/equity/callback"
                        ]
                    ]);
        
        return $response->json();
    }
    
    public static function acknowledgeTransaction($parentTransactionReferenceID, $transaction) {
        EquityTransaction::create([
                                    'parent_txn_reference_id' => $parentTransactionReferenceID,
                                    'equity_reference_id' => $transaction['reference'],
                                    'transaction_id' => $transaction['transactionId'],
                                    'status' => 'pending'
                                    ]);
    }
    
    public static function confirmTransaction($txnData) {
        $transaction = EquityTransaction::where('equity_reference_id', $txnData['Reference'])->first();
        
        if($transaction->id > 0) {
            $transaction->third_party_txn_id = $data['ThirdPartyTranID'];
            $transaction->receiver_name = $data['ReceiverName'];
            $transaction->receiver_number = $data['ReceiverMsisdn'];
            $transaction->description = $data['ResponseDescription'];
            $transaction->status = 'completed';
            $transaction->transaction_time = Date('Y-m-d H:i:s');
            $transaction->save();
            
            event(new EquityTransactionCompleted($transaction));
        }
    }
}
