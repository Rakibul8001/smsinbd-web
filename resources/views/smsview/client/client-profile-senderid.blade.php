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
                              height: 50px;
                              width: 50px;
                              text-align: center;
                              line-height: 49px;
                              font-size: 21px;
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
                    
                </div>
                </div>
            </div>
            </div>
            <div class="wrapper bg-white b-b">
            @include('smsview.client.profiletab')
            </div>
            <div class="padder">    
                @if(session()->has('msg'))
                    <div class="alert alert-success font-weight-bold clientsuccess" role="alert">
                    {{session()->get('msg')}}
                    </div>
                @endif  
                <div class="alert alert-danger font-weight-bold clientunsuccess" style="display:none;" role="alert"></div>
                <div>
                <div class="container profile-senderid" style="margin-top:30px;" data-current_profileid="{{$request->userid}}">
                                <div class="row">
                                    <div class="col-md-12 col-xs-12">
                                        <div class="panel panel-default">
                                        <div class="panel-heading font-bold">Assigned Senderid</div>
                                            <div class="panel-body"> 
                                                <table class="clientassignsenderid table table-striped table-hover dt-responsive display nowrap no-footer dtr-inline dataTable collapsed" style="width: 100%;" role="grid" aria-describedby="example_info">
                                                <thead>
                                                    <tr>
                                                    <th>ID</th>
                                                    <th>SenderID</th>
                                                    <th>Status</th>
                                                    <th>Default</th>
                                                    <th>Created On</th>
                                                    </tr>
                                                </thead>
                                                <tfoot>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>SenderID</th>
                                                    <th>Status</th>
                                                    <th>Default</th>
                                                    <th>Created On</th>
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
            </div>
        </div>
        <!-- / main -->
        </div>



        </div>
        </div>
        <!-- /content -->
        
@endsection