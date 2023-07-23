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
        <!-- <div class="bg-light lter b-b wrapper-md">
            <div class="row">
              <div class="col-sm-6 col-xs-12">
                <h1 class="m-n font-thin h3 text-black font-bold">Super Admin</h1>
                <small class="text-muted">Welcome to SMSBD Application</small>
              </div>  
              <div class="col-sm-6 col-xs-12 text-right hidden-xs">
                  <h1 class="m-n font-thin h3 text-black font-bold">Create Invoice</h1>
                  <small class="text-muted"></small>
              </div>
            </div>
        </div> -->

        <!-- / main header -->

        <div class="wrapper-md" ng-controller="FlotChartDemoCtrl">
            <div class="row">
                <div class="col-md-8">
                    <div class="panel panel-default dataTables_wrapper">
                        <div class="panel-heading font-bold">Reseller SMS Sale Form <span class="pull-right" style="top: -7px;position: relative;right: -12px;"><button type="button" class="btn btn-primary btn-addon btn-md pull-left clearinvoice">RESET FORM</button></span>
                        <span class="text-danger font-bold balancecheckerr pull-right" style="display: none;">Validation period can't left empty</span>
                    </div>
                        <div class="panel-body">
                            <div style="width:auto; position: relative;left:3%;">
                            <div class="form-group" style="margin-bottom: 30px;">
                                <div class="row">
                                    <div class="col-md-offset-1 col-md-4 col-lg-4 col-sm-4 col-xs-4">
                                        <input type="hidden" name="user_type" id="user_type" value="reseller"/>
                                        <label for="" class="font-bold">Client</label>
                                        <select ui-jq="chosen" name="invoice_client" id="invoice_client" class="form-control">
                                            <option value="">Select client</option>
                                            @foreach($clients as $client)
                                                <option value="{{$client->id}}">{{$client->name}}->{{$client->phone}}</option>
                                            @endforeach
                                        </select>
                                        <span class="text-danger font-bold clienterr" style="display: none;">Invoice client can't left empty</span>
                                    </div>

                                    <div class="col-md-3 col-lg-3 col-sm-4 col-xs-4">
                                        <label for="" class="font-bold">Date</label>
                                        <div class="input-group date">
                                            <input type="text" class="form-control invoice_date" name="invoice_date" id="invoice_date" placeholder="Enter date"onkeypress=" return handleEnter(this, event)"/>
                                            <span class="input-group-addon"><i class="fa fa-th"></i></span>
                                        </div>
                                        <span class="text-danger font-bold invoicedateerr" style="display: none;">Invoice date can't left empty</span>
                                    </div>
                                    <div class="col-md-3 col-lg-3 col-sm-4 col-xs-4">
                                        <label for="" class="font-bold">Validity Period</label>
                                        <div class="input-group date">
                                            <select name="validity_date" id="validity_date" class="form-control" onkeypress="return handleEnter(this, event)">
                                                <option value=""></option>
                                                <option value="1 Month">1 Month</option>
                                                <option value="3 Month">3 Month</option>
                                                <option value="6 Month">6 Month</option>
                                                <option value="1 Year">1 Year</option>
                                                <option value="Unlimited">Unlimited</option>
                                            </select>
                                            <span class="input-group-addon"><i class="fa fa-th"></i></span>
                                        </div>
                                        <span class="text-danger font-bold validitydateerr" style="display: none;">Validation period can't left empty</span>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-offset-1 col-md-3 col-lg-3 col-sm-3 col-xs-3"> 
                                        <label for="" class="font-bold">SMS Type</label>
                                        <select name="sms_type" id="sms_type" class="form-control" onkeypress="return handleEnter(this, event)">
                                            <option value=""></option>
                                            <option value="mask">Mask</option>
                                            <option value="nomask">Non Mask</option>
                                            <option value="voice">Voice</option>
                                        </select>
                                        <span class="text-danger font-bold smstypeerr" style="display: none;">Sms type can't left empty</span>
                                    </div>
                                    <div class="col-md-2 col-lg-2 col-sm-2 col-xs-2">
                                        <label for="" class="font-bold">Rate</label>
                                        <input type="text" class="form-control" name="rate" id="rate" placeholder="Enter rate" onkeypress="return handleEnter(this, event)"/>
                                        <span class="text-danger font-bold rateerr" style="display: none;">Rate can't left empty</span>
                                    </div>

                                    <div class="col-md-2 col-lg-2 col-sm-2 col-xs-2">
                                        <label for="" class="font-bold">Amount</label>
                                        <input type="text" class="form-control" name="price" id="price" placeholder="Enter amount" onkeypress="return handleEnter(this, event)"/>
                                        <span class="text-danger font-bold priceerr" style="display: none;">Amount can't left empty</span>
                                    </div>
                                   
                                    <div class="col-md-2 col-lg-2 col-sm-2 col-xs-2">
                                        <label for="" class="font-bold">Qty</label>
                                        <input type="hidden" name="smsqty" id="smsqty"/>
                                        <div class="smsqty form-control"></div>
                                        <span class="text-danger font-bold smsqtyerr" style="display: none;">Sms qty can't left empty</span>
                                    </div>
                                    
                                    
                                    
                                    <div class="col-md-2 col-lg-2 col-sm-2 col-xs-2" style="margin-top: 26px;">
                                    <button type="button" class="btn btn-primary btn-addon btn-md pull-left smssale">ADD</button>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12 col-md-12 col-lg-12 col-sm-12 col-xl-12">
                                        <spa>
                                    </div>
                                </div>
                            </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="panel panel-default dataTables_wrapper">
                            <div class="panel-heading font-bold">Invoice Information</div>
                            <div class="panel-body">
                                <div style="width:auto; position: relative;">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Voucher Type : </th>
                                                <th class="paymentoption">
                                                <select name="paymentoption" id="paymentoption" class="form-control" onkeypress="return handleEnter(this, event)">
                                                    <option value="">Select voucher type</option>
                                                    <option value="cash">Payment</option>
                                                    <option value="debit">Refund</option>
                                                </select>
                                                <span class="text-danger font-bold paymentoptionerr" style="display: none;">Payment type can't left empty</span>
                                                </th>
                                            </tr>
                                            <tr>
                                                <th>Invoice Total: </th>
                                                <th class="invoicetotal"></th>
                                            </tr>
                                            <tr>
                                                <th>Vat: <input type="text" name="invoice_vat" id="invoice_vat" placeholder="%" size="4" class="text-center"/>
                                                <span class="text-danger font-bold vaterr" style="display: none;">Invoice vat can't left empty</span>
                                                </th>
                                                <th class="invoicevat"></th>
                                            </tr>
                                            <tr>
                                                <th>Grand Total : </th>
                                                <th class="grandtotal"></th>
                                            </tr>
                                            

                                            <tr>
                                                <th>Payment Method : </th>
                                                <th class="paymentby">
                                                <select name="paymentby" id="paymentby" class="form-control" onkeypress="return handleEnter(this, event)">
                                                    <option value="">Select payment method</option>
                                                    @foreach($groups['data'] as $group)
                                                        <option value="{{$group['id']}}">{{$group['acc_head']}}</option>
                                                    @endforeach
                                                </select>
                                                <span class="text-danger font-bold paymentbyerr" style="display: none;">Payment method can't left empty</span>
                                                </th>
                                            </tr>
                                            <tr>
                                                <th>Remarks : </th>
                                                <th class="remarks">
                                                    <textarea name="remarks" id="remarks" class="form-control" placeholder="Enter remarks"></textarea>
                                                    <span class="text-danger font-bold remarkserr" style="display: none;">Remarks can't left empty</span>
                                                </th>
                                            </tr>
                                        </thead>
                                    </table>
                                    <button type="button" class="btn btn-primary btn-addon btn-md pull-right save-invoice"><i class="fa fa-plus"></i> Submit</button>
                                </div>
                            </div>
                    </div>
                </div>

                <div class="col-md-12">
                
                    <div class="panel panel-default dataTables_wrapper">
                        <div class="panel-heading font-bold">Product List</div>
                        <div class="panel-body">
                            <div style="width:auto; position: relative;">
                                <table class="table table-bordered carttable">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Type</th>
                                            <th>Qty</th>
                                            <th>Rate</th>
                                            <th>Price</th>
                                            <th class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                </table>
                            </div>
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