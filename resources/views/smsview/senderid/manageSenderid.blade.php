@extends('layouts.smsapp')

@section('appbody')

<style type="text/css">
  
  table.dataTable.nowrap th, table.dataTable.nowrap td {
      white-space: break-spaces;
  }
  #data_table_filter{
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
                  <h1 class="m-n font-thin h3 text-black font-bold">Manage Client Sender IDs</h1>
                  <small class="text-muted"></small>
              </div>
            </div>
        </div>

        <!-- clients -->
        <div class="modal bd-example-modal-lg fade" id="assignsenderid" tabindex="-1" role="dialog" aria-labelledby="operatorapiModalLabel" aria-hidden="true">
          <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title font-bold" id="operatorapiModalLabel">Assign Sender Id to Clients</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="margin-top: -22px;">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <form role="form" action="{{route('assign-senderid')}}" method="post">
                @csrf
                <div class="load-senderid"></div>
              <div class="modal-footer">
                <input type="hidden" name="sender_id" id="client_sender_id"/>
                <input type="hidden" name="client_sender_name" id="client_sender_name"/>
                <button type="submit" class="btn btn-primary btn-addon btn-md"><i class="fa fa-plus"></i> Submit</button>
                
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
              </div>
            </form>
            </div>
          </div>
        </div>

        <!-- resellers -->
        <div class="modal bd-example-modal-lg fade" id="assignsenderidReseller" tabindex="-1" role="dialog" aria-labelledby="operatorapiModalLabel" aria-hidden="true">
          <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title font-bold" id="operatorapiModalLabel">Assign Sender Id to Resellers</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="margin-top: -22px;">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <form role="form" action="{{route('assign-senderid-resellers')}}" method="post">
                @csrf
                <div class="load-senderidResellers"></div>
              <div class="modal-footer">
                <input type="hidden" name="sender_id" id="reseller_sender_id"/>
                <input type="hidden" name="reseller_sender_name" id="reseller_sender_name"/>
                <button type="submit" class="btn btn-primary btn-addon btn-md"><i class="fa fa-plus"></i> Submit</button>
                
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
              </div>
            </form>
            </div>
          </div>
        </div>

        <!-- / main header -->

        <div class="wrapper-md">
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
                  <div class="panel panel-default">
                    <table id="data_table" class="table table-bordered table-hover">
                      <thead>
                        <tr>
                          <th>Sender ID</th>
                          <th>Type</th>
                          <th>Description</th>
                          <th>Status</th>
                          <th>Created By</th>
                          <th class="actions">Action</th>
                        </tr>
                      </thead>
                      <thead>
                          <tr>
                            <th><input type="text" data-column="0"  class="search-input-text" placeholder="Search Sender ID"></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
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
    var dataTable = $('#data_table').DataTable( {
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
        url :"{{ route('senderid.get-senderids') }}", // json datasource
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

    
    $('body').on('click','.assignsenderid', function(){
        $('#activeclients').empty();
        let id = $(this).data('id');
        let sendername = $(this).data('sendername');
        
        $('#client_sender_id').val(id);
        $('#client_sender_name').val(sendername);
        $('#activeclients').trigger("chosen:updated");
        
    });
    $('#activeclients').trigger("chosen:updated");
    

    $('#assignsenderid').on('shown.bs.modal', function(){
      
      let id = $('#client_sender_id').val();
      let sendername = $('#client_sender_name').val();
      $('#activeclients').empty();
      $('.load-senderid').html('');
      $('.load-senderid').load("{{ route('load-assigned-sender-id')}}"+'?id='+id, function() {
            // callback function
            $('#activeclients').select2();
          });
      $('#activeclients').trigger("chosen:updated");
       
    });



    $('body').on('click','.assignsenderidReseller', function(){
        $('#activeResellers').empty();
        let id = $(this).data('id');
        let sendername = $(this).data('sendername');
        
        $('#reseller_sender_id').val(id);
        $('#reseller_sender_name').val(sendername);
        $('#activeResellers').trigger("chosen:updated");
        
    });
    $('#activeResellers').trigger("chosen:updated");
    

    $('#assignsenderidReseller').on('shown.bs.modal', function(){
      
      let id = $('#reseller_sender_id').val();
      let sendername = $('#reseller_sender_name').val();
      $('#activeResellers').empty();
      $('.load-senderidResellers').html('');
      $('.load-senderidResellers').load("{{ route('load-assigned-sender-id-reseller')}}"+'?id='+id, function() {
            // callback function 
              $('#activeResellers').select2();
          });
      $('#activeResellers').trigger("chosen:updated");
       
    });



    $('body').on('click','.senderclientdtl', function(){
      let senderid_users_id = $(this).data('assign_user_senderid');
      let senderid = $(this).data('sms_sender_id');
      let tr = $(this).closest('tr');

      swal({
        title: "Are you sure?",
        text: "Once deleted, you will not be able to recover this record!",
        icon: "warning",
        buttons: true,
      })
      .then((willDelete) => {
        console.log(willDelete);
        if (willDelete) {

          $.ajax({
            url: `/delete-client-senderid/${senderid_users_id}/${senderid}`,
            type: 'get',
            success: function(res) {
              swal(res.msg, {
                icon: "success",
              });
              tr.fadeOut(400,function(){
                  tr.remove();
              });
            },
            error: function(err) {
              swal(err.responseJSON.errmsg, {
                icon: "error",
              });
            }
          });

        } 
        /*else {
          swal("Your imaginary file is safe!");
        }
        */
      });


    });

    $('body').on('click','.senderidResellerDelete', function(){
      let resellerid = $(this).data('senderid_reseller');
      let senderid = $(this).data('sms_sender_id');
      let tr = $(this).closest('tr');

      swal({
        title: "Are you sure?",
        text: "Once deleted, you will not be able to recover this record!",
        icon: "warning",
        buttons: true,
      })
      .then((willDelete) => {
        console.log(willDelete);
        if (willDelete) {

          $.ajax({
            url: `/delete-reseller-senderid/${resellerid}/${senderid}`,
            type: 'get',
            success: function(res) {
              swal(res.msg, {
                icon: "success",
              });
              tr.fadeOut(400,function(){
                  tr.remove();
              });
            },
            error: function(err) {
              swal(err.responseJSON.errmsg, {
                icon: "error",
              });
            }
          });

        }
        /*else {
          swal("Your imaginary file is safe!");
        }
        */
      });


    });
    
  } );
</script>

@endsection
