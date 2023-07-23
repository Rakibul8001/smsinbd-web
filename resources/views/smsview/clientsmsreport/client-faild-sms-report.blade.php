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
                <h1 class="m-n font-thin h3 text-black font-bold">Client Admin Panel</h1>
                <small class="text-muted">Welcome to SMSBD Application</small>
              </div>
              <div class="col-sm-6 col-xs-12 text-right hidden-xs">
                  <h1 class="m-n font-thin h3 text-black font-bold">SenderID List</h1>
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
                        Datewise SMS sent Report
                      </div>
                      <div class="table-responsive dataTables_wrapper">
                            <div class="container">
                                <div class="row">
                                    <div class="col-md-3 col-lg-3 col-sm-12 col-xs-12 col-md-offset-2">
                                        <div class="input-group date">
                                        <input type="text" class="form-control from_date" name="from_date" id="from_date" value="<?php echo date("Y-m-d"); ?>" placeholder="From date"onkeypress=" return handleEnter(this, event)"/>
                                            <span class="input-group-addon"><i class="fa fa-th"></i></span>
                                        </div>
                                        <span class="text-danger font-bold invoicedateerr" style="display: none;">Invoice date can't left empty</span>
                                    </div>  
                                    <div class="col-md-3 col-lg-3 col-sm-12 col-xs-12">
                                        <div class="input-group date">
                                            <input type="text" class="form-control to_date" name="to_date" id="to_date" value="<?php echo date("Y-m-d"); ?>" placeholder="To date"onkeypress=" return handleEnter(this, event)"/>
                                            <span class="input-group-addon"><i class="fa fa-th"></i></span>
                                        </div>
                                        <span class="text-danger font-bold invoicedateerr" style="display: none;">Invoice date can't left empty</span>
                                    </div>

                                    <div class="col-md-3 col-lg-3 col-sm-12 col-xs-12">
                                        <div class="input-group date">
                                            <button type="button" class="btn btn-primary getclietfaildsmsreport">Submit</button>
                                        </div>
                                        <span class="text-danger font-bold invoicedateerr" style="display: none;">Invoice date can't left empty</span>
                                    </div>
                                </div>
                            </div>
                        <table class="clientfaildsmsreport table table-striped table-hover dt-responsive display nowrap no-footer dtr-inline dataTable collapsed" style="width: 100%;" role="grid" aria-describedby="example_info">
                          <thead>
                            <tr>
                              <th>ID</th>
                              <th>Name</th>
                              <th>Email</th>
                              <th>SenderID</th>
                              <th>Contact</th>
                              <th>SMS Type</th>
                              <th>SMS Category</th>
                              <th>No.of.Sms</th>
                              <th>Send From</th>
                              <th>Status</th>
                            </tr>
                          </thead>
                          <tfoot>
                          <tr>
                              <th>ID</th>
                              <th>Name</th>
                              <th>Email</th>
                              <th>SenderID</th>
                              <th>Contact</th>
                              <th>SMS Type</th>
                              <th>SMS Category</th>
                              <th>No.of.Sms</th>
                              <th>Send From</th>
                              <th>Status</th>
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