@extends('layouts.smsapp')

@section('appbody')
  <style type="text/css">
    .modal-dialog {
        width: 1200px;
        margin: 30px auto;
    }

    .modal-dialog-small {
        width: 700px;
        margin: 30px auto;
    }
    
    table.dataTable.nowrap th, table.dataTable.nowrap td {
        white-space: break-spaces;
    }
    #singleSmsReport_filter{
      display: none;
    }
  </style>
  <!-- content -->
  <div id="content" class="app-content" role="main">
    <div class="app-content-body ">
        <div class="hbox hbox-auto-xs hbox-auto-sm" ng-init="
        app.settings.asideFolded = false; 
        app.settings.asideDock = false;
        ">
        <!-- main -->
        <div class="col">
        <!-- main header -->
        <div class="bg-light lter b-b wrapper-md">
            <div class="row">
              <div class="col-sm-6 col-xs-12">
                @include('smsview.common.user-head-title')
                <small class="text-muted">Welcome to SMSBD Application</small>
              </div>
              <div class="col-sm-6 col-xs-12 text-right hidden-xs">
                  <h1 class="m-n font-thin h3 text-black font-bold">Campaign's Detail Report</h1>
                  <small class="text-muted"></small>
              </div>
            </div>
        </div>

        
        <div class="modal bd-example-modal-md fade" id="statusModal" tabindex="-1" role="dialog" aria-hidden="true">
          <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
              <div class="modal-header">
                
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="margin-top: -22px;">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>

              <div class="modal-body">
                <h3 id="smsStatus"></h3>
                <p class="text-success" id="successCount"></p>
                <p class="text-danger" id="failedCount"></p>
              </div>
              <div class="modal-footer">
                
                
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
              </div>
            </div>
          </div>
        </div>

        <!-- / main header -->
        <div class="wrapper-md" ng-controller="FlotChartDemoCtrl">
          <div class="row">
           <div class="col-md-12">
              <div class="panel panel-default dataTables_wrapper">
              <div class="panel-heading font-bold">
                  Campaign Details
                  <h4 class="pull-right"><span class="text-success">Processed Numbers: </span><span id="processedCon" class="text-success"></span> , <span class="text-primary">Remaining Numbers: </span><span id="remainingCon" class="text-primary"></span> , <span class="text-danger">Failed Numbers: </span><span id="failedCon" class="text-danger"></span></h4>
                </div>
                <div class="panel-body">
                  <div class="col-md-5">
                    <table class="table table-bordered table-responsive ">
                      <tbody>
                        <?php
                          if(Auth::guard('root')->user() || Auth::guard('reseller')->user()){
                            ?>
                              
                            <tr>
                                <td>User</td>
                                <td>{{ $campaignUser->name }} &nbsp;&nbsp; <b>Mobile:</b>{{$campaignUser->phone}}</td>
                            </tr>
                              
                            <?php
                        }
                        ?>
                        <tr>
                          <td>
                            Campaign Id: 
                          </td>
                          <td>
                            {{ $campaign->campaign_no }} 
                          </td>
                        </tr>
                        <tr>
                          <td>
                            Campaign Name: 
                          </td>
                          <td>
                            {{ $campaign->campaign_name }} 
                          </td>
                        </tr>
                        <tr>
                          <td>
                            Description: 
                          </td>
                          <td>
                            {{ $campaign->description }} 
                          </td>
                        </tr>
                        <tr>
                          <td>
                            Sender ID: 
                          </td>
                          <td>
                            {{ $campaign->getSenderId->name }} 
                          </td>
                        </tr>
                        <tr>
                          <td>
                            SMS Type: 
                          </td>
                          <td>
                            {{ $type }} 
                          </td>
                        </tr>
                        <tr>
                          <td>
                            <b>SMS Content: </b>
                          </td>
                          <td>
                            {{ $campaign->content }} 
                          </td>
                        </tr>
                        

                      </tbody>
                    </table>
                  </div>
                  <div class="col-md-3">
                    <table class="table table-bordered table-responsive ">
                      <tbody>

                        
                        <tr>
                          <td>
                            Sent From: 
                          </td>
                          <td>
                            {{ $sent_through }} 
                          </td>
                        </tr>
                        <tr>
                          <td>
                            Sms Type: 
                          </td>
                          <td>
                            {{ $smsType }} 
                          </td>
                        </tr>

                        <tr>
                          <td>
                            Total Contacts: 
                          </td>
                          <td>
                            {{ $campaign->total_numbers }} 
                          </td>
                        </tr>
                        <tr>
                          <td>
                            Sms Count: 
                          </td>
                          <td>
                            {{ $campaign->sms_qty }} 
                          </td>
                        </tr>
                        <tr>
                          <td>
                            Date & Time: 
                          </td>
                          <td>
                            {{ date('Y-m-d h:i:s', strtotime($campaign->created_at)) }} 
                          </td>
                        </tr>
                        <tr>
                          <td>
                            Status: 
                          </td>
                          <td>
                            <span id="campaign_status">{{ $campaign->status }}</span> 
                          </td>
                        </tr>

                      </tbody>
                    </table>
                  </div>
                  <div class="col-md-4">
                    <table class="table table-bordered table-responsive text-center">
                      <thead>
                        <th class="text-center">
                          Operator
                        </th>
                        <th class="text-center">
                          Total Numbers
                        </th>
                        <th class="text-center">
                          <span class="text-success">Sent</span>
                        </th>
                        <th class="text-center">
                          <span class="text-danger">Failed</span>
                        </th>
                      </thead>
                      <tbody>

                        
                        <tr>
                          <td>
                            Grameenphone 
                          </td>
                          <td>
                            {{ $totalNumbers['GP'] }} 
                          </td>
                          <td>
                            <span class="text-success" id="gpSent"></span>
                          </td>
                          <td>
                            <span class="text-danger" id="gpFailed"></span>
                          </td>
                        </tr>
                        <tr>
                          <td>
                            Banglalink
                          </td>
                          <td>
                            {{ $totalNumbers['BL'] }} 
                          </td>
                          <td>
                            <span class="text-success" id="blSent"></span>
                          </td>
                          <td>
                            <span class="text-danger" id="blFailed"></span>
                          </td>
                        </tr>

                        <tr>
                          <td>
                            Airtel
                          </td>
                          <td>
                            {{ $totalNumbers['Airtel'] }} 
                          </td>
                          <td>
                            <span class="text-success" id="airtelSent"></span>
                          </td>
                          <td>
                            <span class="text-danger" id="airtelFailed"></span>
                          </td>
                        </tr>
                        <tr>
                          <td>
                            Robi
                          </td>
                          <td>
                            {{ $totalNumbers['Robi'] }} 
                          </td>
                          <td>
                            <span class="text-success" id="robiSent"></span>
                          </td>
                          <td>
                            <span class="text-danger" id="robiFailed"></span>
                          </td>
                        </tr>
                        <tr>
                          <td>
                            Teletalk
                          </td>
                          <td>
                            {{ $totalNumbers['Ttk'] }} 
                          </td>
                          <td>
                            <span class="text-success" id="ttkSent"></span>
                          </td>
                          <td>
                            <span class="text-danger" id="ttkFailed"></span>
                          </td>
                        </tr>

                      </tbody>
                    </table>
                    <button class="btn btn-sm btn-danger pull-right" id="resendFailed"> Resend To Failed/Pending Numbers</button>
                    <br/><br/>
                  <?php
                  if(Auth::guard('root')->user() && $campaign->status == 'Pending'){
                      ?>
                      <button class="btn btn-sm btn-primary pull-right" id="forceRefund"> Process Refund for Failed & Pending Numbers</button>
                      <?php
                  }
                  ?>
                  </div>
                  


                </div>
              </div>
            </div>
          </div>
          <div class="row">
              <div class="col-md-12">
                  <div class="panel panel-default dataTables_wrapper">
                      <div class="panel-heading font-bold">
                        Campaign Numbers Details
                      </div>
                      <div class="table-responsive ">
                        <table class="table table-striped table-hover display no-footer dtr-inline dataTable" id="singleSmsReport" style="width: 100%;" role="grid" aria-describedby="example_info">
                          <thead>
                            <tr>
                              
                              <th>Mobile Number</th>
                              <th>Operator</th>
                              <th>Status</th>
                            </tr>
                          </thead>
                          <thead>
                              <tr>
                                <th>
                                  <input type="text" data-column="0"  class="search-input-text" placeholder="Search Number" >
                                </th>
                                <th></th>
                                <th></th>
                              </tr>
                          </thead>

                          <tfoot>
                            <tr>
                              <th>Mobile Number</th>
                              <th>Operator</th>
                              <th>Status</th>
                            </tr>
                          </tfoot>
                          <tbody>
                          </tbody>
                        </table>
                      </div>
                    </div>
              </div>
          </div>
        </div>
        
        </div>
        <!-- / main -->
        <!-- right col -->
        
        <!-- / right col -->
        </div>



        </div>
        </div>
        <!-- /content -->
        
@endsection

@section('scripts')

<script>

  //dataTABLE
  $(document).ready(function() {
    
    var dataTable = $('#singleSmsReport').DataTable( {
      "processing": true,
      "language": {
          "processing": "<div class='overlay'><i class='fa fa-refresh fa-spin'></i></div>"
      },
      "columnDefs": [ {
            "targets": 'no-sort',
            "orderable": false,
      } ], //desable sorting
      "bAutoWidth": false ,

      "serverSide": true,

      "ajax":{
        url :"{{ route('campaign-details-data', $campaign->id) }}", // json datasource
        type: "post",  // method  , by default get
        error: function(){  // error handling
          $(".employee-grid-error").html("");
          $("#employee-grid").append('<tbody class="employee-grid-error"><tr><th colspan="3">No Data Found!</th></tr></tbody>');
          $("#employee-grid_processing").css("display","none");
          
        },
        complete: function() {
          //
        }
      }
    } );    

    var campaignType = '{{ $campaign->campaign_type }}';

    var refreshTime = 10000;

    if (campaignType=="A") {
      var refreshTime = 10000;
    } else if (campaignType=="B") {
      var refreshTime = 18000;
    } else if (campaignType=="C") {
      var refreshTime = 23000;
    } else if (campaignType=="D") {
      var refreshTime = 30000;
    } else if (campaignType=="E") {
      var refreshTime = 35000;
    }

    $("#employee-grid_filter").css("display","none");  // hiding global search box
    $('.search-input-text').on( 'keyup click', function () {   // for text boxes
      var i =$(this).attr('data-column');  // getting column index
      var v =$(this).val();  // getting search input value
      dataTable.columns(i).search(v).draw();
    } );


    function livecheck() {
       $.ajax({
        url: "{{ config('apiconfig.api_url') }}/sms-api/campaign/livestatus",
        type: "post",
        data: {
          api_token: '{{ $api_token }}',
          campaign: {{ $campaign->id }}
        },
        success: function(res) {
          console.log(res);
          $('#processedCon').html(res.processed);
          $('#remainingCon').html(res.pending);
          $('#failedCon').html(res.failed);
          $('#campaign_status').html(res.campaign_status);

          $('#gpSent').html(res.gpSent);
          $('#gpFailed').html(res.gpFailed);
          $('#blSent').html(res.blSent);
          $('#blFailed').html(res.blFailed);
          $('#airtelSent').html(res.airtelSent);
          $('#airtelFailed').html(res.airtelFailed);
          $('#robiSent').html(res.robiSent);
          $('#robiFailed').html(res.robiFailed);
          $('#ttkSent').html(res.ttkSent);
          $('#ttkFailed').html(res.ttkFailed);

          remainingContacts = res.pending;
          failedNums = res.failed;
        }
      });
    }

    livecheck();

    
    //resend failed numbers
    $("#resendFailed").click(function() {
      $(this).attr('disabled', true);

      $.ajax({
        url: "{{ config('apiconfig.api_url') }}/sms-api/process-campaign-failed-retry",
        type: "post",
        data: {
          api_token: '{{ $api_token }}',
          campaign: {{ $campaign->id }}
        },
        success: function(res) {
          if (res.status=='success') {
            $('#smsStatus').css({'color':'green'}).text(res.message);            
            $('#statusModal').modal('show');

            
            setInterval( function () {
      
              if (failedNums!=0) {
                livecheck();
                dataTable.ajax.reload();
              }
                
            }, refreshTime );
            

          }

          if (res.status=='error') {
            $('#smsStatus').css({'color':'red'}).text(res.message);
            $('#statusModal').modal('show');
          }
        }
      });
    });

    function failedRetryLoad(){      
      setInterval( function () {
        if (remainingContacts!=0) {
          livecheck();
        }
      }, refreshTime );
    }

  } );
</script>

@endsection