@extends('layouts.smsapp')

@section('appbody')
    <!-- content -->
    <div id="content" class="app-content" role="main">

  	<div class="app-content-body ">

        
<div class="bg-light lter b-b wrapper-md">

	<div class="col-md-12">
		<h1 class="m-n font-thin h3">API Management    <span class="pull-right" style="margin-right:10px; color:green;">@if(session()->has('apitokenmsg')) <strong>{{session()->get('apitokenmsg')}} @endif</strong></span></h1> 
	</div>
	<br> <br> 
	<div class="well bg-white">
		Your API Key : <strong id="key_show_id"> <?php echo auth()->user()->api_token; ?>  </strong>  &nbsp; &nbsp; 
		<a href="{{route('generate-apitoken',[Auth::guard('web')->user()])}}" class="btn btn-sm btn-danger pull-right">Generate New <i class="fa fa-plus"></i></a>
	</div> 
	<div class="well bg-white">
        <ul><li style="display: inline-block; margin-right:20px; font-weight:900;">Approved Senderid List:  </li><?php foreach($data as $senderids) { foreach($senderids as $senderid) { echo "<li style='display:inline-block; margin-right:10px;'>".$senderid['senderid']."</li>"; }}  ?></ul>
	</div> 
	
    
    
    <div class="panel with-nav-tabs panel-default" style="padding:20px;">
    <h3>API</h3>
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
		<strong>API URL (HTTP(s) GET))</strong> : <?php echo url()->to('/'); ?>/api/send-sms?api_token=(APITOKEN)&senderid=(Approved Sender Id)&message=(Message Content)&contact_number=(Contact Number)
	                          </div>
                            </div>
                            <div class="box box-danger">
                               <div class="alert alert-info bg-white">
        
        <pre style="background:#FFF">&lt;?php
                $post_url = "<?php echo url()->to('/'); ?>/api/send-sms" ;  
                  
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
                    curl_setopt($request, CURLOPT_HEADER, 0);
                    curl_setopt($request, CURLOPT_RETURNTRANSFER, 1);  
                    curl_setopt($request, CURLOPT_POSTFIELDS, $post_string); 
                    curl_setopt($request, CURLOPT_SSL_VERIFYPEER, FALSE);  
                    $post_response = curl_exec($request);  
                   curl_close ($request);  
                  
                $responses=array();  		
                 $array =  json_decode( preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $post_response), true );   
                 
                if($array){ 
                 echo $array['msg'] ;
                 
                 echo $array['camid'] ;
                 
                 print_r($array);
                }
                 
                ?&gt;
                                                </pre>
	                          </div>
                            </div>
                        </div>

                        <div class="tab-pane fade in" id="tab1API2">
                            <div class="box box-danger">
                               <div class="alert alert-info bg-white">
		<strong>API URL (HTTP(s) POST))</strong> : <?php echo url()->to('/'); ?>/api/send-sms?api_token=(APITOKEN)&senderid=(Approved Sender Id)&message=(Message Content)&contact_number=(Contact Number)
	                          </div>
                            </div>
                            <div class="box box-danger">
                               <div class="alert alert-info bg-white">
        
        <pre style="background:#FFF">&lt;?php
                $post_url = "<?php echo url()->to('/'); ?>/api/send-sms" ;  
                  
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
                  
                $headers = array(
                    'Content-Type: application/json',
                    'Authorization: Bearer '. api_token
                );

                $request = curl_init($post_url);
                    curl_setopt($request, CURLOPT_HTTPHEADER, $headers);
                    curl_setopt($request, CURLOPT_RETURNTRANSFER, 1);  
                    curl_setopt($request, CURLOPT_POSTFIELDS, $post_string); 
                    curl_setopt($request, CURLOPT_SSL_VERIFYPEER, FALSE);  
                    $post_response = curl_exec($request);  
                   curl_close ($request);  
                  
                $responses=array();  		
                 $array =  json_decode( preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $post_response), true );   
                 
                if($array){ 
                 echo $array['msg'] ;
                 
                 echo $array['camid'] ;
                 
                 print_r($array);
                }
                 
                ?&gt;
                                                </pre>
	                          </div>
                            </div>
                        </div>
                        
                        <div class="tab-pane fade in" id="tab1API3">
                            <div class="box box-danger">
                                <div class="alert alert-success">
                                        <strong>SEND SMS API Guzzle</strong> : 
                                   <pre style="background:#FFF">&lt;?php
                                   $client = new Client();

                                    $smsresponse = $client->request('GET',"<?php echo url()->to('/'); ?>/api/send-sms",[
                                        'query' => [ 
                                        'api_token' =&gt; 'API KEY',
                                        'senderid' =&gt; 'SENDER ID',
                                        'contact_number' =&gt; 'MOBILE NUMBER',
                                        'message' =&gt; 'Hello world'
                                    ]);
    
                                    return $smsresponse->getBody();
                 
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
  <div class="panel panel-default"> 
        <div id="messagebox"> </div> 
		
		
		
            <div id="panel-heading font-bold" style="padding:10px; font-weight:900;">SMS Send API</div>
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
							<td> api_token </td>
							<td> API Token </td>
							<td> Your API Token <strong id="key_id_ref"><?php echo auth()->user()->api_token; ?></strong> </td>
						</tr>
						<tr>
							<td> senderid </td>
							<td> approved senderid </td>
							<td> senderid assigned by vendor</td>
						</tr>
						<tr>
							<td> contact_number </td>
							<td> mobile number </td>
							<td> Exp: Comma separated 88017XXXXXXXX,88018XXXXXXXX,88019XXXXXXXX... (start with 880 or 01x) </td>
						</tr>
						<tr>
							<td> message </td>
							<td> sms content </td>
							<td> Exp: anything you can write into message content. </td>
						</tr>
					</tbody>
				</table>
			</div>
			
			
			<div class="well" style="margin: 20px;">
                <h4 class="modal-title"> <label> Send SMS API</label> </h4>
				<strong>API URL</strong> :<?php echo url()->to('/'); ?>/api/send-sms?api_token=<?php echo auth()->user()->api_token; ?>&senderid=(Approved Sender Id)&message=(Message Content)&contact_number=(Contact Number)
				<br>
				<strong>API URL</strong> : Your API Key <strong id="key_id_ref2"><?php echo auth()->user()->api_token; ?></strong>
            </div>

            <div id="panel-heading font-bold" style="padding:10px; font-weight:900;">Balance Check API</div>
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
							<td> api_token </td>
							<td> API Token </td>
							<td> Your API Token <strong id="key_id_ref"><?php echo auth()->user()->api_token; ?></strong> </td>
						</tr>
						<tr>
							<td> senderidtype </td>
							<td> approved senderid type(mask|nonmask|voice)</td>
							<td> senderid assigned by vendor</td>
						</tr>
						
					</tbody>
				</table>
			</div>
            
			<div class="well" style="margin: 20px;">
                <h4 class="modal-title"> <label> SMS Balance Check API</label> </h4>
				<strong>API URL</strong> :<?php echo url()->to('/'); ?>/api/sms-balance?api_token=API_TOKEN&senderidtype=mask||nonmask||voice
				<br>
				<strong>API URL</strong> : Your API Key <strong id="key_id_ref2"><?php echo auth()->user()->api_token; ?></strong>
			</div>
			
			
                                            
			
			<!--<h4 class="modal-title"> <label>API Key Retrieval</label></h4>
            <div class="well">
				<strong>API URL</strong> : https://portal.smsinbd.com/getkey/(username)/( password)
				<br>
				<strong>Username</strong> : Your account User ID used to login.
				<br>
				<strong>Password</strong> : Account password that you use to login.
			</div>-->
			
        </div>
    </div>



    <div class="wrapper-md">
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
			
			
                                            
			
			<!--<h4 class="modal-title"> <label>API Key Retrieval</label></h4>
            <div class="well">
				<strong>API URL</strong> : https://portal.smsinbd.com/getkey/(username)/( password)
				<br>
				<strong>Username</strong> : Your account User ID used to login.
				<br>
				<strong>Password</strong> : Account password that you use to login.
			</div>-->
			
        </div>
    </div>
 
        
        
       
<!-- Button trigger modal -->

  
  
  
  
  


	</div>

  </div>
@endsection