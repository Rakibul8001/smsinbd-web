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
       
        <!-- / main header -->
          <div class="row">
            <!-- <div class="col-md-3 col-lg-3 col-sm-12 col-3 pull-right" style="margin:25px 25px 0 0;">
            <a class="btn btn-primary btn-addon btn-md pull-right mb-5 addsenderid" style="margin-bottom:10px; z-index: 99; position:relative;" data-toggle="modal" data-target="#addContactInGroup"><i class="fa fa-plus"></i> Create New Contact</a>
            </div> -->
          </div>
          <div class="row m-t-md" style="margin:10px;">
            <div class="col-md-9">
              @if(session()->has('msg'))
                <div class="alert alert-success font-weight-bold clientsuccess" role="alert">
                  {{session()->get('msg')}}
                </div>
              @endif
              <div style="position: absolute; width:100%; height: 100%; background-color:#fff; opacity:.8; z-index:99;display: none;" class="setupsmsroot">
                <span style="top:40%; position:absolute; width:100%; text-align:center;" class="font-bold setupsms">Processing....</span>
              </div>
              <!-- <div class="row row-sm text-center">
                  <div class="col-xs-12 m-b-md mt-3">
                      <div class="r bg-primary dker item hbox no-border">
                          <div class="col w-xs v-middle hidden-md">
                              <div class="sparkline inline"><i class="fa fa-money text-white-dker" style="font-size: 25px;"></i></div>
                          </div>
                          <div class="col dk padder-v r-r">
                              <div class="text-white-dk font-thin font-bold h4"><span>MASK SMS BALANCE</span></div>
                              <span class="text-white-dk font-thin font-bold h1 maskbal">{{$totalmaskbal}}</span>
                          </div>

                          <div class="col w-xs v-middle hidden-md">
                              <div class="sparkline inline"><i class="fa fa-money text-white-dker" style="font-size: 25px;"></i></div>
                          </div>
                          <div class="col dk padder-v r-r">
                              <div class="text-white-dk font-thin font-bold h4"><span>NON MASK SMS BALANCE</span></div>
                              <span class="text-white-dk font-thin font-bold h1 nonmaskbal">{{$totalnonmaskbal}}</span>
                          </div>
                          <div class="col w-xs v-middle hidden-md">
                              <div class="sparkline inline"><i class="fa fa-money text-white-dker" style="font-size: 25px;"></i></div>
                          </div>
                          <div class="col dk padder-v r-r">
                              <div class="text-white-dk font-thin font-bold h4"><span>VOICE SMS BALANCE</span></div>
                              <span class="text-white-dk font-thin font-bold h1 voicebal">{{$totalvoicebal}}</span>
                          </div>
                      </div>
                  </div>
              </div> -->
              
              <div class="panel panel-default">
              <div class="panel-heading font-bold setupsmsform">SMS Form </div>
                <!-- <div class="panel-body contactuplodfile" style="display: none;">
                  <form role="form" action="#" method="post" enctype="multipart/form-data">
                  <div class="form-group">
                    <div class="row" style="border: 1px solid #ddd; margin: 0px 10px 40px 10px; padding: 10px;">
                        <div class="col-md-3 col-md-offset-3">
                          <div class="radio">
                            <label class="i-checks font-bold">
                              <input type="radio" name="formtype" value="smsform" checked>
                              <i></i>
                              SMS Form
                            </label>
                          </div>
                        </div>
                       
                      </div>
                    <div class="row">
                      <label class="col-sm-3 control-label font-bold">Upload Contact List File</label>
                      <div class="col-sm-7">
                        <input type="file" name="file" id="file">
                      </div>
                      <div class="col-sm-2">
                        <button type="submit" class="btn btn-primary btn-addon btn-md pull-right"><i class="fa fa-plus"></i> Submit</button>
                      </div>
                    </div>
                  </div>
                  </form>  
                </div> -->

                <?php //print_r(session('senderr')) ."<br/>"; ?>
                <?php //print_r(session('sendsuccess')); ?>
                <div class="panel-body messagesendfrm">
                <form role="form" action="#" id="smssendfrm" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                      <div class="row">
                        <div class="col-md-6">
                          <label class="font-bold">Campaing  Name</label>
                        <input type="text" name="cam_name" id="cam_name" value="" class="form-control" placeholder="Enter campaing Name">
                        </div>
                        <div class="col-md-6">
                        <?php //echo "<pre>"; print_r($data); echo "</pre>"; ?>
                          <label class="font-bold">Sender ID <span class="text-danger">*</span></label>
                          <select name="senderid" id="senderid" class="form-control">
                            <option value="">Client senderid</option>
                            @foreach($data['data'] as $assignsender)
                              @if($assignsender['default'] == 1)
                                <option selected value="{{$assignsender['senderid']}}">{{$assignsender['senderid']}}</option>
                              @else
                                <option value="{{$assignsender['senderid']}}">{{$assignsender['senderid']}}</option>
                              @endif
                            @endforeach
                          </select>
                        </div>
                      </div>
                    </div>
                    <div class="form-group">
                      <div class="row" style="border: 1px solid #ddd; margin: 5px 1px;">
                        <div class="col-md-3 col-md-offset-3">
                          <div class="radio">
                            <label class="i-checks font-bold">
                              <input type="radio" name="numbertype" id="numbertypesingle" value="single" checked>
                              <i></i>
                              Type Number
                            </label>
                          </div>
                        </div>
                        <div class="col-md-3">
                          <div class="radio">
                            <label class="i-checks font-bold">
                              <input type="radio" name="numbertype" id="numbertypegroup" value="contgroup">
                              <i></i>
                              Contact Group
                            </label>
                          </div>
                        </div>
                        <div class="col-md-3">
                          <div class="radio">
                            <label class="i-checks font-bold">
                              <input type="radio" name="numbertype" id="numbertypeupload" value="uploadfile">
                              <i></i>
                              Upload File
                            </label>
                          </div>
                        </div>
                      </div>
                    </div>

                    <div class="form-group">
                      <div class="row">
                        <div class="col-md-12 contact_number">
                          <label class="font-bold">Number <span class="text-danger">*</span></label>
                          <textarea name="contact_number" id="contact_number" class="form-control" placeholder="8801800000000
8801700000000
8801900000000
8801600000000
8801500000000" rows="4">01873967699</textarea>
                        </div>

                        <div class="col-md-12 contactgroup" style="display: none;">
                          
                          <label for="contactgroup">Contact Group <span class="text-danger">*</span></label>
                          <select ui-jq="chosen"  id="smssent_contactgroup" name="contactgroup[]" multiple class="form-control w-md">
                              @foreach($groups as $group)  
                                  <option value="{{$group->id}}">{{$group->group_name}}</option>
                              @endforeach
                          </select>
                        </div>
                        <div class="col-md-12 contactgroup_file" style="display: none;">
                          
                            <label class="col-sm-3 control-label font-bold">Select File [xls, xlsx]</label>
                            <div class="col-sm-7">
                              <input type="file" name="file" id="file12">
                            </div>
                        </div>
                        
                      </div>
                    </div>
                    
                    <div class="form-group">
                      <div class="row">
                        <div class="form-group col-sm-6">

                          <label>Choose SMS Template</label>  
                          @if(count($usertemplates) == 0)
                            <select id="template" name="template" class="form-control" placeholder="Select Template" disabled="disabled">

                            <option value="">Choose Template</option> 

                            </select>
                          @else
                            <select id="template" name="template" class="form-control" placeholder="Select Template">

                            <option value="">Choose Template</option> 

                              @foreach($usertemplates as $template)
                                <option value="{{$template->id}}">{{$template->template_title}}</option>
                              @endforeach
                              
                            </select>
                          @endif

                        </div>
                      </div>
                      <div class="row">
                        <div class="col-md-8" id="recipient">
                          <label class="font-bold">Message Content</label>
                          <textarea name="msgcontent" id="msgcontent" rows="5" class="count_me form-control" placeholder="Enter message content"></textarea>
                          <div class="col-md-4" style="padding:0;">  Analyze SMS Count </div>
                          <div style="float: right; padding-right: 5px;">
                              <span class="parts-count">|</span>
                            </div>
                        </div>
                        <label class="font-bold" style="margin-left:14px;">SMS Type</label>
                        <div class="col-md-4">
                          <div class="row">
                        <div class="col-md-6">
                        
                          <div class="radio">
                            <label class="i-checks">
                              <input type="radio" name="sms_type" id="recipientsmsRadiosText" value="text" checked>
                              <i></i>
                              Text
                            </label>
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="radio">
                            <label class="i-checks">
                              <input type="radio" name="sms_type" id="recipientsmsRadiosUnicode" value="unicode">
                              <i></i>
                              Unicode
                            </label>
                          </div>
                        </div>
                          </div>
                        </div>
                        
                      </div>
                    </div>

                    <div class="form-group">
                        <label for="target" class="font-bold"> Schedule SMS <span style="color: red;">*</span> </label><br>
                        <span>
                      <input type="radio" class="ace send_now_checkbox" name="schedule" id="send_now_checkbox" value="1" onchange="hide_show_target_time('#content')" checked="">
                      <label class="lbl" for="send_now_checkbox">  Send Now  </label>
                    </span> &nbsp;&nbsp;
                        <span>
                      <input type="radio" class="ace send_later_checkbox" name="schedule" id="send_later_checkbox" value="2" onchange="hide_show_target_time('#content')">
                      <label class="lbl" for="send_later_checkbox"> Send Later </label>
                    </span>
                    </div>
                    <div class="form-group target_time" id="target_time" style="display: none;">
                        <label for="target" class="font-bold"> Target schedule </label>
                                <div class="input-group date" id="datetimepicker2">
                            <input type="text" name="target_time" id="date-timepicker" class="form-control date-timepicker" placeholder="MM/DD/YYYY h:mm:ss" value="">
                            <span class="input-group-addon">
                                <span class="glyphicon glyphicon-calendar"></span>
                            </span>
                        </div>
                    </div>
                    <input type="hidden" id="totalsms" name="totalsms"/>
                    <input type="hidden" id="sms_category" name="sms_category"/>
                    <input type="hidden" id="total_contacts" name="total_contacts"/>
                    <button type="button" class="btn btn-primary btn-addon btn-md pull-right setsmssend"><i class="fa fa-save"></i> Submit</button>
                    <!-- submit button action handled by --- views/layouts/smsapp.blade.php - >setsmssend -->

                  </form>
                </div>
              </div>
            </div>

            <div class="col-md-3">
              <div class="panel panel-default">
                <div class="panel-heading font-bold">Notice</div>
                <div class="panel-body">
                  
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
                                    <input type="radio" name="contactformtype" id="multipleform" value="multiple">
                                    <i></i>
                                    Multiple Number
                                  </label>
                                </div>
                              </div>
                              
                            </div>
                        </div>
                        <div class="form-group multiplenumber" style="display: none;">
                          
                            <div class="row">
                              <label class="col-sm-3 control-label font-bold">Upload Contact List File</label>
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
                            <div class="col-md-12">
                              <label class="font-bold">Mobile Number <span class="text-danger">*</span></label>
                              <input type="text" name="contact_number" id="contact_number" value="" class="form-control" placeholder="Enter mobile number">
                            </div>
                          </div>
                          <div class="row">
                            <div class="col-md-6">
                              <label class="font-bold">Name <span class="text-danger">*</span></label>
                              <input type="text" name="contact_name" id="contact_name" value="" class="form-control" placeholder="Enter nmae">
                            </div>
                            <div class="col-md-6">
                              <label class="font-bold">Email <span class="text-danger">*</span></label>
                              <input type="text" name="email" id="email" value="" class="form-control" placeholder="Enter email">
                            </div>
                          </div> 

                          <div class="row">
                            <div class="col-md-6">
                              <label class="font-bold">Gender <span class="text-danger">*</span></label>
                              <select name="gender" id="gender" class="form-control">
                                <option value="">Select gender</option>
                                  <option value="male">Male</option>
                                  <option value="female">Female</option>
                              </select>
                            </div>
                            <div class="col-md-6">
                              <label class="font-bold">DOB <span class="text-danger">*</span></label>
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
                <button type="submit" class="btn btn-primary btn-addon btn-md btncontactingroup"><i class="fa fa-save"></i> Submit</button>
                


                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
              </div>
            </form>
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
        <script>

function hide_show_target_time(check_id) {
        if ($(check_id + ' .send_now_checkbox').is(":checked")) {
            $(check_id + " .target_time").hide();
        } else if ($(check_id + ' .send_later_checkbox').is(":checked")) {
            $(check_id + " .target_time").show();
        }
    }
        </script>
@endsection