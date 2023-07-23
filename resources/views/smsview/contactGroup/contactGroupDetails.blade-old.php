@extends('layouts.smsapp')

@section('appbody')
  <style type="text/css">
    .modal-dialog {
        width: 1200px;
        margin: 30px auto;
    }

    .modal-dialog-small {
        width: 700px;
        margin: 30px auto;
    }
    
    table.dataTable.nowrap th, table.dataTable.nowrap td {
        white-space: break-spaces;
    }
    #contactGroupDetails_filter{
      display: none;
    }
  </style>
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
                  <h1 class="m-n font-thin h3 text-black font-bold">Contact Group Details</h1>
                  <small class="text-muted"></small>
              </div>
            </div>
        </div>

        


        <!-- / main header -->
        <div class="wrapper-md" ng-controller="FlotChartDemoCtrl">
          <a class="btn btn-primary btn-addon btn-md pull-right mb-5 addsenderid" style="margin-bottom:10px; z-index: 99; position:relative;" data-toggle="modal" data-target="#addContactInGroup"><i class="fa fa-plus"></i> Add/Upload Contacts</a>
          <div class="row">
           <div class="col-md-12">
              <div class="panel panel-default dataTables_wrapper">
                <div class="panel-heading font-bold">
                  Contact Group Details
                </div>
                <div class="panel-body">
                  <div class="col-md-5">
                    <table class="table table-bordered table-responsive ">
                      <tbody>
                        <tr>
                          <td>
                            Contact Group Name: 
                          </td>
                          <td>
                            {{ $contactGroup->group_name }} 
                          </td>
                        </tr>
                        <tr>
                          <td>
                            Status:
                          </td>
                          <td>
                            {{ $contactGroup->status==1? 'Enabled' : 'Disabled' }} 
                          </td>
                        </tr>
                        <tr>
                          <td>
                            <b>Number of Contacts: </b>
                          </td>
                          <td>
                            {{ $totalContacts }} 
                          </td>
                        </tr>
                        

                      </tbody>
                    </table>
                  </div>

                </div>
              </div>
            </div>
          </div>
          <div class="row">
              <div class="col-md-12">
                  <div class="panel panel-default dataTables_wrapper">
                      <div class="panel-heading font-bold">
                        Contact Group Numbers
                      </div>
                      <div class="table-responsive ">
                        <table class="table table-striped table-hover display no-footer dtr-inline dataTable" id="contactGroupDetails" style="width: 100%;" role="grid" aria-describedby="example_info">
                          <thead>
                            <tr>
                              
                              <th>Mobile Number</th>
                              <th>Name</th>
                              <th>Email</th>
                              <th>Gender</th>
                            </tr>
                          </thead>
                          <thead>
                              <tr>
                                <th>
                                  <input type="text" data-column="0"  class="search-input-text" placeholder="Search Number" >
                                </th>
                                <th>
                                  <input type="text" data-column="1"  class="search-input-text" placeholder="Search Name" >
                                </th>
                                <th></th>
                                <th></th>
                              </tr>
                          </thead>

                          <tfoot>
                            <tr>
                              <th>Mobile Number</th>
                              <th>Name</th>
                              <th>Email</th>
                              <th>Gender</th>
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

        <!-- Modal -->
        
        <!-- / main -->
        <!-- right col -->
        
        <!-- / right col -->
        </div>
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

                        <input type="hidden"  id="contactgroupid" name="contactgroupid" value="{{ $contactGroup->id }}">
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
        </div>
        <!-- /content -->
        
@endsection

@section('scripts')

<script>

  //dataTABLE
  $(document).ready(function() {
    
    var dataTable = $('#contactGroupDetails').DataTable( {
      "processing": true,
      "language": {
          "processing": "<div class='overlay'><i class='fa fa-refresh fa-spin'></i></div>"
      },
      "columnDefs": [ {
            "targets": 'no-sort',
            "orderable": false,
      } ], //desable sorting
      "bAutoWidth": false ,

      "serverSide": true,

      "ajax":{
        url :"{{ route('contact-group-numbers', $contactGroup->id) }}", // json datasource
        type: "post",  // method  , by default get
        error: function(){  // error handling
          $(".employee-grid-error").html("");
          $("#employee-grid").append('<tbody class="employee-grid-error"><tr><th colspan="3">No Data Found!</th></tr></tbody>');
          $("#employee-grid_processing").css("display","none");
          
        },
        complete: function() {
          //
        }
      }
    } );    



    $("#employee-grid_filter").css("display","none");  // hiding global search box
    $('.search-input-text').on( 'keyup click', function () {   // for text boxes
      var i =$(this).attr('data-column');  // getting column index
      var v =$(this).val();  // getting search input value
      dataTable.columns(i).search(v).draw();
    } );



    //create-contacts

    $('body').on('click','.btncontactingroup',function(e){
      e.preventDefault();
      let form = $('#contactingroupfrm')[0];
      let form_data = new FormData(form);
      let contactformtype = document.querySelectorAll('input[name="contactformtype"]');
      let formtype = '';
      let data = {};
      let isValidate = true;
      var postUrl = '{{ route("create-contacts") }}';
      contactformtype.forEach(formtype => {
        
        if(formtype.checked)
        {
          formtype = formtype.value;
          if (formtype == 'single')
          {
            if ($('#contact_number').val() == "")
            {
              alert("Mobile number can't left empty");
              $('#contact_number').focus()
              isValidate = false;
              return false;
            }
            form_data.append('contact_number', document.querySelector('#contact_number').value);
            form_data.append('contact_name', document.querySelector('#contact_name').value);
            form_data.append('email', document.querySelector('#email').value);
            form_data.append('gender', document.querySelector('#gender').value);
            form_data.append('dob', document.querySelector('#dob').value);
            form_data.append('contactgroup', document.querySelector('#contactgroupid').value);
          }

          if (formtype == 'multiple')
          {
            if ($('#file').val() == "")
            {
              alert("File can't left empty");
              isValidate = false;
              $('#file').focus();
              return false;
            }
            postUrl = "{{ config('apiconfig.api_url') }}/sms-api/uploadcontacts";
            form_data.append('api_token','{{ $api_token }}');
            form_data.append('contactgroup', document.querySelector('#contactgroupid').value);

            document.querySelector('#contact_number').value = '';
            document.querySelector('#contact_name').value = '';
            document.querySelector('#email').value = '';
            document.querySelector('#gender').value = '';
            document.querySelector('#dob').value = '';
          }
        }
      });

      $.ajax({
        url: postUrl,
        type: 'post',
        data: form_data,
        processData: false,
        contentType: false,
        beforeSend: function(){
          if (isValidate == false) {
            $('.insrecord').text('Required field can\'t left empty');
            return false;
          }
          $('.insrecord').css({'display':'block'});
          $('.insrecord').text('Processing.....wait please');
        },
        success: function(res) {
          $('.insrecord').text(res.msg);
          $('.insrecord').fadeIn();
          setTimeout(function(){
            $('.insrecord').fadeOut();
            document.querySelector('#contact_number').value = '';
            document.querySelector('#contact_name').value = '';
            document.querySelector('#email').value = '';
            document.querySelector('#gender').value = '';
            document.querySelector('#dob').value = '';
            document.querySelector('#file').value = '';
            $('.file-caption-name').children().text('');
            $('#id').val('');
            $('#frmmode').val('ins');
            window.location.reload();
          },500);
          
        },
        error: function(err) {
          $('.notinsrecord').text(err.responseJSON.errmsg);
          $('.notinsrecord').fadeIn();
          setTimeout(function(){
            $('.notinsrecord').fadeOut();
          },500);
        }
      });
    });

    
  } );
</script>

@endsection