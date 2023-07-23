@extends('layouts.smsapp')

@section('appbody')
    <!-- content -->
    <div id="content" class="app-content" role="main">

  	<div class="app-content-body ">

        
<div class="bg-light lter b-b wrapper-md">

	<div class="col-md-12">
		<h1 class="m-n font-thin h3">API Documentation    <span class="pull-right" style="margin-right:10px; color:green;">@if(session()->has('apitokenmsg')) <strong>{{session()->get('apitokenmsg')}} @endif</strong></span></h1> 
	</div>
	<br> <br> 
	<div class="well bg-white">
		Your API Key : <strong id="key_show_id"> {{ $api_token }}  </strong>  &nbsp; &nbsp; 
		<a href="{{route('generate-apitoken',[Auth::guard('web')->user()])}}" class="btn btn-sm btn-danger pull-right">Generate New <i class="fa fa-plus"></i></a>
	</div> 
	<div class="well bg-white">
        <ul>
        	<li style="display: inline-block; margin-right:20px; font-weight:900;">
        		Approved Senderid List:
        	</li>
        	@foreach($senderIds as $senderId)
        		<li style='display:inline-block; margin-right:10px;'>
        			{{ $senderId->getSenderid->name }}
        		</li>
        	@endforeach
        </ul>
	</div> 
	
    
    
    <div class="panel panel-default" style="padding:20px;">

    	<div class="well" style="margin: 20px;">
            <h4 class="modal-title"> <label>API For Sending SMS</label> </h4>
			<strong>API URL: </strong>{{ config('apiconfig.api_url') }}/sms-api/sendsms?api_token=(Your API Key)&senderid=(Approved Sender Id)&message=(Message Content)&contact_number=(Contact Number)
			<br>
			<strong>API Key: </strong><?php echo auth()->user()->api_token; ?>
			<br>
			<br>
			<strong>NOTE:</strong> API supports both GET and POST requests.
        </div>
		<div class="portlet-body flip-scroll">
			<table class="table table-bordered table-striped table-condensed flip-content">
				<thead class="flip-content">
					<tr>
						<th> Request Parameter Name </th>
						<th> Meaning/Value </th>
						<th> Description </th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td> api_token </td>
						<td> API Token/Key </td>
						<td> Your API Token: <strong id="key_id_ref"><?php echo auth()->user()->api_token; ?></strong> </td>
					</tr>
					<tr>
						<td> senderid </td>
						<td> Approved senderid </td>
						<td> Senderid assigned to you</td>
					</tr>
					<tr>
						<td> contact_number </td>
						<td> Mobile number </td>
						<td> Exp: Comma or space separated 88017XXXXXXXX,88018XXXXXXXX,88019XXXXXXXX... (start with 880 or 01x) </td>
					</tr>
					<tr>
						<td> message </td>
						<td> sms content </td>
						<td> Exp: anything you can write into message content. </td>
					</tr>
				</tbody>
			</table>

	    	<div id="panel-heading font-bold" style="padding:10px; font-weight:900;">Response If Success: (Response Format: JSON)</div>
	    	<table class="table table-bordered table-striped table-condensed flip-content">
				<thead class="flip-content">
					<tr>
						<th> Response Parameter Name </th>
						<th> Meaning/Value </th>
						<th> Desired Value - Description</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td> status </td>
						<td> Status of the API request </td>
						<td> <b>success</b> - means the request was successfully executed</td>
					</tr>
					<tr>
						<td> message </td>
						<td> Response message </td>
						<td> <b>SMS sent successfully</b> - message about the request</td>
					</tr>
					<tr>
						<td> smsid </td>
						<td> ID of the sms </td>
						<td> Exp: <b>100000-000000001</b> - sms ID <span class="text-danger">which will be required to fetch delivery status of the sms</span>. (** Applicable for single number request only)</td>
					</tr>
					<tr>
						<td> SmsCount </td>
						<td> Number of sms by length </td>
						<td> Exp: <b>1</b> - number of sms by the length of the sms. (** Applicable for single number request only)</td>
					</tr>
				</tbody>
			</table>

			<div id="panel-heading font-bold" style="padding:10px; font-weight:900;">Response If Failed: (Response Format: JSON)</div>
	    	<table class="table table-bordered table-striped table-condensed flip-content">
				<thead class="flip-content">
					<tr>
						<th> Response Parameter Name </th>
						<th> Meaning/Value </th>
						<th> Desired Value - Description</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td> status </td>
						<td> Status of the API request </td>
						<td> <b>error</b> - means the request was failed to sent the sms</td>
					</tr>
					<tr>
						<td> message </td>
						<td> Response message </td>
						<td> Exp: <b>Insufficient mask sms balance</b> - details about the failure</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>

	<div class="panel with-nav-tabs panel-default" style="padding:20px;">
		<div id="panel-heading font-bold" style="padding:10px; font-weight:900;">PHP code example For Sending SMS</div>
        <div class="panel-heading">
                <ul class="nav nav-tabs">
                    <li class="active"><a href="#tab1API1" data-toggle="tab">GET request</a></li> 
                    <li><a href="#tab1API2" data-toggle="tab">POST request</a></li>
                    <li><a href="#tab1API3" data-toggle="tab">Request using (Guzzle)</a></li> 
                </ul>
        </div>
        <div class="panel-body">
            <div class="tab-content">
                <div class="tab-pane fade in active" id="tab1API1">
                    <div class="box box-danger">
                       <div class="alert alert-info bg-white">
							<strong>API URL (HTTP(s) GET))</strong> : {{ config('apiconfig.api_url') }}/sms-api/sendsms?api_token=(APITOKEN)&senderid=(Approved Sender Id)&message=(Message Content)&contact_number=(Contact Number)
                      </div>
                    </div>
                    <div class="box box-danger">
                       <div class="alert alert-info bg-white">
        
        <pre style="background:#FFF">&lt;?php
    $post_url = "{{ config('apiconfig.api_url') }}/sms-api/sendsms" ;  
      
    $post_values = array( 
    'api_token' =&gt; 'API KEY',
    'senderid' =&gt; 'SENDER ID',
    'message' =&gt; 'Hello world',
    'contact_number' =&gt; 'MOBILE NUMBER',
    );
    
    $post_string = "";
    foreach( $post_values as $key =&gt; $value )
        { $post_string .= "$key=" . urlencode( $value ) . "&amp;"; }
       $post_string = rtrim( $post_string, "&amp; " );
      
    $request = curl_init($post_url);
        curl_setopt($request, CURLOPT_HEADER, 0);
        curl_setopt($request, CURLOPT_RETURNTRANSFER, 1);  
        curl_setopt($request, CURLOPT_POSTFIELDS, $post_string); 
        curl_setopt($request, CURLOPT_SSL_VERIFYPEER, FALSE);  
        $post_response = curl_exec($request);  
       curl_close ($request);  

    $array =  json_decode( preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $post_response), true );   
     
    if($array){
    	//status of the request
    	echo $array['status'] ;

    	//status message of the request
    	echo $array['message'] ;
    }

    /*
    *   You can request for single sms to multiple numbers through this api also.
    *   In this case you have to seperate numbers with comma(,) or space like-
    *   017XXXXXXXX,018XXXXXXXX,019XXXXXXXX
    *   or
    *   017XXXXXXXX 018XXXXXXXX 019XXXXXXXX
    *   As:
    *   'contact_number' =&gt; '017XXXXXXXX,018XXXXXXXX,019XXXXXXXX'
    *   or
    *   'contact_number' =&gt; '017XXXXXXXX 018XXXXXXXX 019XXXXXXXX'
    *
    *
    *   For multiple numbers request you will receive two additional data in return if your request is successful.
    *   Then the response will be as:
    */

    if($array){
    	//status of the request
    	echo $array['status'] ;

    	//status message of the request
    	echo $array['message'] ;

    	//number of successfully sent contacts
    	echo $array['success'] ;

    	//number of successfully sent contacts
    	echo $array['failed'] ;
    }
                 
?&gt;
		</pre>
			          </div>
			        </div>
			    </div>

                <div class="tab-pane fade in" id="tab1API2">
                    <div class="box box-danger">
                       <div class="alert alert-info bg-white">
        
        <pre style="background:#FFF">&lt;?php
    $post_url = "{{ config('apiconfig.api_url') }}/sms-api/sendsms" ;  
      
    $post_values = array( 
    'api_token' =&gt; 'API KEY',
    'senderid' =&gt; 'SENDER ID',
    'contact_number' =&gt; 'MOBILE NUMBER',
    'message' =&gt; 'Hello world',
    );
    
    $post_string = "";
    foreach( $post_values as $key =&gt; $value )
        { $post_string .= "$key=" . urlencode( $value ) . "&amp;"; }
       $post_string = rtrim( $post_string, "&amp; " );

    $request = curl_init($post_url);
        curl_setopt($request, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($request, CURLOPT_RETURNTRANSFER, 1);  
        curl_setopt($request, CURLOPT_POSTFIELDS, $post_string); 
        curl_setopt($request, CURLOPT_SSL_VERIFYPEER, FALSE);  
        $post_response = curl_exec($request);  
       curl_close ($request);  
      		
    $array =  json_decode( preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $post_response), true );   
     
    if($array){
    	//status of the request
    	echo $array['status'] ;

    	//status message of the request
    	echo $array['message'] ;
    }

    /*
    *   You can request for single sms to multiple numbers through this api also.
    *   In this case you have to seperate numbers with comma(,) or space like-
    *   017XXXXXXXX,018XXXXXXXX,019XXXXXXXX
    *   or
    *   017XXXXXXXX 018XXXXXXXX 019XXXXXXXX
    *   As:
    *   'contact_number' =&gt; '017XXXXXXXX,018XXXXXXXX,019XXXXXXXX'
    *   or
    *   'contact_number' =&gt; '017XXXXXXXX 018XXXXXXXX 019XXXXXXXX'
    *
    *
    *   For multiple numbers request you will receive two additional data in return if your request is successful.
    *   Then the response will be as:
    */

    if($array){
    	//status of the request
    	echo $array['status'] ;

    	//status message of the request
    	echo $array['message'] ;

    	//number of successfully sent contacts
    	echo $array['success'] ;

    	//number of successfully sent contacts
    	echo $array['failed'] ;
    }
    
                 
?&gt;
        </pre>
                          </div>
                        </div>
                    </div>
                        
	                <div class="tab-pane fade in" id="tab1API3">
	                    <div class="box box-danger">
	                        <div class="alert alert-success">
	                                <strong>SEND SMS API Guzzle (GET Method)</strong> : 
	    <pre style="background:#FFF">&lt;?php
   $client = new Client();

    $response = $client->request('GET',"{{ config('apiconfig.api_url') }}/sms-api/sendsms",[
        'query' => [ 
        'api_token' =&gt; 'API KEY',
        'senderid' =&gt; 'SENDER ID',
        'contact_number' =&gt; 'MOBILE NUMBER',
        'message' =&gt; 'Hello world'
    ]);

    $returnedData = json_decode($response->getBody());

    //status of the request
    $status = $returnedData->status;

    //status message of the request
    $status = $returnedData->message;

    /*
    *   You can request for single sms to multiple numbers through this api also.
    *   In this case you have to seperate numbers with comma(,) or space like-
    *   017XXXXXXXX,018XXXXXXXX,019XXXXXXXX
    *   or
    *   017XXXXXXXX 018XXXXXXXX 019XXXXXXXX
    *   As:
    *   'contact_number' =&gt; '017XXXXXXXX,018XXXXXXXX,019XXXXXXXX'
    *   or
    *   'contact_number' =&gt; '017XXXXXXXX 018XXXXXXXX 019XXXXXXXX'
    *
    *
    *   For multiple numbers request you will receive two additional data in return if your request is successful.
    *   Then the response will be as:
    */
    
    //number of successfully sent contacts
    $success = $returnedData->success;
    
    //number of failed contacts
    $failed = $returnedData->failed;

?&gt;
        </pre>
                          		</div>

                                <div class="alert alert-success">
                                    <strong>SEND SMS API Guzzle (POST Method)</strong> : 
        <pre style="background:#FFF">&lt;?php
   $client = new Client();

    $response = $client->request('POST',"{{ config('apiconfig.api_url') }}/sms-api/sendsms",[
        'form_params' => [ 
	        'api_token' =&gt; 'API KEY',
	        'senderid' =&gt; 'SENDER ID',
	        'contact_number' =&gt; 'MOBILE NUMBER',
	        'message' =&gt; 'Hello world'
	    ]);

    $returnedData = = json_decode($response->getBody());

    //status of the request
    $status = $returnedData->status;

    //status message of the request
    $status = $returnedData->message;

    /*
    *   You can request for single sms to multiple numbers through this api also.
    *   In this case you have to seperate numbers with comma(,) or space like-
    *   017XXXXXXXX,018XXXXXXXX,019XXXXXXXX
    *   or
    *   017XXXXXXXX 018XXXXXXXX 019XXXXXXXX
    *   As:
    *   'contact_number' =&gt; '017XXXXXXXX,018XXXXXXXX,019XXXXXXXX'
    *   or
    *   'contact_number' =&gt; '017XXXXXXXX 018XXXXXXXX 019XXXXXXXX'
    *
    *
    *   For multiple numbers request you will receive two additional data in return if your request is successful.
    *   Then the response will be as:
    */
    
    //number of successfully sent contacts
    $success = $returnedData->success;
    
    //number of failed contacts
    $failed = $returnedData->failed;


                 
?&gt;


        </pre>
                        </div>
                    </div>
                </div>    
            </div>
        </div>
    </div>
</div>

<!-- Main content -->
<div class="wrapper-md">
  	<div class="panel panel-default" style="padding:20px;">

		<div class="well" style="margin: 20px;">
            <h4 class="modal-title"> <label> API For Checking Balance</label> </h4>
			<strong>API URL: </strong>{{ config('apiconfig.api_url') }}/sms-api/balance?api_token=(Your API Key)
			<br>
			<strong>API Key: </strong><?php echo auth()->user()->api_token; ?>
			<br>
			<br>
			<strong>NOTE:</strong> API supports both GET and POST requests.
        </div>
        
        <div class="portlet-body flip-scroll">
			<table class="table table-bordered table-striped table-condensed flip-content">
				<thead class="flip-content">
					<tr>
						<th> Request Parameter Name </th>
						<th> Meaning/Value </th>
						<th> Description </th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td> api_token </td>
						<td> API Token or Key </td>
						<td> Your API Token <strong id="key_id_ref"><?php echo auth()->user()->api_token; ?></strong> </td>
					</tr>
					
				</tbody>
			</table>

			<div id="panel-heading font-bold" style="padding:10px; font-weight:900;">Response If Success: (Response Format: JSON)</div>
	    	<table class="table table-bordered table-striped table-condensed flip-content">
				<thead class="flip-content">
					<tr>
						<th> Response Parameter Name </th>
						<th> Meaning/Value </th>
						<th> Desired Value - Description</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td> status </td>
						<td> Status of the API request </td>
						<td> <b>success</b> - means the request was successfully executed</td>
					</tr>
					<tr>
						<td> mask </td>
						<td> Masking SMS balance </td>
						<td> Exp: <b>1000</b> - sms balance -> number of sms available.</td>
					</tr>
					<tr>
						<td> nonmask </td>
						<td> Non-masking SMS balance </td>
						<td> Exp: <b>1000</b> - sms balance -> number of sms available.</td>
					</tr>
					<tr>
						<td> voice </td>
						<td> Voice SMS balance </td>
						<td> Exp: <b>1000</b> - sms balance -> number of sms available.</td>
					</tr>
				</tbody>
			</table>

			<div id="panel-heading font-bold" style="padding:10px; font-weight:900;">Response If Failed: (Response Format: JSON)</div>
	    	<table class="table table-bordered table-striped table-condensed flip-content">
				<thead class="flip-content">
					<tr>
						<th> Response Parameter Name </th>
						<th> Meaning/Value </th>
						<th> Desired Value - Description</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td> status </td>
						<td> Status of the API request </td>
						<td> <b>error</b> - means the request was failed to execute</td>
					</tr>
					<tr>
						<td> message </td>
						<td> Response message </td>
						<td> Exp: <b>Invalid Request</b> - details about the failure</td>
					</tr>
				</tbody>
			</table>
		</div>			
    </div>
</div>



<!-- Main content -->
<div class="wrapper-md">
  	<div class="panel panel-default" style="padding:20px;">

		<div class="well" style="margin: 20px;">
            <h4 class="modal-title"> <label> API For Checking SMS Delivery Status</label> </h4>
			<strong>API URL: </strong>{{ config('apiconfig.api_url') }}/sms-api/delivery-report?api_token=(Your API Key)&smsId=(SMS ID <span class="text-danger">received while sending the sms</span>)
			<br>
			<strong>API Key: </strong><?php echo auth()->user()->api_token; ?>
			<br>
			<br>
			<strong>NOTE:</strong> <b>Delivery reports are generally updated after 10 minutes of sending the sms. You should call the this API after 15 to 20 minutes of sending the sms.</b> API supports both GET and POST requests.
        </div>
        
        <div class="portlet-body flip-scroll">
			<table class="table table-bordered table-striped table-condensed flip-content">
				<thead class="flip-content">
					<tr>
						<th> Request Parameter Name </th>
						<th> Meaning/Value </th>
						<th> Description </th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td> api_token </td>
						<td> API Token or Key </td>
						<td> Your API Token <strong id="key_id_ref"><?php echo auth()->user()->api_token; ?></strong> </td>
					</tr>

					<tr>
						<td> smsId </td>
						<td> ID of the SMS </td>
						<td> SMS ID <span class="text-danger">which was received in response of the sms sending API</span></td>
					</tr>
					
				</tbody>
			</table>

			<div id="panel-heading font-bold" style="padding:10px; font-weight:900;">Response If Success: (Response Format: JSON)</div>
	    	<table class="table table-bordered table-striped table-condensed flip-content">
				<thead class="flip-content">
					<tr>
						<th> Response Parameter Name </th>
						<th> Meaning/Value </th>
						<th> Desired Value - Description</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td> status </td>
						<td> Status of the API request </td>
						<td> <b>success</b> - means the request was successfully executed</td>
					</tr>
					<tr>
						<td> message </td>
						<td> Response message </td>
						<td> <b>SMS status fetched</b> - message about the request</td>
					</tr>
					<tr>
						<td> smsStatus </td>
						<td> Delivery Status of the SMS </td>
						<td> Exp: <b>Delivered</b> - current status of the sms</td>
					</tr>
				</tbody>
			</table>

			<div id="panel-heading font-bold" style="padding:10px; font-weight:900;">Response If Failed: (Response Format: JSON)</div>
	    	<table class="table table-bordered table-striped table-condensed flip-content">
				<thead class="flip-content">
					<tr>
						<th> Response Parameter Name </th>
						<th> Meaning/Value </th>
						<th> Desired Value - Description</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td> status </td>
						<td> Status of the API request </td>
						<td> <b>error</b> - means the request was failed to execute</td>
					</tr>
					<tr>
						<td> message </td>
						<td> Response message </td>
						<td> Exp: <b>Invalid smsId</b> - details about the failure</td>
					</tr>
				</tbody>
			</table>

			<div id="panel-heading font-bold" style="padding:10px; font-weight:900;">Possible types of status of a SMS:</div>
	    	<table class="table table-bordered table-striped table-condensed flip-content">
				<thead class="flip-content">
					<tr>
						<th> Status </th>
						<th> Meaning/Value </th>
						<th> Description</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td> Pending </td>
						<td> SMS is waiting to be sent to the desired number </td>
						<td> SMS is not sent to operator yet, currently under processing and will be sent shortly. If SMS is in <b>Pending</b> state for a long time then there might be some error in the operator end or our end. You may need to resend the sms or contact technical team for further info.</td>
					</tr>
					<tr>
						<td> Sent </td>
						<td> SMS was successfully sent </td>
						<td> SMS was successfully sent to the desired mobile number</td>
					</tr>
					<tr>
						<td> Delivered </td>
						<td> SMS was successfully delivered </td>
						<td> SMS was successfully delivered and successfully received by the desired mobile number. It confirms that the sms was successfully reached to the mobile handset of the desired mobile number.</td>
					</tr>
					<tr>
						<td> Failed </td>
						<td> SMS was failed </td>
						<td> SMS was failed by the operator and was not delivered to the desired number. There might be various reason for a sms sending failure. You may try to resend the sms. If it fails again then there might be some problem in the mobile number or operator is unable to send sms to the mobile number or the number is invaid.</td>
					</tr>
					<tr>
						<td> UnDelivered </td>
						<td> SMS was UnDelivered</td>
						<td> SMS was not able to be delivered to the mobile number. SMS was failed to reach the destination mobile number. May be the mobile was swithced off or the destination number was unable to receive the sms or the number or invalid.</td>
					</tr>
					<tr>
						<td> Transmitted </td>
						<td> SMS was successfully transmitted</td>
						<td> SMS was successfully transmitted to the destination mobile number by the mobile operator. This ensures that the sms sending was successful by the mobile operator or the operator has successfully processed the sms sending request.</td>
					</tr>
				</tbody>
			</table>
		</div>			
    </div>
</div>



    <!-- <div class="wrapper-md">
  <div class="panel panel-default"> 
            <div id="panel-heading font-bold" style="padding:10px; font-weight:900;">DLR Report Parameters </div> 
		
			<div class="portlet-body flip-scroll">

				<table class="table table-bordered table-striped table-condensed flip-content">
					<thead class="flip-content">
						<tr>
							<th> Parameter Name </th>
							<th> Meaning/Value </th>
							<th> Description </th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td> api_token (required)</td>
							<td> API Token </td>
							<td> Your API Token <strong id="key_id_ref"><?php echo auth()->user()->api_token; ?></strong> </td>
						</tr>
						<tr>
							<td> fromdate </td>
							<td> From Date </td>
							<td> User provided date input(format: YYYY-MM-YY, eg.<?php echo date("Y-m-d"); ?>)</td>
						</tr>
						<tr>
							<td> todate </td>
							<td> To Date </td>
							<td> User provided date input(format: YYYY-MM-YY, eg.<?php echo date("Y-m-d"); ?>) </td>
						</tr>
						<tr>
							<td> camid </td>
							<td> SMS Campaign id </td>
							<td> Unique campaign id </td>
						</tr>
						<tr>
							<td> limit_start </td>
							<td> Record showing from number </td>
							<td> eg.0 like limit 0,100</td>
						</tr>
						<tr>
							<td> limit_end </td>
							<td> Record showing end number </td>
							<td> eg.100 like limit 0, 100, (max record limit in a request is 2000) </td>
						</tr>
					</tbody>
				</table>
			</div>
			
            
            
            <div id="panel-heading font-bold" style="padding:10px; font-weight:900;">DLR Report Without Parameters</div> 
		
			<div class="portlet-body flip-scroll">

				<table class="table table-bordered table-striped table-condensed flip-content">
					<thead class="flip-content">
						<tr>
							<th> Parameter Name </th>
							<th> Meaning/Value </th>
							<th> Description </th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td> api_token</td>
							<td> API Token </td>
							<td> Your API Token <strong id="key_id_ref"><?php echo auth()->user()->api_token; ?></strong> </td>
						</tr>
					</tbody>
				</table>
			</div>
			<div class="well" style="margin: 20px;">
                <h4 class="modal-title"> <label> DLR report without parameter</label> </h4>
                <p>If no parameter provided, 100 records will be shown in current date, you can view more records using limit_start & limt_end parameters </p>
				<strong>API URL</strong> :<?php echo url()->to('/'); ?>/api/dlr-report?api_token=<?php echo auth()->user()->api_token; ?>
				<br>
				<strong>API URL</strong> : Your API Key <strong id="key_id_ref2"><?php echo auth()->user()->api_token; ?></strong>
            </div>
            

            <div id="panel-heading font-bold" style="padding:10px; font-weight:900;">DLR Report using limit range in current date</div> 
		
			<div class="portlet-body flip-scroll">

				<table class="table table-bordered table-striped table-condensed flip-content">
					<thead class="flip-content">
						<tr>
							<th> Parameter Name </th>
							<th> Meaning/Value </th>
							<th> Description </th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td> api_token</td>
							<td> API Token </td>
							<td> Your API Token <strong id="key_id_ref"><?php echo auth()->user()->api_token; ?></strong> </td>
                        </tr>
                        <tr>
							<td> limit_start </td>
							<td> Record showing from number </td>
							<td> eg.0 like limit 0,100</td>
						</tr>
						<tr>
							<td> limit_end </td>
							<td> Record showing end number </td>
							<td> eg.100 like limit 0, 100, (max record limit in a request is 2000) </td>
						</tr>
					</tbody>
				</table>
			</div>
			<div class="well" style="margin: 20px;">
                <h4 class="modal-title"> <label> DLR report using limit range</label> </h4>
                <p>You can control record view using limit_start & limit_end parameters, but maximum record view in request is 2000 </p>
				<strong>API URL</strong> :<?php echo url()->to('/'); ?>/api/detail-dlr-report?api_token=<?php echo auth()->user()->api_token; ?>&limit_start=200&limit_end=500
				<br>
				<strong>API URL</strong> : Your API Key <strong id="key_id_ref2"><?php echo auth()->user()->api_token; ?></strong>
            </div>
            

            <div id="panel-heading font-bold" style="padding:10px; font-weight:900;">DLR Report using limit range in specific date range</div> 
		
			<div class="portlet-body flip-scroll">

				<table class="table table-bordered table-striped table-condensed flip-content">
					<thead class="flip-content">
						<tr>
							<th> Parameter Name </th>
							<th> Meaning/Value </th>
							<th> Description </th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td> api_token</td>
							<td> API Token </td>
							<td> Your API Token <strong id="key_id_ref"><?php echo auth()->user()->api_token; ?></strong> </td>
                        </tr>
                        <tr>
							<td> limit_start </td>
							<td> Record showing from number </td>
							<td> eg.0 like limit 0,100</td>
						</tr>
						<tr>
							<td> limit_end </td>
							<td> Record showing end number </td>
							<td> eg.100 like limit 0, 100, (max record limit in a request is 2000) </td>
                        </tr>
                        <tr>
							<td> fromdate </td>
							<td> From Date </td>
							<td> User provided date input(format: YYYY-MM-YY, eg.<?php echo date("Y-m-d"); ?>)</td>
						</tr>
						<tr>
							<td> todate </td>
							<td> To Date </td>
							<td> User provided date input(format: YYYY-MM-YY, eg.<?php echo date("Y-m-d"); ?>) </td>
						</tr>
					</tbody>
				</table>
			</div>
			<div class="well" style="margin: 20px;">
                <h4 class="modal-title"> <label> DLR report using limit range in specific date range</label> </h4>
                <p>You can control record view using limit_start & limit_end parameters in a specific date range, but maximum record view in request is 2000 </p>
				<strong>API URL</strong> :<?php echo url()->to('/'); ?>/api/detail-dlr-report?api_token=<?php echo auth()->user()->api_token; ?>&limit_start=200&limit_end=500&fromdate=<?php echo date("Y-m-d"); ?>&todate=<?php echo date("Y-m-d"); ?>
				<br>
				<strong>API URL</strong> : Your API Key <strong id="key_id_ref2"><?php echo auth()->user()->api_token; ?></strong>
            </div>
            

            <div id="panel-heading font-bold" style="padding:10px; font-weight:900;">DLR Report using  Campaign ID</div> 
		
			<div class="portlet-body flip-scroll">

				<table class="table table-bordered table-striped table-condensed flip-content">
					<thead class="flip-content">
						<tr>
							<th> Parameter Name </th>
							<th> Meaning/Value </th>
							<th> Description </th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td> api_token</td>
							<td> API Token </td>
							<td> Your API Token <strong id="key_id_ref"><?php echo auth()->user()->api_token; ?></strong> </td>
                        </tr>
                        <tr>
							<td> camid </td>
							<td> SMS Campaign id </td>
							<td> Unique campaign id </td>
						</tr>
					</tbody>
				</table>
			</div>
			<div class="well" style="margin: 20px;">
                <h4 class="modal-title"> <label> DLR report using Campaign ID</label> </h4>
                <p>You can view record detail using Campaign ID </p>
				<strong>API URL</strong> :<?php echo url()->to('/'); ?>/api/dlr-report?api_token=<?php echo auth()->user()->api_token; ?>&camid=322378
				<br>
				<strong>API URL</strong> : Your API Key <strong id="key_id_ref2"><?php echo auth()->user()->api_token; ?></strong>
            </div>
			
			
                                            
			
		
			
        </div>
    </div> -->
 
        
        
       
<!-- Button trigger modal -->

  
  
  
  
  


	</div>

  </div>
@endsection