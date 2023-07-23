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
    #singleSmsReport_filter{
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
                  <h1 class="m-n font-thin h3 text-black font-bold">Single SMS Report</h1>
                  <small class="text-muted"></small>
              </div>
            </div>
        </div>

        


        <!-- / main header -->
        <div class="wrapper-md" ng-controller="FlotChartDemoCtrl">
          
            <div class="row">
              <div class="col-md-12">
                  <div class="panel panel-default dataTables_wrapper">
                      <div class="panel-heading font-bold">
                        Single SMS Report
                      </div>
                      <div class="table-responsive ">
                        <table class="table table-striped table-hover display no-footer dtr-inline dataTable" id="singleSmsReport" style="width: 100%;" role="grid" aria-describedby="example_info">
                          <thead>
                            <tr>
                              
                              <th>Sms ID</th>
                              <th>SenderID</th>
                              <th>SMS Type</th>
                              <th>Send To</th>
                              <th>Send From</th>
                              <th>Sms Count</th>
                              <th>Date</th>
                              <th>Status</th>
                              <th>Content</th>
                            </tr>
                          </thead>
                          <thead>
                              <tr>
                                <th>
                                  <input type="text" data-column="0"  class="search-input-text" placeholder="Search ID" style="max-width: 100px;">
                                </th>
                                <th>
                                  <input type="text" data-column="1"  class="search-input-text" placeholder="SenderId" style="max-width: 100px;">
                                </th>
                                <th></th>
                                <th>
                                  <input type="text" data-column="3"  class="search-input-text" placeholder="Number" style="max-width: 100px;">
                                </th>
                                <th></th>
                                <th></th>
                                <th>
                                  <input type="text" data-column="6"  class="search-input-text" placeholder="Search Date" style="max-width: 100px;">
                                </th>
                                <th></th>
                                <th></th>
                              </tr>
                          </thead>

                          <tfoot>
                            <tr>
                              <th>Sms ID</th>
                              <th>SenderID</th>
                              <th>SMS Type</th>
                              <th>Send To</th>
                              <th>Send From</th>
                              <th>Sms Count</th>
                              <th>Date</th>
                              <th>Status</th>
                              <th>Content</th>
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

@section('scripts')

<script>

  //dataTABLE
  $(document).ready(function() {
    
    var dataTable = $('#singleSmsReport').DataTable( {
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
        url :"{{ route('single-sms-report-data') }}", // json datasource
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

    
  } );
</script>

@endsection