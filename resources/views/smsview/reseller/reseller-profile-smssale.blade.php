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
                                    height: 70px;
                                    width: 70px;
                                    text-align: center;
                                    line-height: 67px;
                                    font-size: 24px;
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
                    <div class="pull-right pull-none-xs text-center">
                    <a href class="m-b-md inline m">
                        <span class="h3 block font-bold">Mask</span>
                        <small>{{$totalmaskbal}}</small>
                    </a>
                    <a href class="m-b-md inline m">
                        <span class="h3 block font-bold">Non Mask</span>
                        <small>{{$totalnonmaskbal}}</small>
                    </a>
                    <a href class="m-b-md inline m">
                        <span class="h3 block font-bold">Voice</span>
                        <small>{{$totalvoicebal}}</small>
                    </a>
                    </div>
                </div>
                </div>
            </div>
            </div>
            <div class="modal bd-example-modal-md fade" id="smsRootTemplate" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
          <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title font-bold senderidhead" id="exampleModalLabel">Add User Template</h5>
               
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="margin-top: -22px;">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <form role="form" action="{{ route('save-template') }}" id="smssenderadd" method="post">
                @csrf
              <div class="modal-body">
                <div class="panel panel-default">
                  <div class="panel-heading font-bold">Template Information</div>
                    <div class="panel-body">
                    
                        
                        <div class="form-group general-senderid" style="margin-top: 10px;">
                          <table class="table table-bordered table-striped" style="width: 100%;" role="grid" aria-describedby="example_info">
                            <thead>
                              <tr>
                                <!-- <th>Operator Name</th> -->
                                <th>Template Title</th>
                                <td><input type="text" name="template_title"  value="{{old('template_title')}}" id="template_title" class="form-control" placeholder="Enter template title"></td>
                              </tr>
                              <tr>
                                <!-- <th>Operator Name</th> -->
                                <th>Template Description</th>
                                <td><textarea name="template_desc"id="template_desc" cols="10" rows="10" class="form-control" placeholder="Enter template description">{{old('template_desc')}}</textarea></td>
                              </tr>
                              </tr>
                            </thead>
                            

                            <tbody>
                            </tbody>
                          </table>
                        </div>
                        <div class="form-group row">
                          <label for="status" class="col-sm-2 font-bold" style="margin-top: 10px;">Status</label>
                          <div class="col-md-2">
                              
                              <div class="radio">
                                
                                <label class="i-checks">
                                  <input type="radio" name="status" id="senderid_status_yes" value="1">
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
                        </div>
                        
                    </div>
                  </div>
              </div>
              <div class="modal-footer">
                
                <input type="hidden" name="frmmode" id="frmmode" value="ins"/>
                <input type="hidden" name="id" id="id" value="ins"/>
                <button type="submit" class="btn btn-primary btn-addon btn-md templatesavebtn"><i class="fa fa-save"></i> Submit</button>
                
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
              </div>
            </form>
            </div>
          </div>
        </div>
            <div class="wrapper bg-white b-b">
            @include('smsview.reseller.profiletab')
            </div>
            <div class="padder" style="margin-top:20px;">    
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-default dataTables_wrapper">
                        <div class="panel-heading font-bold">Reseller SMS Sale Form <span class="pull-right" style="top: -7px;position: relative;right: -12px;"><button type="button" class="btn btn-primary btn-addon btn-md pull-left clearinvoice">RESET FORM</button></span>
                        <span class="text-danger font-bold balancecheckerr pull-right" style="display: none;">Validation period can't left empty</span>
                    </div>
                        <div class="panel-body">
                            <div style="width:auto; position: relative;left:3%;">
                            <div class="form-group" style="margin-bottom: 30px;">
                                <div class="row">
                                    <input type="hidden" name="user_type" id="user_type" value="reseller"/>
                                    <input type="hidden" name="invoice_client" id="invoice_client" value="{{$request->userid}}"/>
                                    <span class="text-danger font-bold clienterr" style="display: none;">Invoice client can't left empty</span>
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
                                    <div class="col-md-3 col-lg-3 col-sm-3 col-xs-3"> 
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
                <div class="col-md-12">
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
                                                    @if(Auth::guard('root')->check() || Auth::guard('manager')->check())
                                                        <option value="{{$group['id']}}">{{$group['acc_head']}}</option>
                                                    @else
                                                        @if($group['acc_head'] == 'Cash')
                                                            <option value="{{$group['id']}}">{{$group['acc_head']}}</option>
                                                        @endif
                                                    @endif
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

                
            </div>
            </div>
        </div>
        <!-- / main -->
        </div>



        </div>
        </div>
        <!-- /content -->
        
@endsection