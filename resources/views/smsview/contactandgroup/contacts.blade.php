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
                  <h1 class="m-n font-thin h3 text-black font-bold">Contact List In Group</h1>
                  <small class="text-muted"></small>
              </div>
            </div>
        </div>
        <!-- / main header -->
        <div class="wrapper-md" ng-controller="FlotChartDemoCtrl">
            <a class="btn btn-primary btn-addon btn-md pull-right mb-5 addsenderid" style="margin-bottom:10px; z-index: 99; position:relative;" data-toggle="modal" data-target="#addContactInGroup"><i class="fa fa-plus"></i> Create New</a>
            <div class="row">
              <div class="col-md-12">
                  <div class="panel panel-default dataTables_wrapper">
                      <div class="panel-heading font-bold">
                        Contact List
                      </div>
                      <div class="table-responsive dataTables_wrapper">
                        <table class="contactlist table table-striped table-hover dt-responsive display nowrap no-footer dtr-inline dataTable collapsed" style="width: 100%;" role="grid" aria-describedby="example_info">
                          <thead>
                            <tr>
                              <th>ID</th>
                              <th>Contact Number</th>
                              <th>Group</th>
                              <th>Status</th>
                              <th>Action</th>
                            </tr>
                          </thead>
                          <tfoot>
                          <tr>
                              <th>ID</th>
                              <th>Contact Number</th>
                              <th>Group</th>
                              <th>Status</th>
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


        <!-- Modal -->
        <div class="modal bd-example-modal-lg fade" id="addContactInGroup" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
          <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title font-bold contactgrouphead" id="exampleModalLabel">Add New Contact Group</h5>
               
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="margin-top: -22px;">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <form role="form" action="create-contacts" id="contactingroupfrm" enctype="multipart/form-data" method="post">
                @csrf
              <div class="modal-body">
                <div class="panel panel-default">
                  <div class="panel-heading font-bold">Contact Group Name <span class="insrecord pull-right text-success"></span><span class="notinsrecord pull-right text-danger"></span></div>
                    
                    <div class="panel-body">
                        <div class="form-group">
                        <div class="row" style="border: 1px solid #ddd; margin: 0px 10px 40px 10px; padding: 10px;">
                              <div class="col-md-3 col-md-offset-3">
                                <div class="radio">
                                  <label class="i-checks font-bold">
                                    <input type="radio" name="contactformtype" id="singleform" value="single" checked>
                                    <i></i>
                                    Single Number
                                  </label>
                                </div>
                              </div>
                              <div class="col-md-3">
                                <div class="radio">
                                  <label class="i-checks">
                                    <input type="radio" name="contactformtype" id="multipleform" value="multiple" require>
                                    <i></i>
                                    Upload File
                                  </label>
                                </div>
                              </div>
                              
                            </div>
                        </div>
                        <div class="form-group multiplenumber" style="display: none;">
                          
                            <div class="row">
                              <label class="col-sm-3 control-label font-bold">Upload Contact List File <span class="text-danger">*</span></label>
                              <div class="col-sm-7">
                                <input type="file" name="file" id="file">
                              </div>
                              <!-- <div class="col-sm-2">
                                <button type="submit" class="btn btn-primary btn-addon btn-md pull-right"><i class="fa fa-plus"></i> Submit</button>
                              </div> -->
                            </div>
                            
                        </div>
                        <div class="form-group singlenumber">
                          
                          <div class="row">
                            <div class="col-md-4">
                              <label class="font-bold">Mobile Number <span class="text-danger">*</span></label>
                              <input type="text" name="contact_number" id="contact_number" value="" class="form-control" placeholder="Enter mobile number" require>
                            </div>
                            <div class="col-md-4">
                              <label class="font-bold">Name</label>
                              <input type="text" name="contact_name" id="contact_name" value="" class="form-control" placeholder="Enter nmae" require>
                            </div>
                            <div class="col-md-4">
                              <label class="font-bold">Email</label>
                              <input type="text" name="email" id="email" value="" class="form-control" placeholder="Enter email">
                            </div>
                          </div> 

                          <div class="row">
                            <div class="col-md-6">
                              <label class="font-bold">Gender</label>
                              <select name="gender" id="gender" class="form-control">
                                <option value="">Select gender</option>
                                  <option value="male">Male</option>
                                  <option value="female">Female</option>
                              </select>
                            </div>
                            <div class="col-md-6">
                              <label class="font-bold">DOB</label>
                              <input type="text" name="dob" id="dob" value="" class="form-control datepicker" placeholder="Enter dob">
                            </div>
                          </div> 
                        </div>

                        <!-- <div class="form-group row">
                          <label for="status" class="col-sm-2 font-bold" style="margin-top: 10px;">Publish</label>
                          <div class="col-md-2">
                              
                              <div class="radio">
                                
                                <label class="i-checks">
                                  <input type="radio" name="status" id="senderid_status_yes" value="1" checked>
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

                        </div> -->

                        <div class="form-group">
                            <div class="row">
                              <div class="col-md-12 contactgroup">
                                <label for="contactgroup">Contact Group <span class="text-danger">*</span></label>
                                <select ui-jq="chosen"  id="contactgroup" name="contactgroup[]" multiple class="form-control w-md">
                                      @foreach($groups as $group)  
                                          <option value="{{$group->id}}">{{$group->group_name}}</option>
                                      @endforeach    
                                </select>
                              </div>
                            </div>
                        </div>
                    </div>
                  </div>
              </div>
              <div class="modal-footer">
                <input type="hidden" name="frmmode" id="frmmode" value="ins"/>
                <input type="hidden" name="id" id="id"/>
                <button type="submit" class="btn btn-primary btn-addon btn-md btncontactingroup"><i class="fa fa-plus"></i> Submit</button>
                
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
              </div>
            </form>
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