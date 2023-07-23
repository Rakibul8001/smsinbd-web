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
                  <h1 class="m-n font-thin h3 text-black font-bold">Manage Client Sender IDs</h1>
                  <small class="text-muted"></small>
              </div>
            </div>
        </div>

        <!-- / main header -->

        <!-- Modal -->
        <div class="modal bd-example-modal-md fade" id="smsSenderIdTeletalkAddForm" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
          <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title font-bold senderidhead" id="exampleModalLabel">Add SMS Sender ID</h5>
               
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="margin-top: -22px;">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <form role="form" action="{{ route('add-sms-senderid') }}" id="smssenderaddteletalk" method="post">
                @csrf
              <div class="modal-body">
                <div class="panel panel-default">
                  <div class="panel-heading font-bold">Teletalk Sender ID Information</div>
                    
                    <div class="panel-body">
                        <input type="hidden" name="sendertype" id="sendertype_teletalk" value="teletalk">
                        <div class="form-group">
                          <!-- <div class="row">
                            <div class="col-md-offset-3 col-md-3">
                              <div class="radio">
                                <label class="i-checks">
                                  <input type="radio" name="sendertype" id="sendertype_general" value="general" checked>
                                  <i></i>
                                  General
                                </label>
                              </div>
                            </div>
                            <div class="col-md-3">
                              <div class="radio">
                                <label class="i-checks">
                                  <input type="radio" name="sendertype" id="sendertype_taletalk" value="talitalk">
                                  <i></i>
                                  TaliTalk
                                </label>
                              </div>
                            </div>
                          </div> -->
                          
                          <div class="row">
                            <div class="col-md-12">
                              <label class="font-bold">Master Sender ID <span class="text-danger">*</span></label>
                                <input type="text" name="sender_name" id="sender_name_teletalk" value="{{old('sender_name')}}" class="form-control" placeholder="Enter Operator Name">
                                
                            </div>
                          </div> 
                        </div>
                        


                        <div class="form-group talitalk-senderid" style="margin-top: 10px;">
                          <table class="table table-bordered table-striped" style="width: 100%;" role="grid" aria-describedby="example_info">
                            <thead>
                              <tr>
                                <th>Operator Name</th>
                                <th>User</th>
                                <th>Password</th>
                              </tr>
                            </thead>
                            <tbody>
                            
            
                              <tr>
                                <td><?php $i = 0;  ?> @foreach($operators as $operator) @foreach($gateways as $gateway) @if($operator->operator_name == 'Teletalk') <?php if($i == 0) { ?> <input type="hidden" name="sender_operator_id" value="{{$operator->id}}"/>{{$operator->operator_name}} <?php $i = 1; } ?> @endif @endforeach @endforeach</td>
                                <td>
                                <input type="text" name="sender_user" value="{{old('sender_user')}}" id="sender_user" class="form-control" placeholder="Enter user">
                                </td>
                                <td>
                                <input type="text" name="sender_password" value="{{old('sender_password')}}" id="sender_password" class="form-control" placeholder="Enter password">
                                </td>
                              </tr>
                            </tbody>
                            <tfoot>
                            <tr>
                                <th>Operator Name</th>
                                <th>User</th>
                                <th>Password</th>
                              </tr>
                            </tfoot>
                            <tbody>
                            </tbody>
                          </table>
                            
                        </div>

                        <div class="form-group row">
                          <label for="status" class="col-sm-2 font-bold" style="margin-top: 10px;">Publish</label>
                          <div class="col-md-2">
                              
                              <div class="radio">
                                
                                <label class="i-checks">
                                  <input type="radio" name="status" id="senderid_status_yes" value="1" checked>
                                  <i></i>
                                  Yes
                                </label>
                              </div>
                            </div>
                            <div class="col-md-2">
                              <div class="radio">
                                <label class="i-checks">
                                  <input type="radio" name="status" id="senderid_status_no" value="0">
                                  <i></i>
                                  No
                                </label>
                              </div>
                            </div>

                          <label for="default" class="col-sm-2 font-bold" style="margin-top: 10px;">Default</label>
                          <div class="col-md-2">
                              
                              <div class="radio">
                                
                                <label class="i-checks">
                                  <input type="radio" name="default" id="senderid_default_yes" value="1">
                                  <i></i>
                                  Yes
                                </label>
                              </div>
                            </div>
                            <div class="col-md-2">
                              <div class="radio">
                                <label class="i-checks">
                                  <input type="radio" name="default" id="senderid_default_no" value="0" checked>
                                  <i></i>
                                  No
                                </label>
                              </div>
                            </div>
                        </div>
                        
                        
                    </div>
                  </div>
              </div>
              <div class="modal-footer">
                <input type="hidden" name="smssender_rec_id" id="smssender_rec_id"/>
                <input type="hidden" name="frmmode" id="frmmode" value="ins"/>
                <button type="submit" class="btn btn-primary btn-addon btn-md senderidbtn"><i class="fa fa-save"></i> Submit</button>
                
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
              </div>
            </form>
            </div>
          </div>
        </div>



        <div class="modal bd-example-modal-md fade" id="smsSenderIdAddForm" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
          <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title font-bold senderidhead" id="exampleModalLabel">Add SMS Sender ID</h5>
               
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="margin-top: -22px;">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <form role="form" action="{{ route('add-sms-senderid') }}" id="smssenderadd" method="post">
                @csrf
              <div class="modal-body">
                <div class="panel panel-default">
                  <div class="panel-heading font-bold">Sender ID Information</div>
                    <input type="hidden" name="sendertype" id="sendertype_general" value="general">
                    <div class="panel-body">
                    
                        <div class="form-group">
                          <!-- <div class="row">
                            <div class="col-md-offset-3 col-md-3">
                              <div class="radio">
                                <label class="i-checks">
                                  <input type="radio" name="sendertype" id="sendertype_general" value="general" checked>
                                  <i></i>
                                  General
                                </label>
                              </div>
                            </div>
                            <div class="col-md-3">
                              <div class="radio">
                                <label class="i-checks">
                                  <input type="radio" name="sendertype" id="sendertype_taletalk" value="talitalk">
                                  <i></i>
                                  TaliTalk
                                </label>
                              </div>
                            </div>
                          </div> -->
                          
                          <div class="row">
                            <div class="col-md-12">
                              <label class="font-bold">Master Sender ID <span class="text-danger">*</span></label>
                                <input type="text" name="sender_name" id="sender_name" value="{{old('sender_name')}}" class="form-control" placeholder="Enter Operator Name">
                                
                            </div>
                          </div> 
                        </div>
                        <div class="form-group general-senderid" style="margin-top: 10px;">
                          <table class="table table-bordered table-striped" style="width: 100%;" role="grid" aria-describedby="example_info">
                            <thead>
                              <tr>
                                <th>Operator Name</th>
                                <th>Associate ID</th>
                                <th>Gateway</th>
                              </tr>
                            </thead>
                            <tbody>
                            <?php $i = 0; ?>
                            @foreach($operators as $operator)
            
                              <tr>
                                <td><input type="hidden" name="sender_operator_name[]" value="{{$operator->operator_name}}"/>
                                <input type="hidden" name="edit_associate_id[]" value="{{$operator->id}}"/>{{$operator->operator_name}}</td>
                                <td><input type="text" name="associate_sender_id[]"  value="{{old('sender_name'.$i)}}" id="associate_sender_id{{$operator->id}}" class="form-control" placeholder="Enter associate sender id"></td>
                                <td>
                                  <select name="gateway[]" id="gateway{{$operator->id}}" class="form-control">
                                    <option selected value="{{old('gateway'.$i)}}">Select gateway</option>
                                    @foreach($gateways as $gateway)
                                      <option value="{{$gateway->id}}">{{$gateway->gateway_name}}</option>
                                    @endforeach
                                  </select>
                                </td>
                              </tr>
                              
                            @endforeach
                            </tbody>
                            <tfoot>
                            <tr>
                                <th>Operator Name</th>
                                <th>Associate ID</th>
                                <th>Gateway</th>
                              </tr>
                            </tfoot>
                            <tbody>
                            </tbody>
                          </table>
                            
                        </div>


                        

                        <div class="form-group row">
                          <label for="status" class="col-sm-2 font-bold" style="margin-top: 10px;">Publish</label>
                          <div class="col-md-2">
                              
                              <div class="radio">
                                
                                <label class="i-checks">
                                  <input type="radio" name="status" id="senderid_status_yes" value="1" checked>
                                  <i></i>
                                  Yes
                                </label>
                              </div>
                            </div>
                            <div class="col-md-2">
                              <div class="radio">
                                <label class="i-checks">
                                  <input type="radio" name="status" id="senderid_status_no" value="0">
                                  <i></i>
                                  No
                                </label>
                              </div>
                            </div>

                          <label for="default" class="col-sm-2 font-bold" style="margin-top: 10px;">Default</label>
                          <div class="col-md-2">
                              
                              <div class="radio">
                                
                                <label class="i-checks">
                                  <input type="radio" name="default" id="senderid_default_yes" value="1">
                                  <i></i>
                                  Yes
                                </label>
                              </div>
                            </div>
                            <div class="col-md-2">
                              <div class="radio">
                                <label class="i-checks">
                                  <input type="radio" name="default" id="senderid_default_no" value="0" checked>
                                  <i></i>
                                  No
                                </label>
                              </div>
                            </div>
                        </div>
                        
                        
                    </div>
                  </div>
              </div>
              <div class="modal-footer">
                <input type="hidden" name="smssender_rec_id" id="smssender_rec_id"/>
                <input type="hidden" name="frmmode" id="frmmode" value="ins"/>
                <button type="submit" class="btn btn-primary btn-addon btn-md senderidbtn"><i class="fa fa-save"></i> Submit</button>
                
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
              </div>
            </form>
            </div>
          </div>
        </div>

        <div class="modal bd-example-modal-md fade" id="assignsenderid" tabindex="-1" role="dialog" aria-labelledby="operatorapiModalLabel" aria-hidden="true">
          <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title font-bold" id="operatorapiModalLabel">Assign Sender Id</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="margin-top: -22px;">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <form role="form" action="{{route('assign-client-senderid')}}" id="smsoperatorapi" method="post">
                @csrf
                <div class="load-senderid"></div>
              <div class="modal-footer">
                <input type="hidden" name="client_sender_id" id="client_sender_id"/>
                <input type="hidden" name="client_sender_name" id="client_sender_name"/>
                <button type="submit" class="btn btn-primary btn-addon btn-md"><i class="fa fa-plus"></i> Submit</button>
                
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
              </div>
            </form>
            </div>
          </div>
        </div>



        <div class="wrapper-md" ng-controller="FlotChartDemoCtrl">
            @if(Auth::guard('root')->check())
            <a class="btn btn-primary btn-addon btn-md pull-right mb-5 addsenderid" style="margin-bottom:10px; z-index: 99; position:relative;" data-toggle="modal" data-target="#smsSenderIdAddForm"><i class="fa fa-plus"></i> Create General Senderid</a>
            @endif
            <div class="row">
             
              <div class="col-md-12">
                  @if(session()->has('msg'))
                    <div class="alert alert-success font-weight-bold clientsuccess" role="alert">
                      {{session()->get('msg')}}
                    </div>
                  @endif 

                  @if(session()->has('errmsg'))
                    <div class="alert alert-danger font-weight-bold clientsuccess" role="alert">
                      {{session()->get('errmsg')}}
                    </div>
                  @endif 
                  <div class="panel panel-default dataTables_wrapper">
                      <div class="panel-heading font-bold">
                      @if(Auth::guard('root')->check())
                        <a href="{{route('teletalk-sms-senderid',['senderidtype'=>'teletalk'])}}" class="btn btn-primary btn-addon btn-md mb-5" style="margin-bottom:10px; z-index: 99; position:relative; margin-right:20px;"><i class="fa fa-list"></i> Teletalk Senderid</a>
                      @endif
                      </div>
                  
                      <div class="table-responsive">
                        <table ui-jq="dataTable" id="smssender" class="smssender display nowrap dataTable dtr-inline collapsed" style="width: 100%;" role="grid" aria-describedby="example_info">
                          <thead>
                            <tr>
                              <th>ID</th>
                              <th>Sender ID</th>
                              <th>Operator</th>
                              <th>Status</th>
                              <th>Default</th>
                              <th>User</th>
                              <th>Password</th>
                              <th>Created By</th>
                              <th>Updated By</th>
                              <th class="actions">Action</th>
                            </tr>
                          </thead>
                          <tfoot>
                          <tr>
                              <th>ID</th>
                              <th>Sender ID</th>
                              <th>Operator</th>
                              <th>Status</th>
                              <th>Default</th>
                              <th>User</th>
                              <th>Password</th>
                              <th>Created By</th>
                              <th>Updated By</th>
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