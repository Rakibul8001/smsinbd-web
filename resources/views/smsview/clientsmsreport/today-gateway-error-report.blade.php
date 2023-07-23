@extends('layouts.smsapp')

@section('appbody')
  <style type="text/css">
    .modal-dialog {
        width: 1200px;
        margin: 30px auto;
    }

    table.dataTable.nowrap th, table.dataTable.nowrap td {
        white-space: break-spaces;
    }
    
  </style>
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
                  <h1 class="m-n font-thin h3 text-black font-bold">Gateway Error Report</h1>
                  <small class="text-muted"></small>
              </div>
            </div>
        </div>

       


        
        <!-- / main header -->
        <div class="wrapper-md" ng-controller="FlotChartDemoCtrl">
          
            <div class="row">
              <div class="col-md-12">
                  <div class="panel panel-default dataTables_wrapper">
                      <div class="panel-heading font-bold">
                        Today's Gateway Errors
                      </div>
                      
                      <div class="table-responsive dataTables_wrapper">
                            
                        <table class="gatewayerrors table table-striped table-hover display no-footer dtr-inline dataTable" style="width: 100%;" role="grid" aria-describedby="example_info">
                          <thead>
                            <tr>
                              <th>Id</th>
                              <th>Operator</th>
                              <th>Senderid</th>
                              <th>Error Description</th>
                              <th>Created At</th>
                            </tr>
                          </thead>
                          <tfoot>
                          <tr>
                              <th>Id</th>
                              <th>Operator</th>
                              <th>Senderid</th>
                              <th>Error Description</th>
                              <th>Created At</th>
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