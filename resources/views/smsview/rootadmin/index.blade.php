@extends('layouts.smsapp')

@section('appbody')
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
            <div class="col-sm-6 text-right hidden-xs">
                
            </div>
            </div>
        </div>
        <!-- / main header -->
        <div class="wrapper-md" ng-controller="FlotChartDemoCtrl">
            <!-- stats -->
            <div class="row row-sm text-center">
                <div class="col-md-3 col-lg-3 col-xs-6 m-b-md">
                    <div class="r bg-success dker item hbox no-border">
                        <div class="col w-xxs v-middle hidden-md">
                            <div class="sparkline inline"><i class="icon-envelope text-white-dker" style="font-size: 15px;"></i></div>
                        </div>
                        <div class="col dk padder-v r-r">
                            <div class="text-white-dk font-thin font-bold h6"><span>TODAY's Campaign SMS</span></div>
                            <span class="font-thin h6 font-bold">{{number_format($todaysCampaignSMS)}}</span>
                        </div>
                    </div>
                </div>

                <!-- <div class="col-md-3 col-lg-3 col-xs-6 m-b-md">
                    <div class="r bg-success dker item hbox no-border">
                        <div class="col w-xxs v-middle hidden-md">
                            <div class="sparkline inline"><i class="icon-envelope text-white-dker" style="font-size: 15px;"></i></div>
                        </div>
                        <div class="col dk padder-v r-r">
                            <div class="text-white-dk font-thin font-bold h6"><span>THIS WEEK SMS SENT</span></div>
                            <span class="font-thin h6 font-bold">number_format($thisweekssmssent)</span>
                        </div>
                    </div>
                </div> -->

                <!-- <div class="col-md-3 col-lg-3 col-xs-6 m-b-md">
                    <div class="r bg-success dker item hbox no-border">
                        <div class="col w-xxs v-middle hidden-md">
                            <div class="sparkline inline"><i class="icon-envelope text-white-dker" style="font-size: 15px;"></i></div>
                        </div>
                        <div class="col dk padder-v r-r">
                            <div class="text-white-dk font-thin font-bold h6"><span>THIS MONTH SMS SENT</span></div>
                            <span class="font-thin h6 font-bold">number_format($thismonthsmssent)</span>
                        </div>
                    </div>
                </div> -->
                
                <div class="col-md-3 col-lg-3 col-xs-6 m-b-md">
                    <div class="r bg-success dker item hbox no-border">
                        <div class="col w-xxs v-middle hidden-md">
                            <div class="sparkline inline"><i class="icon-envelope icon text-white-lter" style="font-size: 15px;"></i></div>
                        </div>
                        <div class="col dk padder-v r-r">
                            <div class="text-white-dk font-thin font-bold h6"><span>TODAY'S Single/API SMS</span></div>
                            <span class="font-thin  font-bold h6">{{$todaysSingleSMS}}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-lg-3 col-xs-6 m-b-md">
                    <div class="r bg-success dker item hbox no-border">
                        <div class="col w-xxs v-middle hidden-md">
                            <div class="sparkline inline"><i class="icon-envelope icon text-white-lter" style="font-size: 15px;"></i></div>
                        </div>
                        <div class="col dk padder-v r-r">
                            <div class="text-white-dk font-thin font-bold h6"><span>Today's total sms</span></div>
                            <span class="font-thin font-bold h6">{{number_format($todaysCampaignSMS+$todaysSingleSMS)}}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-lg-3 col-xs-6 m-b-md">
                    <div class="r bg-primary dker item hbox no-border">
                        <div class="col w-xxs v-middle hidden-md">
                            <div class="sparkline inline"><i class="icon-envelope icon text-success-lter" style="font-size: 15px;"></i></div>
                        </div>
                        <div class="col dk padder-v r-r">
                            <div class="text-white-dk font-thin font-bold h6"><span>Today's Total Campaign</span></div>
                            <span class="font-thin font-bold h6">{{ $todaysTotalCampaign }}</span>
                        </div>
                    </div>
                </div>
                
                
                <!--2nd col-->
                <div class="col-md-3 col-lg-3 col-xs-6 m-b-md">
                    <div class="r bg-success dker item hbox no-border">
                        <div class="col w-xxs v-middle hidden-md">
                            <div class="sparkline inline"><i class="icon-envelope text-white-dker" style="font-size: 15px;"></i></div>
                        </div>
                        <div class="col dk padder-v r-r">
                            <div class="text-white-dk font-thin font-bold h6"><span>Last 7 Days Campaign SMS</span></div>
                            <span class="font-thin h6 font-bold">{{number_format($last7daysCampaignSMS)}}</span>
                        </div>
                    </div>
                </div>

                <div class="col-md-3 col-lg-3 col-xs-6 m-b-md">
                    <div class="r bg-success dker item hbox no-border">
                        <div class="col w-xxs v-middle hidden-md">
                            <div class="sparkline inline"><i class="icon-envelope icon text-white-lter" style="font-size: 15px;"></i></div>
                        </div>
                        <div class="col dk padder-v r-r">
                            <div class="text-white-dk font-thin font-bold h6"><span>Last 7 Days Single SMS</span></div>
                            <span class="font-thin  font-bold h6">{{number_format($last7daysSingleSMS)}}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-lg-3 col-xs-6 m-b-md">
                    <div class="r bg-success dker item hbox no-border">
                        <div class="col w-xxs v-middle hidden-md">
                            <div class="sparkline inline"><i class="icon-envelope icon text-white-lter" style="font-size: 15px;"></i></div>
                        </div>
                        <div class="col dk padder-v r-r">
                            <div class="text-white-dk font-thin font-bold h6"><span>Last 7 Days Total SMS</span></div>
                            <span class="font-thin font-bold h6">{{number_format($last7daysCampaignSMS+$last7daysSingleSMS)}}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-lg-3 col-xs-6 m-b-md">
                    <div class="r bg-primary dker item hbox no-border">
                        <div class="col w-xxs v-middle hidden-md">
                            <div class="sparkline inline"><i class="icon-envelope icon text-success-lter" style="font-size: 15px;"></i></div>
                        </div>
                        <div class="col dk padder-v r-r">
                            <div class="text-white-dk font-thin font-bold h6"><span>Last 7 Days Total Campaign</span></div>
                            <span class="font-thin font-bold h6">{{$last7daysTotalCampaign}}</span>
                        </div>
                    </div>
                </div>
                
                <!--2nd col end-->
                
                
                
                 <!--3rd col-->
                <div class="col-md-3 col-lg-3 col-xs-6 m-b-md">
                    <div class="r bg-success dker item hbox no-border">
                        <div class="col w-xxs v-middle hidden-md">
                            <div class="sparkline inline"><i class="icon-envelope text-white-dker" style="font-size: 15px;"></i></div>
                        </div>
                        <div class="col dk padder-v r-r">
                            <div class="text-white-dk font-thin font-bold h6"><span>Last 30 Days Campaign SMS</span></div>
                            <span class="font-thin h6 font-bold">{{number_format($last30daysCampaignSMS)}}</span>
                        </div>
                    </div>
                </div>

                <div class="col-md-3 col-lg-3 col-xs-6 m-b-md">
                    <div class="r bg-success dker item hbox no-border">
                        <div class="col w-xxs v-middle hidden-md">
                            <div class="sparkline inline"><i class="icon-envelope icon text-white-lter" style="font-size: 15px;"></i></div>
                        </div>
                        <div class="col dk padder-v r-r">
                            <div class="text-white-dk font-thin font-bold h6"><span>Last 30 Days Single SMS</span></div>
                            <span class="font-thin  font-bold h6">{{number_format($last30daysSingleSMS)}}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-lg-3 col-xs-6 m-b-md">
                    <div class="r bg-success dker item hbox no-border">
                        <div class="col w-xxs v-middle hidden-md">
                            <div class="sparkline inline"><i class="icon-envelope icon text-white-lter" style="font-size: 15px;"></i></div>
                        </div>
                        <div class="col dk padder-v r-r">
                            <div class="text-white-dk font-thin font-bold h6"><span>Last 30 Days Total SMS</span></div>
                            <span class="font-thin font-bold h6">{{number_format($last30daysCampaignSMS+$last7daysSingleSMS)}}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-lg-3 col-xs-6 m-b-md">
                    <div class="r bg-primary dker item hbox no-border">
                        <div class="col w-xxs v-middle hidden-md">
                            <div class="sparkline inline"><i class="icon-envelope icon text-success-lter" style="font-size: 15px;"></i></div>
                        </div>
                        <div class="col dk padder-v r-r">
                            <div class="text-white-dk font-thin font-bold h6"><span>Last 30 Days Total Campaign</span></div>
                            <span class="font-thin font-bold h6">{{$last30daysTotalCampaign}}</span>
                        </div>
                    </div>
                </div>
                
                <!--3rd col end-->
                
                
                
                
                
                <div class="col-md-2 col-lg-2 col-xs-6 m-b-md">
                    <div class="r bg-primary dker item hbox no-border">
                        <div class="col w-xxs v-middle hidden-md">
                            <div class="sparkline inline"><i class="icon-user icon text-success-lter" style="font-size: 15px;"></i></div>
                        </div>
                        <div class="col dk padder-v r-r">
                            <div class="text-white-dk font-thin font-bold h6"><span>RESELLER</span></div>
                            <span class="font-thin font-bold h6">{{$totalreseller}}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-2 col-lg-2 col-xs-6 m-b-md">
                    <div class="r bg-primary dker item hbox no-border">
                        <div class="col w-xxs v-middle hidden-md">
                            <div class="sparkline inline"><i class="icon-user icon text-success-lter" style="font-size: 15px;"></i></div>
                        </div>
                        <div class="col dk padder-v r-r">
                            <div class="text-white-dk font-thin font-bold h6"><span>CLIENTS</span></div>
                            <span class="font-thin  font-bold h6">{{$totalusers}}</span>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4 col-lg-4 col-xs-6 m-b-md">
                    <div class="r bg-info dker item hbox no-border">
                        <div class="col w-xxs v-middle hidden-md">
                            <div class="sparkline inline"><i class="fa fa-money icon text-white-dker" style="font-size: 15px;"></i></div>
                        </div>
                        <div class="col dk padder-v r-r">
                            <div class="text-white-dk font-thin font-bold h6"><span>TODAY'S REVENUE</span></div>
                            <span class="font-thin font-bold h6">{{ number_format($totalrevinue,2) }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 col-lg-4 col-xs-6 m-b-md">
                    <div class="r bg-info dker item hbox no-border">
                        <div class="col w-xxs v-middle hidden-md">
                            <div class="sparkline inline"><i class="fa fa-money icon text-white-dker" style="font-size: 15px;"></i></div>
                        </div>
                        <div class="col dk padder-v r-r">
                            <div class="text-white-dk font-thin font-bold h6"><span>THIS MONTH REVENUE</span></div>
                            <span class="font-thin font-bold h6">{{number_format($totalrevinueinmonth,2)}}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 col-lg-4 col-xs-6 m-b-md">
                    <div class="r bg-info dker item hbox no-border">
                        <div class="col w-xxs v-middle hidden-md">
                            <div class="sparkline inline"><i class="fa fa-money icon text-white-dker" style="font-size: 15px;"></i></div>
                        </div>
                        <div class="col dk padder-v r-r">
                            <div class="text-white-dk font-thin font-bold h6"><span>THIS YEAR REVENUE</span></div>
                            <span class="font-thin font-bold h6">{{ number_format($totalrevinueinyear,2) }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 col-lg-4 col-xs-6 m-b-md mt-3">
                    <div class="r bg-primary dker item hbox no-border">
                        <div class="col w-xxs v-middle hidden-md">
                            <div class="sparkline inline"><i class="icon-user icon text-success-lter" style="font-size: 15px;"></i></div>
                        </div>
                        <div class="col dk padder-v r-r">
                            <div class="text-white-dk font-thin font-bold h6"><span>TODAY'S ENROLL CLIENT</span></div>
                            <span class="text-white-dk font-thin font-bold h6">{{$todaysenrollclient}}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 col-lg-4 col-xs-6 m-b-md mt-3">
                    <div class="r bg-primary dker item hbox no-border">
                        <div class="col w-xxs v-middle hidden-md">
                            <div class="sparkline inline"><i class="icon-user icon text-success-lter" style="font-size: 15px;"></i></div>
                        </div>
                        <div class="col dk padder-v r-r">
                            <div class="text-white-dk font-thin font-bold h6"><span>MONTHLY ENROLL CLIENT</span></div>
                            <span class="text-white-dk font-thin font-bold h6">{{$monthlyenrollclient}}</span>
                        </div>
                    </div>
                </div>
            </div>
           

            {{-- <div class="row">
                <div class="col-md-12 col-lg-12">
                    <div class="panel panel-default dataTables_wrapper">
                      <div class="panel-heading font-bold">
                          <div class="panel-title">Single SMS Activity</div>
                      <!-- <a href="{{route('teletalk-sms-senderid',['senderidtype'=>'teletalk'])}}" class="btn btn-primary btn-addon btn-md mb-5" style="margin-bottom:10px; z-index: 99; position:relative; margin-right:20px;"><i class="fa fa-list"></i> Teletalk Senderid</a> -->
                        
                      </div>
                      <div class="table-responsive ">
                        <table class="table table-striped table-hover display no-footer dtr-inline dataTable" id="singleSmsReport" style="width: 100%;" role="grid" aria-describedby="example_info">
                          <thead>
                            <tr>
                              <th>ID</th>
                              <th>Client</th>
                              <th>SenderID</th>
                              <th>Content</th>
                              <th>Number</th>
                              <th>Time</th>
                              <th>Status</th>
                            </tr>
                          </thead>
                          <thead>
                              <tr>
                                <th></th>
                                <th>
                                    <input type="text" data-column="1"  class="search-input-text" width="100" placeholder="Search Client" >
                                </th>
                                <th>
                                <input type="text" data-column="2"  class="search-input-text" placeholder="Search SENDERID">
                                </th>
                                <th>
                                    <input type="text" data-column="3"  class="search-input-text" placeholder="Search Content">
                                </th>
                                <th></th>
                                <th>
                                    <input type="text" data-column="4"  class="search-input-text" placeholder="Search DATE-TIME">
                                </th>
                                <th></th>
                              </tr>
                          </thead>
                          <tfoot>
                            <tr>
                            <th>ID</th>
                              <th>Client</th>
                              <th>SenderID</th>
                              <th>Content</th>
                              <th>Number</th>
                              <th>Time</th>
                              <th>Status</th>
                            </tr>
                          </tfoot>
                          <tbody>
                          </tbody>
                        </table>
                      </div>
                    </div>
                </div>
            </div> --}}

        </div>
        </div>
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
        url :"{{ route('root-singlesms-data') }}", // json datasource
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



    $("#employee-grid_filter").css("display","none");  // hiding global search box
    $('.search-input-text').on( 'keyup click', function () {   // for text boxes
      var i =$(this).attr('data-column');  // getting column index
      var v =$(this).val();  // getting search input value
      dataTable.columns(i).search(v).draw();
    } );

    var refreshTime = 10000;

    
    //live tracking
    setInterval( function () {
        dataTable.ajax.reload();
    }, refreshTime );

  } );
</script>

@endsection