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
                  <h1 class="m-n font-thin h3 text-black font-bold">SMS Delivery History</h1>
                  <small class="text-muted"></small>
              </div>
            </div>
        </div>

        <!-- Modal -->
        <div class="modal bd-example-modal-md fade" id="campaigndetailsview" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
          <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title font-bold rootaccountidhead" id="exampleModalLabel">Campaign Report Details</h5>
               
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="margin-top: -22px;">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">
                <div class="panel panel-default">
                  <div class="panel-heading font-bold">Contact List</div>
                    
                    <div class="panel-body">
                    
                        
                          <table class="campaigncontactlist table table-striped table-hover dt-responsive display nowrap no-footer dtr-inline dataTable collapsed" style="width: 100%;" role="grid" aria-describedby="example_info">
                            <thead>
                              <tr>
                                <th>Campaign Title</th>
                                <th>Total SMS Sent</th>
                                <th>Submitted At</th>
                                <th>Action</th>
                              </tr>
                            </thead>
                            <tfoot>
                              <tr>
                                <th>Campaign Title</th>
                                <th>Total SMS Sent</th>
                                <th>Submitted At</th>
                                <th>Action</th>
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
        <!-- / main header -->
        <div class="wrapper-md" ng-controller="FlotChartDemoCtrl">
          
            <div class="row">
              <div class="col-md-12">
                  <div class="panel panel-default dataTables_wrapper">
                      <div class="panel-heading font-bold">
                        Datewise DLR Report
                      </div>
                      
                      <div class="table-responsive dataTables_wrapper">
                            <div class="container">
                                <div class="row">
                                <div class="col-md-2 col-lg-2 col-sm-12 col-xs-12 col-md-offset-3">
                                        <div class="input-group date">
                                        <input type="text" class="form-control from_date" name="from_date" id="from_date" value="<?php echo date("Y-m-d"); ?>" placeholder="From date"onkeypress=" return handleEnter(this, event)"/>
                                            <span class="input-group-addon"><i class="fa fa-th"></i></span>
                                        </div>
                                        <span class="text-danger font-bold invoicedateerr" style="display: none;">From date can't left empty</span>
                                    </div>  
                                    <div class="col-md-2 col-lg-2 col-sm-12 col-xs-12">
                                        <div class="input-group date">
                                            <input type="text" class="form-control to_date" name="to_date" id="to_date" value="<?php echo date("Y-m-d"); ?>" placeholder="To date"onkeypress=" return handleEnter(this, event)"/>
                                            <span class="input-group-addon"><i class="fa fa-th"></i></span>
                                        </div>
                                        <span class="text-danger font-bold invoicedateerr" style="display: none;">To date can't left empty</span>
                                    </div>

                                    <div class="col-md-3 col-lg-3 col-sm-12 col-xs-12">
                                        <div class="input-group date">
                                            <button type="button" class="btn btn-primary getrootclietcountsmsreport">Submit</button>
                                        </div>
                                        <span class="text-danger font-bold invoicedateerr" style="display: none;">To date can't left empty</span>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="pull-left totalconsulatesendsms"></div>
                                    </div>
                                </div>
                            </div>
                        <table class="clientsmscountreport table table-striped table-hover dt-responsive display nowrap no-footer dtr-inline dataTable collapsed" style="width: 100%;" role="grid" aria-describedby="example_info">
                          <thead>
                            <tr>
                              <th>#SL</th>
                              <th>Campaign Title</th>
                              <th>Total SMS Sent</th>
                              <th>Submitted At</th>
                              <th>Action</th>
                            </tr>
                          </thead>
                          <tfoot>
                            <tr>
                              <th>#SL</th>
                              <th>Campaign Title</th>
                              <th>Total SMS Sent</th>
                              <th>Submitted At</th>
                              <th>Action</th>
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