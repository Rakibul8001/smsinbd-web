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
                  <h1 class="m-n font-thin h3 text-black font-bold">Client Invoice List</h1>
                  <small class="text-muted"></small>
              </div>
            </div>
        </div>
        <!-- / main header -->
        <div class="wrapper-md" ng-controller="FlotChartDemoCtrl">
            @if(Auth::guard('root')->check() || Auth::guard('manager')->check())
            <a class="btn btn-primary btn-addon btn-md pull-right mb-5" style="margin-bottom:10px; z-index: 9999; position:relative;" href="{{route('sms-saleto-client')}}"><i class="fa fa-plus"></i> Create New</a>
            @endif
            <div class="row">
              <div class="col-md-12">
                  <div class="panel panel-default dataTables_wrapper">
                      <div class="panel-heading">
                        <strong>Client Invoices</strong>
                      </div>
                      <div class="table-responsive dataTables_wrapper">
                        <table class="resellermyinvoicelist table table-striped table-hover display nowrap no-footer dtr-inline dataTable collapsed" style="width: 100%;" role="grid" aria-describedby="example_info">
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
        <!-- right col -->
        
        <!-- / right col -->
        </div>



        </div>
        </div>
        <!-- /content -->
        
@endsection