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
    #contactGroupTable_filter{
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
                  <h1 class="m-n font-thin h3 text-black font-bold">Group List</h1>
                  <small class="text-muted"></small>
              </div>
            </div>
        </div>
        <!-- / main header -->
        <div class="wrapper-md" ng-controller="FlotChartDemoCtrl">
        <a class="btn btn-primary btn-addon btn-md pull-right mb-5 addsenderid" style="margin-bottom:10px; z-index: 99; position:relative;" data-toggle="modal" data-target="#addContactGroup"><i class="fa fa-plus"></i> Create New</a>
            <div class="row">
              <div class="col-md-12">
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
                        Contact Group List
                      </div>
                      <div class="table-responsive dataTables_wrapper">
                        <table class="table table-striped table-hover dt-responsive display nowrap no-footer dtr-inline dataTable collapsed" style="width: 100%;" role="grid" aria-describedby="example_info" id="contactGroupTable">
                          <thead>
                            <tr>
                              <th>Name</th>
                              <th>No. of Contacts</th>
                              <th>Created At</th>
                              <th>Status</th>
                              <th class="no-sort">Action</th>
                            </tr>
                          </thead>
                          <tbody>
                          </tbody>
                          <tfoot>
                            <tr>
                              <th>Name</th>
                              <th>No. of Contacts</th>
                              <th>Created At</th>
                              <th>Status</th>
                              <th class="no-sort">Action</th>
                            </tr>
                          </tfoot>
                        </table>
                      </div>
                </div>
              </div>
          </div>
        </div>


        <!-- Modal -->
        <div class="modal bd-example-modal-md fade" id="addContactGroup" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
          <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title font-bold contactgrouphead" id="exampleModalLabel">Add New Contact Group</h5>
               
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="margin-top: -22px;">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <form role="form" action="#" id="smssenderadd" method="post">
                @csrf
              <div class="modal-body">
                <div class="panel panel-default">
                  <div class="panel-heading font-bold">Contact Group Name <span class="insrecord pull-right text-success"></span><span class="notinsrecord pull-right text-danger"></span></div>
                    
                    <div class="panel-body">
                    
                        <div class="form-group">
                          <div class="row">
                            <div class="col-md-12">
                              <label class="font-bold">Name <span class="text-danger">*</span></label>
                              <input type="text" name="group_name" id="group_name" value="" class="form-control" placeholder="Enter group Name">
                            </div>
                          </div> 
                        </div>

                        <div class="form-group row">
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

                        </div>
                    </div>
                  </div>
              </div>
              <div class="modal-footer">
                <input type="hidden" name="frmmode" id="frmmode" value="ins"/>
                <input type="hidden" name="id" id="id"/>
                <button type="submit" class="btn btn-primary btn-addon btn-md btncontactgroup"><i class="fa fa-plus"></i> Submit</button>
                
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

@section('scripts')

<script>

  //dataTABLE
  $(document).ready(function() {
    
    var dataTable = $('#contactGroupTable').DataTable( {
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
        url :"{{ route('contact-groups-data') }}", // json datasource
        type: "post",  // method  , by default get
        error: function(){  // error handling
          $(".employee-grid-error").html("");
          $("#employee-grid").append('<tbody class="employee-grid-error"><tr><th colspan="3">No Data Found!</th></tr></tbody>');
          $("#employee-grid_processing").css("display","none");
          
        },
        complete: function() {
          $(".confirm").confirm({
            title: 'Please Confirm!',
            content: 'Do you really want to delete this?'
          });
        }
      }
    } );    



    $("#employee-grid_filter").css("display","none");  // hiding global search box
    $('.search-input-text').on( 'keyup click', function () {   // for text boxes
      var i =$(this).attr('data-column');  // getting column index
      var v =$(this).val();  // getting search input value
      dataTable.columns(i).search(v).draw();
    } );

    
  } );
</script>


<script>

  $('body').on('click','.btncontactgroup', function(e){
      e.preventDefault();
      let status = 0;
      let frmmode = $('#frmmode').val();
      if ($('#senderid_status_yes').is(':checked') == true) {
        status = $('#senderid_status_yes').val();
      }

      if ($('#senderid_status_no').is(':checked') == true) {
        status = $('#senderid_status_no').val();
      }

      $.ajax({
        url: "{{ route('create-group') }}",
        type: 'post',
        data: {
          id: $('#id').val(),
          group_name: $('#group_name').val(),
          status: status,
          frmmode: frmmode
        },
        beforeSend: function()
        {
          if ($('#group_name').val() == "")
          {
            alert("Group name can't left empty");
            $('#group_name').focus();
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
            $('#group_name').val('');
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
      })
    });
</script>



@endsection