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
                <h1 class="m-n font-thin h3 text-black font-bold">Client Profile</h1>
                <small class="text-muted"></small>
            </div>
            <div class="col-sm-6 text-right hidden-xs">
                
            </div>
            </div>
        </div>
        <!-- / main header -->
        <div>
            <div class="wrapper-lg bg-white-opacity">
                <div class="row m-t">
                <div class="col-sm-7">
                    <a href class="thumb-lg pull-left m-r" style="border: 1px solid #ddd;
                        border-radius: 100%;
                        height: 100px;
                        width: 100px;
                        text-align: center;
                        line-height: 96px;
                        font-size: 32px;
                        background-color: #ddd;">
                    <i class="icon-user"></i>
                    </a>
                    <div class="clear m-b">
                    <div class="m-b m-t-sm">
                        <span class="h3 text-black">{{$user->name}}</span>
                        <small class="m-l">{{$user->address}}</small>
                    </div>
                    </div>
                </div>
                <div class="col-sm-5">
                    
                </div>
                </div>
            </div>
            </div>
           
            <div class="wrapper bg-white b-b">
            @include('smsview.client.profiletab')
            </div>
            <div class="padder" style="margin-top:20px;">    
            <div class="row">
                <div class="col-md-12">
                
                <div class="panel panel-default dataTables_wrapper">
                      <div class="panel-heading">
                        <strong>Client Invoices</strong>
                      </div>
                      <div class="table-responsive dataTables_wrapper">
                      <table class="rootinvoicelist table table-striped table-hover display nowrap no-footer dtr-inline dataTable collapsed" style="width: 100%;" role="grid" aria-describedby="example_info">
                          <thead>
                            <tr>
                              <th>ID</th>
                              <th>Name</th>
                              <th>Email</th>
                              <th>Phone</th>
                              <th>InvoiceID</th>
                              <th>Qty</th>
                              <th>Rate</th>
                              <th>Price</th>
                              <th>Validity</th>
                              <th>Vat%</th>
                              <th>Vat Amount</th>
                              <th>Date</th>
                              <th>Created By</th>
                              <th>SMS Type</th>
                            </tr>
                          </thead>
                          <tfoot>
                          <tr>
                              <th>ID</th>
                              <th>Name</th>
                              <th>Email</th>
                              <th>Phone</th>
                              <th>InvoiceID</th>
                              <th>Qty</th>
                              <th>Rate</th>
                              <th>Price</th>
                              <th>Validity</th>
                              <th>Vat%</th>
                              <th>Vat Amount</th>
                              <th>Date</th>
                              <th>Created By</th>
                              <th>SMS Type</th>
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
        </div>



        </div>
        </div>
        <!-- /content -->
        
@endsection