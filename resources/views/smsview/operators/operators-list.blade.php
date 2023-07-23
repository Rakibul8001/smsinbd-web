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
                  <h1 class="m-n font-thin h3 text-black font-bold">Operator List</h1>
                  <small class="text-muted"></small>
              </div>
            </div>
        </div>

        <!-- / main header -->

        <!-- Modal -->
        <div class="modal bd-example-modal-lg fade" id="operatorlistModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
          <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title font-bold" id="exampleModalLabel">Edit Operator</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="margin-top: -22px;">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <form role="form" action="{{ route('edit-operator') }}" id="smsoperatoredit" method="post">
                @csrf
              <div class="modal-body">
                <div class="panel panel-default">
                  <div class="panel-heading font-bold">OPERATOR INFORMATION</div>
                    
                    <div class="panel-body">
                    
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

                        <div class="form-group" style="margin-top: 10px;">
                          <div class="row">     
                              <div class="col-md-2">
                                <label class="font-bold">Enabled</label>
                              </div>
                              <div class="col-md-2">
                                <input type="checkbox" name="active" id="active" value="" class="i-switch m-t-xs m-r form-control">
                              </div>
                          </div>
                        </div>
                        
                      
                    </div>
                  </div>
              </div>
              <div class="modal-footer">
                <input type="hidden" name="operator_id" id="operator_id" value="">
                <button type="submit" class="btn btn-primary btn-addon btn-md"><i class="fa fa-plus"></i> Submit</button>
                
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
              </div>
            </form>
            </div>
          </div>
        </div>

        <div class="modal bd-example-modal-lg fade" id="operatorapiModal" tabindex="-1" role="dialog" aria-labelledby="operatorapiModalLabel" aria-hidden="true">
          <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title font-bold" id="operatorapiModalLabel">Operator Gateway</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="margin-top: -22px;">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <form role="form" action="{{route('insert-operator')}}" id="smsoperatorapi" method="post">
                @csrf
              <div class="modal-body">
                <div class="panel panel-default">
                  <div class="panel-heading font-bold">OPERATOR GATEWAY INFORMATION</div>
                    
                    <div class="panel-body">
                    
                        <div class="form-group">
                          <div class="row">
                            <div class="col-md-12">
                              <label class="font-bold">Operator Name <span class="text-danger">*</span></label>
                              <input type="text" name="operator_name" id="operator_name_api" class="form-control" placeholder="Enter Operator Name">
                                
                            </div>
                          </div> 
                        </div>
                        <div class="form-group" style="margin-top: 10px;">
                            <div class="row">     
                                <div class="col-md-6">
                                    <label class="font-bold">Gateway User <span class="text-danger">*</span></label>
                                    <input type="text" name="user" id="user" class="form-control" placeholder="Enter gateway user name">
                                    
                                </div>
                                <div class="col-md-6">
                                  <label class="font-bold">Gateway Password <span class="text-danger">*</span></label>
                                  <input type="text" name="password" id="password" class="form-control" placeholder="Enter gateway password">
                                  
                                </div>
                            </div>
                        </div>

                        <div class="form-group" style="margin-top: 10px;">
                          <div class="row">     
                              <div class="col-md-8">
                                  <label class="font-bold">API Url <span class="text-danger">*</span></label>
                                  <input type="text" name="api_url" id="api_url" class="form-control" placeholder="Enter api url">
                                  
                              </div>
                              <div class="col-md-2" style="margin-top: 30px;">
                                <label class="font-bold">Status </label>
                              </div>
                              <div class="col-md-2" style="margin-top: 25px;">
                                <input type="checkbox" name="api_status" id="api_status" class="i-switch m-t-xs m-r form-control">
                              </div>
                          </div>
                        </div>
                        
                      
                    </div>
                  </div>
              </div>
              <div class="modal-footer">
                <input type="hidden" name="operator_id" id="operator_id"/>
                <input type="hidden" name="gateway_id" id="gateway_id"/>
                <button type="submit" class="btn btn-primary btn-addon btn-md"><i class="fa fa-plus"></i> Submit</button>
                
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
              </div>
            </form>
            </div>
          </div>
        </div>

        

        <div class="wrapper-md" ng-controller="FlotChartDemoCtrl">
            <a class="btn btn-primary btn-addon btn-md pull-right mb-5" style="margin-bottom:10px; z-index: 99; position:relative;" href="{{route('add-operator')}}"><i class="fa fa-plus"></i> Create New</a>
            <div class="row">
             
              <div class="col-md-12">
                   @if(session()->has('msg'))
                    <div class="alert alert-success font-weight-bold clientsuccess" role="alert">
                      {{session()->get('msg')}}
                    </div>
                  @endif 
                  <div class="panel panel-default dataTables_wrapper">
                      <div class="panel-heading font-bold">
                        SMS Operators List
                        
                      </div>
                      <div class="table-responsive">
                        <table class="display dataTable dtr-inline " style="width: 100%;" role="grid" aria-describedby="example_info">
                          <thead>
                            <tr>
                              <th>Name</th>
                              <th>Prefix</th>
                              <th>Single SMS URL</th>
                              <th>Multi SMS URL</th>
                              <th>Delivery Report URL</th>
                              <th>Status</th>
                              <th class="actions">Action</th>
                            </tr>
                          </thead>

                          <tbody>
                            @foreach($operators as $key => $operator)
                            <tr>
                              <td>{{ $operator->name }}</td>
                              <td>{{ $operator->prefix }}</td>
                              <td>{{ substr($operator->single_url, 0, 30) }}...</td>
                              <td>{{ substr($operator->multi_url, 0, 30) }}...</td>
                              <td>{{ substr($operator->delivery_url, 0, 30)}}...</td>
                              <td>{{ $operator->active==1 ? 'Active' : 'Disabled' }}</td>
                              <td>
                                  <a href="#" class="btn btn-sm btn-icon btn-pure btn-default operatoredtfrm"  data-toggle="modal" data-target="#operatorlistModal" data-original-title="edit" data-id="{{ $operator->id }}" data-operatorname="{{ $operator->name }}" data-type="{{ $operator->type }}" data-operatorprefix="{{ $operator->prefix }}" data-single_url="{{ $operator->single_url }}" data-multi_url="{{ $operator->multi_url }}" data-delivery_url="{{ $operator->delivery_url }}" data-status="{{ $operator->active }}"><i class="icon icon-pencil" aria-hidden="true"></i></a>
                              </td>
                            </tr>
                            @endforeach
                          </tbody>
                          
                          <tfoot>
                            <tr>
                              <th>Name</th>
                              <th>Prefix</th>
                              <th>Single SMS URL</th>
                              <th>Multi SMS URL</th>
                              <th>Delivery Report URL</th>
                              <th>Status</th>
                              <th class="actions">Action</th>
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