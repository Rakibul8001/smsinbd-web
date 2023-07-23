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
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4>View Customer's Ledger</h4>
                    </div>
                    <div class="panel-body">
                        <form action="{{route('customer-ledger-details')}}">
                            @csrf
                            <div class="form-group">
                                <div class="col-md-6">
                                    <label class="font-bold">Select Customer<span class="text-danger">*</span></label>
                                    <select name="customer" id="customer" class="form-control select2" required>
                                        <?php
                                        foreach ($customers as $customer) {
                                            ?>
                                            <option value="{{ $customer->id}}">{{ $customer->name}} - {{ $customer->phone }}</option>
                                            <?php
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <button type="submit" class="btn btn-success" style="margin-top:20px;">View Customer's Ledger</button>
                                </div>
                            </div>

                        </form>
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
    $(".select2").select2();

    
  } );
</script>

@endsection