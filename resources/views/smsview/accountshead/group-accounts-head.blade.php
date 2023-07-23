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
                  <h1 class="m-n font-thin h3 text-black font-bold">Manage Group Account's Head</h1>
                  <small class="text-muted"></small>
              </div>
            </div>
        </div>

        <!-- / main header -->

        <!-- Modal -->
        <div class="modal bd-example-modal-md fade" id="addrootaccountshead" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
          <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title font-bold rootaccountidhead" id="exampleModalLabel">Add Root Accounts</h5>
               
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="margin-top: -22px;">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <form role="form" action="{{ route('add-accounts-head') }}" id="smssenderadd" method="post">
                @csrf
              <div class="modal-body">
                <div class="panel panel-default">
                  <div class="panel-heading font-bold">Account's Information</div>
                    
                    <div class="panel-body">
                    
                        <div class="form-group">
                       
                          
                          <div class="row">
                            <div class="col-md-12">
                              <label class="font-bold">Account's Head <span class="text-danger">*</span></label>
                                <input type="text" name="acc_head" id="acc_head" value="{{old('acc_head')}}" class="form-control {{$errors->has('acc_head') ? 'border-danger' :  ''}}" placeholder="Enter accounts head">
                                
                            </div>
                          </div> 
                        </div>
                        

                        <div class="form-group row">
                          <label for="status" class="col-sm-2 font-bold" style="margin-top: 10px;">Publish</label>
                          <div class="col-md-2">
                              
                              <div class="radio">
                                
                                <label class="i-checks">
                                  <input type="radio" name="status" id="status_yes" value="1" checked>
                                  <i></i>
                                  Yes
                                </label>
                              </div>
                            </div>
                            <div class="col-md-2">
                              <div class="radio">
                                <label class="i-checks">
                                  <input type="radio" name="status" id="status_no" value="0">
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
                <input type="hidden" name="account_rec_id" id="account_rootrec_id"/>
                <input type="hidden" name="accrootfrmmode" id="accrootfrmmode" value="ins"/>
                <input type="hidden" name="account_type" value="group"/>
                <button type="submit" class="btn btn-primary btn-addon btn-md accrootaddbtn"><i class="fa fa-plus"></i> Submit</button>
                
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
              </div>
            </form>
            </div>
          </div>
        </div>

        <!-- Modal -->
        <div class="modal bd-example-modal-md fade" id="addsubrootaccountshead" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
          <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title font-bold rootaccountidhead" id="exampleModalLabel">Add Transection Accounts</h5>
               
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="margin-top: -22px;">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <form role="form" action="{{ route('add-accounts-head') }}" id="smssenderadd" method="post">
                @csrf
              <div class="modal-body">
                <div class="panel panel-default">
                  <div class="panel-heading font-bold">Account's Information</div>
                    
                    <div class="panel-body">
                    
                        <div class="form-group">
                       
                          
                          <div class="row">
                            <div class="col-md-6">
                              <label class="font-bold">Parent <span class="text-danger">*</span></label>
                                <span class="accountsparent"></span>
                                
                            </div>
                            <div class="col-md-6">
                              <label class="font-bold">Transection Account's Head <span class="text-danger">*</span></label>
                                <input type="text" name="acc_head" id="acc_grouphead" value="{{old('acc_head')}}" class="form-control {{$errors->has('acc_head') ? 'border-danger' :  ''}}" placeholder="Enter accounts head">
                                
                            </div>
                          </div> 
                        </div>
                        

                        <div class="form-group row">
                          <label for="status" class="col-sm-2 font-bold" style="margin-top: 10px;">Publish</label>
                          <div class="col-md-2">
                              
                              <div class="radio">
                                
                                <label class="i-checks">
                                  <input type="radio" name="status" id="status_yes" value="1" checked>
                                  <i></i>
                                  Yes
                                </label>
                              </div>
                            </div>
                            <div class="col-md-2">
                              <div class="radio">
                                <label class="i-checks">
                                  <input type="radio" name="status" id="status_no" value="0">
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
                <input type="hidden" name="account_rec_id" id="account_grouprec_id"/>
                <input type="hidden" name="accrootfrmmode" id="accrootfrmmode" value="ins"/>
                <input type="hidden" name="account_type" value="transection"/>
                <button type="submit" class="btn btn-primary btn-addon btn-md accrootaddbtn"><i class="fa fa-plus"></i> Submit</button>
                
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
              </div>
            </form>
            </div>
          </div>
        </div>

        

        <div class="wrapper-md" ng-controller="FlotChartDemoCtrl">
            <!-- <a class="btn btn-primary btn-addon btn-md pull-right mb-5 addsenderid" style="margin-bottom:10px; z-index: 99; position:relative;" data-toggle="modal" data-target="#addrootaccountshead"><i class="fa fa-plus"></i> Create New</a> -->
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
                        SMS Accounts Chart List
                        
                      </div>
                      <div class="table-responsive">
                        <table ui-jq="dataTable" id="groupaccountshead" class="accountshead display nowrap dataTable dtr-inline collapsed" style="width: 100%;" role="grid" aria-describedby="example_info">
                          <thead>
                            <tr>
                              <th>ID</th>
                              <th>Acc Head</th>
                              <th>Parent</th>
                              <th>Created By</th>
                              <th>Updated By</th>
                              <th>Status</th>
                              <th>Owner</th>
                              <th class="actions">Action</th>
                            </tr>
                          </thead>
                          <tfoot>
                          <tr>
                              <th>ID</th>
                              <th>Acc Head</th>
                              <th>Parent</th>
                              <th>Created By</th>
                              <th>Updated By</th>
                              <th>Status</th>
                              <th>Owner</th>
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