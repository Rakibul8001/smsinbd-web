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
                @if(Auth::guard('root')->check())
                  <h1 class="m-n font-thin h3 text-black font-bold">Root Admin Panel</h1>
                @elseif(Auth::guard('manager')->check())
                  <h1 class="m-n font-thin h3 text-black font-bold">Manager Admin Panel</h1>
                @elseif(Auth::guard('reseller')->check())
                  <h1 class="m-n font-thin h3 text-black font-bold">Reseller Admin Panel</h1>
                @elseif(Auth::guard('client')->check())
                  <h1 class="m-n font-thin h3 text-black font-bold">Client Admin Panel</h1>
                @endif
                <small class="text-muted">Welcome to SMSBD Application</small>
              </div>
              <div class="col-sm-6 col-xs-12 text-right hidden-xs">
                  <h1 class="m-n font-thin h3 text-black font-bold">Update Divider</h1>
                  <small class="text-muted"></small>
              </div>
            </div>
        </div>
        <!-- / main header -->
          <div class="row m-t-md">
            <div class="col-md-7 col-md-offset-3">
              @if(session()->has('msg'))
                <div class="alert alert-success font-weight-bold clientsuccess" role="alert">
                  {{session()->get('msg')}}
                </div>
              @endif
              <div class="panel panel-default">
              <div class="panel-heading font-bold">EDIT Divider </div>
                
                <div class="panel-body">
                <form role="form" action="{{route('update-divider',[$divider->id])}}" method="post">
                    @csrf
                    <div class="form-group">
                      <div class="row">
                        <div class="col-md-12">
                          <label class="font-bold">Divider Name <span class="text-danger">*</span></label>
                            <input type="text" name="divider" id="divider" value="{{$divider->divider}}" class="form-control {{$errors->has('divider') ? 'border-danger': ''}}" placeholder="Enter Divider" required>
                            {{-- @if($errors->has('operator_name')) --}}
                                <label class="col-sm-12 font-bold text-danger control-label">{{$errors->first('divider')}}</label>
                            {{-- @endif --}}
                        </div>
                      </div> 
                  
                    <div class="form-group">
                      <div class="row">
                       
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-primary btn-addon btn-md pull-right"><i class="fa fa-plus"></i> Submit</button>
                        </div>
                      </div>
                    </div>
                  </form>
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