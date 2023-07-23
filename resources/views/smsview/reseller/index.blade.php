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
            @include('smsview.reseller.reseller-head')
            <div class="col-sm-6 text-right hidden-xs">
                
            </div>
            </div>
        </div>
        <!-- / main header -->
        <div class="wrapper-md" ng-controller="FlotChartDemoCtrl">
            <!-- stats -->
            <div class="row row-sm text-center">
                <div class="col-md-4 col-lg-4 col-xs-6 m-b-md">
                        <div class="r bg-success dker item hbox no-border">
                            <div class="col w-xs v-middle hidden-md">
                                <div class="sparkline inline"><i class="icon-envelope text-white-dker" style="font-size: 15px;"></i></div>
                            </div>
                            <div class="col dk padder-v r-r">
                                <div class="text-white-dk font-thin font-bold h6"><span>TODAY'S SMS SENT HISTORY</span></div>
                                <span class="font-thin h6 font-bold">{{number_format($todayssmssent,2)}}</span>
                            </div>
                        </div>
                    </div>
                <div class="col-md-4 col-lg-4 col-xs-6 m-b-md">
                    <div class="r bg-success dker item hbox no-border">
                        <div class="col w-xs v-middle hidden-md">
                            <div class="sparkline inline"><i class="icon-basket icon text-white-lter" style="font-size: 15px;"></i></div>
                        </div>
                        <div class="col dk padder-v r-r">
                            <div class="text-white-dk font-thin font-bold h6"><span>TODAY'S SALES</span></div>
                            <span class="font-thin font-bold h6">{{$totdaysproductsale}}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 col-lg-4 col-xs-12 m-b-md">
                    <div class="r bg-success dker item hbox no-border">
                        <div class="col w-xs v-middle hidden-md">
                            <div class="sparkline inline"><i class="icon-basket icon text-white-lter" style="font-size: 15px;"></i></div>
                        </div>
                        <div class="col dk padder-v r-r">
                            <div class="text-white-dk font-thin font-bold h6"><span>TOTAL SALES</span></div>
                            <span class="font-thin font-bold h6">{{$totalproductsalebyroot}}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row row-sm text-center">
                <div class="col-md-4 col-lg-4 col-xs-6 m-b-md">
                    <div class="r bg-primary dker item hbox no-border">
                        <div class="col w-xs v-middle hidden-md">
                            <div class="sparkline inline"><i class="icon-user icon text-success-lter" style="font-size: 15px;"></i></div>
                        </div>
                        <div class="col dk padder-v r-r">
                            <div class="text-white-dk font-thin font-bold h6"><span>CLIENTS</span></div>
                            <span class="font-thin font-bold h6">{{$totalusers}}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 col-lg-4 col-xs-6 m-b-md mt-3">
                    <div class="r bg-primary dker item hbox no-border">
                        <div class="col w-xs v-middle hidden-md">
                            <div class="sparkline inline"><i class="icon-user icon text-success-lter" style="font-size: 15px;"></i></div>
                        </div>
                        <div class="col dk padder-v r-r">
                            <div class="text-white-dk font-thin font-bold h6"><span>TODAY'S ENROLL CLIENT</span></div>
                            <span class="text-white-dk font-thin font-bold h6">{{$todaysenrollclient}}</span>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 col-lg-4 col-xs-12 m-b-md mt-3">
                    <div class="r bg-primary dker item hbox no-border">
                        <div class="col w-xs v-middle hidden-md">
                            <div class="sparkline inline"><i class="icon-user icon text-success-lter" style="font-size: 15px;"></i></div>
                        </div>
                        <div class="col dk padder-v r-r">
                            <div class="text-white-dk font-thin font-bold h6"><span>MONTHLY ENROLL CLIENT</span></div>
                            <span class="text-white-dk font-thin font-bold h6">{{$monthlyenrollclient}}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row row-sm text-center">
                <div class="col-md-4 col-lg-4 col-xs-6 m-b-md">
                    <div class="r bg-primary dker item hbox no-border">
                        <div class="col w-xs v-middle hidden-md">
                            <div class="sparkline inline"><i class="fa fa-money icon text-white-dker" style="font-size: 15px;"></i></div>
                        </div>
                        <div class="col dk padder-v r-r">
                            <div class="text-white-dk font-thin font-bold h6"><span>TODAY'S REVENUE</span></div>
                            <span class="font-thin font-bold h6">{{number_format($totalrevinue,2)}}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 col-lg-4 col-xs-6 m-b-md">
                    <div class="r bg-info dker item hbox no-border">
                        <div class="col w-xs v-middle hidden-md">
                            <div class="sparkline inline"><i class="fa fa-money icon text-white-dker" style="font-size: 15px;"></i></div>
                        </div>
                        <div class="col dk padder-v r-r">
                            <div class="text-white-dk font-thin font-bold h6"><span>THIS MONTH REVENUE</span></div>
                            <span class="font-thin font-bold h6">{{number_format($totalrevinueinmonth,2)}}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 col-lg-4 col-xs-12 m-b-md">
                    <div class="r bg-info dker item hbox no-border">
                        <div class="col w-xs v-middle hidden-md">
                            <div class="sparkline inline"><i class="fa fa-money icon text-white-dker" style="font-size: 15px;"></i></div>
                        </div>
                        <div class="col dk padder-v r-r">
                            <div class="text-white-dk font-thin font-bold h6"><span>THIS YEAR REVENUE</span></div>
                            <span class="font-thin font-bold h6">{{number_format($totalrevinueinyear,2)}}</span>
                        </div>
                    </div>
                </div>
            </div>


            
            <!-- / stats -->

            <!-- service -->
            
            <!-- / service -->

            <!-- tasks -->
            
            <!-- / tasks -->

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