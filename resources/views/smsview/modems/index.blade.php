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
                  <h1 class="m-n font-thin h3 text-black font-bold">Modem Management</h1>
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
              <div class="panel-heading font-bold">ADD Modem</div>
                <div class="panel-body">
                <form role="form" action="{{route('add-modems')}}" method="post">
                    @csrf
                    <div class="form-group">
                      <div class="row">
                        <div class="col-md-12">
                          <label class="font-bold">Name <span class="text-danger">*</span></label>
                          <input type="text" name="name" id="name" value="{{ old('name')}}" class="form-control {{$errors->has('name') ? 'border-danger': ''}}" placeholder="Enter Modem Name">
                          {{-- @if($errors->has('name')) --}}
                              <label class="col-sm-12 font-bold text-danger control-label">{{$errors->first('name')}}</label>
                          {{-- @endif --}}
                        </div>
                      </div> 
                    </div>

                    <div class="form-group">
                      <div class="row">
                        <div class="col-md-12">
                          <label class="font-bold">Modem's Sim Number <span class="text-danger">*</span></label>
                            <input type="text" name="sim_number" id="sim_number" value="{{ old('sim_number')}}" class="form-control {{$errors->has('sim_number') ? 'border-danger': ''}}" placeholder="Enter Modem's Sim Number">
                            {{-- @if($errors->has('sim_number')) --}}
                                <label class="col-sm-12 font-bold text-danger control-label">{{$errors->first('sim_number')}}</label>
                            {{-- @endif --}}
                        </div>
                      </div> 
                    </div>

                    <div class="form-group">
                      <div class="row">
                        <div class="col-md-12">
                          <label class="font-bold">Modem Description <span class="text-danger">*</span></label>
                            <input type="text" name="description" id="description" value="{{ old('description')}}" class="form-control {{$errors->has('description') ? 'border-danger': ''}}" placeholder="Enter Modem Description">
                            {{-- @if($errors->has('description')) --}}
                                <label class="col-sm-12 font-bold text-danger control-label">{{$errors->first('description')}}</label>
                            {{-- @endif --}}
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