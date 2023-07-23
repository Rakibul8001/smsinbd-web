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

        <!-- / main header -->


        <div class="wrapper-md" ng-controller="FlotChartDemoCtrl">
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
                        SMS Sender ID List
                        
                      </div>
                      <div class="table-responsive">
                        <table ui-jq="dataTable" id="smssender" class="smssenderidforreseller display nowrap dataTable dtr-inline collapsed" style="width: 100%;" role="grid" aria-describedby="example_info">
                          <thead>
                            <tr>
                              <th>ID</th>
                              <th>Sender ID</th>
                              <th>Operator</th>
                              <th>Status</th>
                              <th>default</th>
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
                              <th>default</th>
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