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
            @if(Auth::guard('reseller')->check())  
              @include('smsview.rootadmin.profiletab-reseller-client')
            @else 
              @include('smsview.rootadmin.profiletab')
            @endif
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
                                    <div class="col-md-6 col-xs-12">
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

                                    <div class="col-md-5 col-xs-12">
                                        <div class="panel panel-default">
                                            <div class="panel-heading font-bold">Senderid List 
                                                <span class="pull-right">
                                                    <input type="text" class="form-control searchsenderid" name="search" data-current_profileid="{{$request->userid}}" placeholder="search senderid" style="margin-top: -7px; margin-left: 12px;">
                                                </span>
                                            </div>
                                            <div class="panel-body"> 
                                                <form action="{{route('assign-senderids-to-client')}}" method="post">
                                                @csrf
                                                <input type="hidden" name="userid" value="{{$request->userid}}"/>
                                                <div style="width:100%; height: 175px; overflow-y:auto;">
                                                    
                                                        <table class="table table-border stripped tableprofilesenderid">
                                                            <thead>
                                                                <tr>
                                                                    <th>#</th>
                                                                    <th>Sender Name</th>
                                                                </tr>
                                                            </thead>
                                                            

                                                                <tbody>
                                                                    @foreach($senderids as $senderid)
                                                                    <tr>
                                                                        <td><input type="checkbox" name="sms_sender_id[]" value="<?php echo $senderid->id; ?>"></td>
                                                                        <td>{{$senderid->sender_name}}</td>
                                                                    </tr>
                                                                    @endforeach
                                                                </tbody>

                                                            
                                                        </table>
                                                       
                                                </div> 
                                                <button type="submit" class="btn btn-sm btn-primary pull-right" style="margin-top:10px;">Assign</button>
                                            </form>
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