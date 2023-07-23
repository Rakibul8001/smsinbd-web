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
                  <h1 class="m-n font-thin h3 text-black font-bold">Reseller Panel</h1>
                  <small class="text-muted"></small>
              </div>
            </div>
        </div>

        <div class="modal bd-example-modal-md fade" id="resellerassignedsenders" tabindex="-1" role="dialog" aria-labelledby="operatorapiModalLabel" aria-hidden="true">
          <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title font-bold" id="operatorapiModalLabel">Client Senderid List <span class="clientname pull-right" style="margin-right:20px;"></span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="margin-top: -22px;">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <form role="form" action="{{route('client-balance')}}" id="smsoperatorapi" method="post">
                @csrf
                <div class="load-senderid" style="text-align: center; padding: 24px 10px;">
                  
                </div>
              
            </form>
            </div>
          </div>
        </div>

        <div class="modal bd-example-modal-md fade" id="clientviewbalance" tabindex="-1" role="dialog" aria-labelledby="operatorapiModalLabel" aria-hidden="true">
          <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title font-bold" id="operatorapiModalLabel">Client SMS Balance List <span class="clientname pull-right" style="margin-right:40px;"></span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="margin-top: -22px;">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <form role="form" action="{{route('client-balance')}}" id="smsoperatorapi" method="post">
                @csrf
                <div class="load-balance">
                  <table class="table table-stripped">
                    <thead>
                      <tr>
                        <th>Mask</th>
                        <td class="balmask"></td>
                      </tr>
                      <tr>
                        <th>Non Mask</th>
                        <td class="balnonmask"></td>
                      </tr>
                      <tr>
                        <th>Voice</th>
                        <td class="balvoice"></td>
                      </tr>

                    </thead>
                  </table>
                </div>
              
            </form>
            </div>
          </div>
        </div>
        <!-- / main header -->
        <div class="wrapper-md" ng-controller="FlotChartDemoCtrl">
          <a class="btn btn-primary btn-addon btn-md pull-right mb-5" style="margin-bottom:10px; z-index: 9999; position:relative;" href="{{route('reseller-client-signup')}}"><i class="fa fa-plus"></i> Create New Client</a>
            <div class="row">
              <div class="col-md-12">
                  <div class="panel panel-default dataTables_wrapper">
                      <div class="panel-heading">
                        Clients  <span class="usernotfound pull-right"></span>
                      </div>
                      <div class="table-responsive">
                      <table class="clients display nowrap dataTable dtr-inline collapsed" style="width: 100%;" role="grid" aria-describedby="example_info">
                          <thead>
                            <tr>
                              <th>ID</th>
                              <th>Name</th>
                              <th>Email</th>
                              <th>Company</th>
                              <th>Phone</th>
                              <th>Address</th>
                              <th>Country</th>
                              <th>City</th>
                              <th>State</th>
                              <th>Created From</th>
                              <th>Created By</th>
                              <th>Status</th>
                              <th>Created At</th>
                              <th class="actions">Action</th>
                            </tr>
                          </thead>
                          <tfoot>
                            <tr>
                              <th>ID</th>
                              <th>Name</th>
                              <th>Email</th>
                              <th>Company</th>
                              <th>Phone</th>
                              <th>Address</th>
                              <th>Country</th>
                              <th>City</th>
                              <th>State</th>
                              <th>Created From</th>
                              <th>Created By</th>
                              <th>Status</th>
                              <th>Created At</th>
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