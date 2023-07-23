<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class DlrPUshSecondJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $data = [];

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data;
        if ($this->data['multisms'] == 'no') {
            $this->singleSms();
        }
        if ($this->data['multisms'] == 'yes') {
            $this->multiSms();
        }
    }


    public function singleSms() {
        sleep(7);
        if ($this->data['errorflug'] == 'success') {
            $client = new Client([
                'verify' => false,
                'headers' => [ 'Content-Type' => 'application/json' ]
            ]);

            

            $clientresponse = $client->request('POST','https://httpsmsc.montymobile.com/HTTP/api/Vendor/DLRListener',[
                'body' => json_encode([ 
                    'username' => '9611748847',//$camid,//$resarr['MessageId'],
                    'password' => 'Monty$$$3737',
                    'sender' => $this->data['sender'],
                    'destination' => $this->data['destination'],
                    'messageId' => $this->data['messageId'],
                    'deliveryStatus' => 2,
                    //'dateReceived' => $drldate->addSecond(2),
                    'description' => 'Delivered',
                ])
            ]);

            Log::channel('smslog')->info('3rd response: '. $clientresponse->getBody(). ' -- Time: '. date('y-m-d h:i:s'));
        } else {
            $client = new Client([
                'verify' => false,
                'headers' => [ 'Content-Type' => 'application/json' ]
            ]);

            

            $clientresponse = $client->request('POST','https://httpsmsc.montymobile.com/HTTP/api/Vendor/DLRListener',[
                'body' => json_encode([ 
                    'username' => '9611748847',//$camid,//$resarr['MessageId'],
                    'password' => 'Monty$$$3737',
                    'sender' => $this->data['sender'],
                    'destination' => $this->data['destination'],
                    'messageId' => $this->data['messageId'],
                    'deliveryStatus' => 5,
                    //'dateReceived' => $drldate->addSecond(2),
                    'description' => 'Undelivered',
                ])
            ]);

            Log::channel('smslog')->info('3rd response: '. $clientresponse->getBody(). ' -- Time: '. date('y-m-d h:i:s'));
        }
    }


    public function multiSms() {
        sleep(7);
        if ($this->data['errorflug'] == 'success') {
            $client = new Client([
                'verify' => false,
                'headers' => [ 'Content-Type' => 'application/json' ]
            ]);

            
            foreach($this->data['destination'] as $record) {
                $clientresponse = $client->request('POST','https://httpsmsc.montymobile.com/HTTP/api/Vendor/DLRListener',[
                    'body' => json_encode([ 
                        'username' => '9611748847',//$camid,//$resarr['MessageId'],
                        'password' => 'Monty$$$3737',
                        'sender' => $this->data['sender'],
                        'destination' => $record,
                        'messageId' => $this->data['messageId'],
                        'deliveryStatus' => 2,
                        //'dateReceived' => $drldate->addSecond(2),
                        'description' => 'Delivered',
                    ])
                ]);

                Log::channel('smslog')->info('3rd response: '. $clientresponse->getBody(). ' -- Time: '. date('y-m-d h:i:s'));
            }
        } else {
            $client = new Client([
                'verify' => false,
                'headers' => [ 'Content-Type' => 'application/json' ]
            ]);

            

            foreach($this->data['destination'] as $record) {
                $clientresponse = $client->request('POST','https://httpsmsc.montymobile.com/HTTP/api/Vendor/DLRListener',[
                    'body' => json_encode([ 
                        'username' => '9611748847',//$camid,//$resarr['MessageId'],
                        'password' => 'Monty$$$3737',
                        'sender' => $this->data['sender'],
                        'destination' => $record,
                        'messageId' => $this->data['messageId'],
                        'deliveryStatus' => 5,
                        //'dateReceived' => $drldate->addSecond(2),
                        'description' => 'Undelivered',
                    ])
                ]);

                Log::channel('smslog')->info('3rd response: '. $clientresponse->getBody(). ' -- Time: '. date('y-m-d h:i:s'));
            }
        }
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $drldate = \Carbon\Carbon::now();
        // $client = new Client([
        //     'verify' => false,
        //     'headers' => [ 'Content-Type' => 'application/json' ]
        // ]);

        //Log::channel('smslog')->info('2nd response:  '. date('y-m-d h:i:s'));
        
        if ($this->data['multisms'] == 'no') {
            $this->singleSms();
        }
        if ($this->data['multisms'] == 'yes') {
            $this->multiSms();
        }
    }
}
