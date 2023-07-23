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
            <div class="col-sm-6 col-xs-12 text-right hidden-xs">
                <h1 class="m-n font-thin h3 text-black font-bold">Client Profile</h1>
                <small class="text-muted"></small>
            </div>
            <div class="col-sm-6 text-right hidden-xs">
                
            </div>
            </div>
        </div>
        <!-- / main header -->
        <div>
            <div class="wrapper-lg bg-white-opacity">
                <div class="row m-t">
                <div class="col-sm-7">
                    <a href class="thumb-lg pull-left m-r" style="border: 1px solid #ddd;
                                    border-radius: 100%;
                                    height: 70px;
                                    width: 70px;
                                    text-align: center;
                                    line-height: 67px;
                                    font-size: 24px;
                                    background-color: #ddd;">
                    <i class="icon-user"></i>
                    </a>
                    <div class="clear m-b">
                    <div class="m-b m-t-sm">
                        <span class="h3 text-black">{{$user->name}}</span>
                        <small class="m-l">{{$user->address}}</small>
                    </div>
                    </div>
                </div>
                <div class="col-sm-5">
                    <div class="pull-right pull-none-xs text-center">
                    <a href class="m-b-md inline m">
                        <span class="h3 block font-bold">Mask</span>
                        <small>{{$totalmaskbal}}</small>
                    </a>
                    <a href class="m-b-md inline m">
                        <span class="h3 block font-bold">Non Mask</span>
                        <small>{{$totalnonmaskbal}}</small>
                    </a>
                    <a href class="m-b-md inline m">
                        <span class="h3 block font-bold">Voice</span>
                        <small>{{$totalvoicebal}}</small>
                    </a>
                    </div>
                </div>
                </div>
            </div>
            </div>
            <div class="wrapper bg-white b-b">
            @include('smsview.reseller.profiletab')
            </div>
            <div class="padder" style="margin-top: 20px;">     
                <div class="row">
                    <div class="col-md-5">
                       
                        <div class="row row-sm text-center">
                            <div class="col-xs-6">
                            <div class="panel padder-v item">
                                <div class="h1 text-info font-thin h1">{{$todayssmssent}}</div>
                                <span class="text-muted text-xs">Today's Sms Sent</span>
                                
                            </div>
                            </div>
                            <div class="col-xs-6">
                            <a href class="block panel padder-v bg-primary item">
                                <span class="text-white font-thin h1 block">{{$thisweekssmssent}}</span>
                                <span class="text-muted text-xs">This Week Sms Sent</span>
                                
                            </a>
                            </div>
                            
                            <div class="col-xs-6 m-b-md">
                            <div class="r bg-info dker item hbox no-border">
                                
                                <div class="col dk padder-v r-r">
                                <div class="text-white font-thin h1 block"><span>{{$totalusers}}</span></div>
                                <span class="text-muted text-xs">Total Clients</span>
                                </div>
                            </div>
                            </div>
                            <div class="col-xs-6 m-b-md">
                            <div class="r bg-light dker item hbox no-border">
                                
                                <div class="col dk padder-v r-r">
                                <div class="text-primary-dk font-thin h1"><span>{{$thismonthsmssent}}</span></div>
                                <span class="text-muted text-xs">This Month Sms Sent</span>
                                </div>
                            </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-7">
                        <div class="panel panel-default">
                        <div class="panel-heading font-bold">Monthly Sms Sent</div>
                        <div class="panel-body">
                            <div id="barchart_material" style="width: 540px; height: 300px;"></div>
                        </div>
                        </div>
                    </div>
                    
                </div>
            </div>
        <!-- / main -->
        </div>



        </div>
        </div>
        <!-- /content -->

@endsection