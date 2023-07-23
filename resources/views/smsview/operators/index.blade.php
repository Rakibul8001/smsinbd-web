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
              <div class="panel-heading font-bold">ADD OPERATOR</div>
                <div class="panel-body">
                <form role="form" action="{{route('insert-operator')}}" id="smsoperator" method="post">
                    @csrf
                    <div class="form-group">
                      <div class="row">
                        <div class="col-md-12">
                          <label class="font-bold">Operator Name <span class="text-danger">*</span></label>
                          <input type="text" name="name" id="name" value="{{ old('name')}}" class="form-control {{$errors->has('name') ? 'border-danger': ''}}" placeholder="Enter Operator Name">
                          {{-- @if($errors->has('name')) --}}
                              <label class="col-sm-12 font-bold text-danger control-label">{{$errors->first('name')}}</label>
                          {{-- @endif --}}
                        </div>
                      </div> 
                    </div>

                    <div class="form-group">
                      <div class="row">
                        <div class="col-md-12">
                          <label class="font-bold">Operator Prefix <span class="text-danger">*</span></label>
                            <input type="text" name="prefix" id="prefix" value="{{ old('prefix')}}" class="form-control {{$errors->has('prefix') ? 'border-danger': ''}}" placeholder="17 for GP, 18 for Robi. If multi then separate with comma like 17,13">
                            {{-- @if($errors->has('prefix')) --}}
                                <label class="col-sm-12 font-bold text-danger control-label">{{$errors->first('prefix')}}</label>
                            {{-- @endif --}}
                        </div>
                      </div> 
                    </div>



                    <div class="form-group">
                      <div class="row">
                        <div class="col-md-12">
                          <label class="font-bold">Operator Type <span class="text-danger">*</span></label>
                          <div class="row">
                            <div class="col-md-offset-3 col-md-3">
                              <div class="radio">
                                <label class="i-checks">
                                  <input type="radio" name="type"  value="gsm" checked>
                                  <i></i>
                                  GSM
                                </label>
                              </div>
                            </div>
                            <div class="col-md-3">
                              <div class="radio">
                                <label class="i-checks">
                                  <input type="radio" name="type" value="pstn">
                                  <i></i>
                                  PSTN/IPTSP
                                </label>
                              </div>
                            </div>
                          </div>
                          {{-- @if($errors->has('type')) --}}
                              <label class="col-sm-12 font-bold text-danger control-label">{{$errors->first('type')}}</label>
                          {{-- @endif --}}
                        </div>
                      </div> 
                    </div>

                    <div class="form-group">
                      <div class="row">
                        <div class="col-md-12">
                          <label class="font-bold">Single SMS URL<span class="text-danger">*</span></label>
                            <input type="text" name="single_url" id="single_url" value="{{ old('single_url')}}" class="form-control {{$errors->has('single_url') ? 'border-danger': ''}}" placeholder="Enter Single SMS URL">
                            {{-- @if($errors->has('single_url')) --}}
                                <label class="col-sm-12 font-bold text-danger control-label">{{$errors->first('single_url')}}</label>
                            {{-- @endif --}}
                        </div>
                      </div> 
                    </div>

                    <div class="form-group">
                      <div class="row">
                        <div class="col-md-12">
                          <label class="font-bold">Multiple SMS URL<span class="text-danger">*</span></label>
                            <input type="text" name="multi_url" id="multi_url" value="{{ old('multi_url')}}" class="form-control {{$errors->has('multi_url') ? 'border-danger': ''}}" placeholder="Enter Multiple SMS URL">
                            {{-- @if($errors->has('multi_url')) --}}
                                <label class="col-sm-12 font-bold text-danger control-label">{{$errors->first('multi_url')}}</label>
                            {{-- @endif --}}
                        </div>
                      </div> 
                    </div>


                    <div class="form-group">
                      <div class="row">
                        <div class="col-md-12">
                          <label class="font-bold">SMS Delivery Report URL</label>
                            <input type="text" name="delivery_url" id="delivery_url" value="{{ old('delivery_url')}}" class="form-control {{$errors->has('delivery_url') ? 'border-danger': ''}}" placeholder="Enter SMS Delivery Report URL">
                            {{-- @if($errors->has('delivery_url')) --}}
                                <label class="col-sm-12 font-bold text-danger control-label">{{$errors->first('delivery_url')}}</label>
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