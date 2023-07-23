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
                              height: 50px;
                              width: 50px;
                              text-align: center;
                              line-height: 49px;
                              font-size: 21px;
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
            <div class="modal bd-example-modal-md fade" id="smsClientTemplate" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
          <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title font-bold senderidhead" id="exampleModalLabel">Add User Template</h5>
               
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="margin-top: -22px;">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <form role="form" action="{{ route('save-template') }}" id="smssenderadd" method="post" enctype="multipart/form-data">
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
                                <th>Template Format</th>
                                <td>
                                  <textarea name="template_desc"id="template_desc" cols="10" rows="10" class="form-control" placeholder="Enter template format">{{old('template_desc')}}</textarea>
                                  <p>Hi {Name}, Contact with {Address}. Referance : {Ref} Thanks {Client Name}.</p>

                                  <em>Here {Name}, {Address}, {Ref}, {mobile}... etc are columns name, Note: there is must be a column name "Mobile" in the uploaded xls or xlsx .. Sample file(<a href='https://login.smsinbd.com/public/sample.xlsx' style="font-weight: 900; color: green;;">Download</a>)</emp>
                                </td>
                              </tr>
                              <tr class="btrcfile">
                                
                                <th>Upload filled up BTRC approval form</th>
                                <td>
                                  <input type="file" name="file" id="file">
                                  <em>BTRC approval form(<a href='https://login.smsinbd.com/public/BTRC-Form-20200001-dt.pdf' download="" style="font-weight: 900; color: green;;">Download</a>)</em>
                                </td>
                              </tr>
                            </thead>
                            

                            <tbody>
                            </tbody>
                          </table>
                        </div>
                        <div class="form-group row">
                          
                          <div class="col-md-8">
                                <label for="status" class="col-sm-2 font-bold" style="margin-top: 10px;">Status</label>
                                <div id="senderid_status_yes" style="margin-top: 10px;"></div>
                              
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
            @include('smsview.client.profiletab')
            </div>
            <div class="padder" style="margin-top:20px;">    
                <a class="btn btn-primary btn-addon btn-md pull-right mb-5" style="margin-bottom:10px; z-index: 99; position:relative;" data-toggle="modal" data-target="#smsClientTemplate"><i class="fa fa-plus"></i> Create New Template</a>
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
                      <!-- <a href="{{route('teletalk-sms-senderid',['senderidtype'=>'teletalk'])}}" class="btn btn-primary btn-addon btn-md mb-5" style="margin-bottom:10px; z-index: 99; position:relative; margin-right:20px;"><i class="fa fa-list"></i> Teletalk Senderid</a> -->
                        
                      </div>
                      <div class="table-responsive">
                        <table ui-jq="dataTable" id="clienttemplate" class="clienttemplate display dataTable dtr-inline collapsed" style="width: 100%;" role="grid" aria-describedby="example_info">
                          <thead>
                            <tr>
                              <th>ID</th>
                              <th>Template Title</th>
                              <th>Template Desc</th>
                              <th>Template Owner</th>
                              <th>User Type</th>
                              <th>Status</th>
                              <th>Created At</th>
                              <th>Updated At</th>
                              <th>BTRC File</th>
                              <th>BTRC File Status</th>
                              <th class="actions">Action</th>
                            </tr>
                          </thead>
                          <tfoot>
                          <tr>
                              <th>ID</th>
                              <th>Template Title</th>
                              <th>Template Desc</th>
                              <th>Template Owner</th>
                              <th>User Type</th>
                              <th>Status</th>
                              <th>Created At</th>
                              <th>Updated At</th>
                              <th>BTRC File</th>
                              <th>BTRC File Status</th>
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
        <!-- / main -->
        </div>



        </div>
        </div>
        <!-- /content -->
        
@endsection