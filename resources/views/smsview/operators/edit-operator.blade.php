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
                  <h1 class="m-n font-thin h3 text-black font-bold">Operator Enrollment</h1>
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
              <div class="panel-heading font-bold">EDIT OPERATOR </div>
                
                <div class="panel-body">
                <form role="form" action="{{route('insert-operator')}}" id="smsoperator" method="post">
                    @csrf
                    <div class="form-group">
                      <div class="row">
                        <div class="col-md-12">
                          <label class="font-bold">Operator Name <span class="text-danger">*</span></label>
                            <input type="text" name="operator_name" id="operator_name" value="{{$operator->operator_name}}" class="form-control {{$errors->has('operator_name') ? 'border-danger': ''}}" placeholder="Enter Operator Name">
                            {{-- @if($errors->has('operator_name')) --}}
                                <label class="col-sm-12 font-bold text-danger control-label">{{$errors->first('operator_name')}}</label>
                            {{-- @endif --}}
                        </div>
                      </div> 
                    <div class="form-group" style="margin-top: 10px;">
                        <div class="row">     
                            <div class="col-md-6">
                                <label class="font-bold">Prefix <span class="text-danger">*</span></label>
                                <input type="text" name="operator_prefix" id="Operator_prefix" value="{{$operator->operator_prefix}}" class="form-control {{$errors->has('Operator_prefix') ? 'border-danger': ''}}" placeholder="Enter prefix">
                                {{-- @if($errors->has('operator_name')) --}}
                                    <label class="col-sm-12 font-bold text-danger control-label">{{$errors->first('Operator_prefix')}}</label>
                                {{-- @endif --}}
                            </div>
                            <div class="col-md-2" style="margin-top: 30px;">
                                <label class="font-bold">Status <span class="text-danger">*</span></label>
                            </div>
                            <div class="col-md-4" style="margin-top: 25px;">
                                
                                <input type="checkbox" name="status" id="status" @if($operator->status == 'y') value="{{$operator->status}}" checked @else value="n" @endif class="i-switch m-t-xs m-r form-control {{$errors->has('status')?'border-danger':''}}">
                                @if($errors->has('status'))
                                    <label class="col-sm-12 font-bold text-danger control-label">{{$errors->first('status')}}</label>
                                @endif
                            </div>
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