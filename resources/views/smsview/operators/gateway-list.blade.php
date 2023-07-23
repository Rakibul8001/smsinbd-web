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
                  <h1 class="m-n font-thin h3 text-black font-bold">Gateway List</h1>
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
                                <input type="text" name="operator_name" id="operator_name" class="form-control" placeholder="Enter Operator Name">
                                
                            </div>
                          </div> 
                        </div>
                        <div class="form-group" style="margin-top: 10px;">
                            <div class="row">     
                                <div class="col-md-6">
                                    <label class="font-bold">Prefix <span class="text-danger">*</span></label>
                                    <input type="text" name="operator_prefix" id="operator_prefix" class="form-control" placeholder="Enter prefix">
                                    
                                </div>
                                <div class="col-md-2" style="margin-top: 30px;">
                                    <label class="font-bold">Status <span class="text-danger">*</span></label>
                                </div>
                                <div class="col-md-4" style="margin-top: 25px;">
                                    
                                    <input type="checkbox" name="status" id="status" class="i-switch m-t-xs m-r form-control">
                                    <input type="hidden" id="id" name="id"/>
                                    <input type="hidden" id="created_by" name="created_by"/>
                                </div>
                            </div>
                        </div>
                        
                      
                    </div>
                  </div>
              </div>
              <div class="modal-footer">
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
              <form role="form" action="{{route('add-operator-gateway')}}" id="smsoperatorapi" method="post">
                @csrf
              <div class="modal-body">
                <div class="panel panel-default">
                  <div class="panel-heading font-bold">OPERATOR GATEWAY INFORMATION</div>
                    
                    <div class="panel-body">
                    
                        <div class="form-group">
                          <div class="row">
                            <div class="col-md-12">
                              <label class="font-bold">Operator <span class="text-danger">*</span></label>
                              <select name="operator_id" id="operator_id" class="form-control" required>
                                <option value="">Select One operaton</option>
                                @foreach($operators as $operator)
                                  <option value="{{ $operator->id }}">{{ $operator->name }}</option>
                                @endforeach
                              </select>
                                
                            </div>
                          </div> 
                        </div>


                        <div class="form-group">
                          <div class="row">
                            <div class="col-md-12">
                              <label class="font-bold">Gateway Name <span class="text-danger">*</span></label>
                              <input type="text" name="gateway_name" id="gateway_name" class="form-control" placeholder="Enter Gateway Name" required>
                                
                            </div>
                          </div> 
                        </div>
                        <div class="form-group" style="margin-top: 10px;">
                            <div class="row">     
                                <div class="col-md-6">
                                    <label class="font-bold">Gateway Username <span class="text-danger"></span></label>
                                    <input type="text" name="username" id="user" class="form-control" placeholder="Enter gateway username">
                                    
                                </div>
                                <div class="col-md-6">
                                  <label class="font-bold">Gateway Password <span class="text-danger"></span></label>
                                  <input type="text" name="password" id="password" class="form-control" placeholder="Enter gateway password">
                                  
                                </div>
                            </div>
                        </div>

                        <div class="form-group" style="margin-top: 10px;">
                          <div class="row">     
                              <div class="col-md-2">
                                <label class="font-bold">Enabled</label>
                              </div>
                              <div class="col-md-2">
                                <input type="checkbox" name="active" id="active" class="i-switch m-t-xs m-r form-control">
                              </div>
                          </div>
                        </div>
                        
                      
                    </div>
                  </div>
              </div>
              <div class="modal-footer">
                <input type="hidden" name="gateway_id" id="gateway_id" value="">
                <button type="submit" class="btn btn-primary btn-addon btn-md"><i class="fa fa-plus"></i> Submit</button>
                
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
              </div>
            </form>
            </div>
          </div>
        </div>

        

        <div class="wrapper-md" ng-controller="FlotChartDemoCtrl">
            <a class="btn btn-primary btn-addon btn-md pull-right mb-5 insgateway" style="margin-bottom:10px; z-index: 99; position:relative;" data-toggle="modal" data-target="#operatorapiModal" href="#"><i class="fa fa-plus"></i> Create New</a>
            <div class="row">
             
              <div class="col-md-12">
                   @if(session()->has('msg'))
                    <div class="alert alert-success font-weight-bold clientsuccess" role="alert">
                      {{session()->get('msg')}}
                    </div>
                    @endif
                    @if(session()->has('errmsg'))
                    <div class="alert alert-danger font-weight-bold clientunsuccess" role="alert">
                      {{session()->get('errmsg')}}
                    </div>
                    @endif 
                  <div class="panel panel-default dataTables_wrapper">
                      <div class="panel-heading font-bold">
                        SMS Gateway List
                        
                      </div>
                      <div class="table-responsive">
                        <table ui-jq="dataTable" class=" display nowrap dataTable dtr-inline collapsed" style="width: 100%;" role="grid" aria-describedby="example_info">
                          <thead>
                            <tr>
                              <th>SL</th>
                              <th>Operator</th>
                              <th>Name</th>
                              <th>Username</th>
                              <th>Password</th>
                              <th>Status</th>
                              <th class="actions">Action</th>
                            </tr>
                          </thead>
                          <tbody>
                            @foreach($gateways as $key => $gateway)
                              <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>{{ $gateway->operator->name }}</td>
                                <td>{{ $gateway->name }}</td>
                                <td>{{ $gateway->username }}</td>
                                <td>{{ $gateway->password }}</td>
                                <td>{{ $gateway->active==1 ? 'Active' : 'Disabled' }}</td>
                                <td>
                                  <a href="#" class="btn btn-sm btn-icon btn-pure btn-default operatorapifrm"  data-toggle="modal" data-target="#operatorapiModal" data-original-title="edit" data-id="{{ $gateway->id }}" data-operator="{{ $gateway->operator_id }}" data-gatewayname="{{ $gateway->name }}" data-gatewayuser="{{ $gateway->username }}" data-gatewaypassword="{{ $gateway->password }}"  data-gatewaystatus="{{ $gateway->active }}"><i class="icon icon-pencil" aria-hidden="true"></i></a>
                                </td>
                              </tr>
                            @endforeach
                          </tbody>
                          <tfoot>
                          <tr>
                              <th>SL</th>
                              <th>Operator</th>
                              <th>Name</th>
                              <th>Username</th>
                              <th>Password</th>
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