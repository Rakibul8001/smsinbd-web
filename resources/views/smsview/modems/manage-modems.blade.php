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
                  <h1 class="m-n font-thin h3 text-black font-bold">Modem List</h1>
                  <small class="text-muted"></small>
              </div>
            </div>
        </div>

        <!-- / main header -->

        <!-- Modal -->
        <div class="modal bd-example-modal-lg fade" id="operatorlistModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
          <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title font-bold" id="exampleModalLabel">Edit Modem</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="margin-top: -22px;">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <form role="form" action="{{ route('edit-modem') }}" id="smsoperatoredit" method="post">
                @csrf
              <div class="modal-body">
                <div class="panel panel-default">
                  <div class="panel-heading font-bold">Modems' INFORMATION</div>
                    
                    <div class="panel-body">
                    
                        <div class="form-group">
                          <div class="row">
                            <div class="col-md-12">
                              <label class="font-bold">Modem Name <span class="text-danger">*</span></label>
                              <input type="text" name="name" id="name" value="{{ old('name')}}" class="form-control {{$errors->has('name') ? 'border-danger': ''}}" placeholder="Enter Operator Name">
                              {{-- @if($errors->has('name')) --}}
                                  <label class="col-sm-12 font-bold text-danger control-label">{{$errors->first('name')}}</label>
                              {{-- @endif --}}
                            </div>
                          </div> 
                        </div>

                        <div class="form-group">
                          <div class="row">
                            <div class="col-md-12">
                              <label class="font-bold">Modem's Sim Number <span class="text-danger">*</span></label>
                                <input type="text" name="sim_number" id="sim_number" value="{{ old('sim_number')}}" class="form-control {{$errors->has('sim_number') ? 'border-danger': ''}}" placeholder="Enter Modem's Sim Number">
                                {{-- @if($errors->has('sim_number')) --}}
                                    <label class="col-sm-12 font-bold text-danger control-label">{{$errors->first('sim_number')}}</label>
                                {{-- @endif --}}
                            </div>
                          </div> 
                        </div>


                        <div class="form-group">
                          <div class="row">
                            <div class="col-md-12">
                              <label class="font-bold">Modem Description <span class="text-danger">*</span></label>
                                <input type="text" name="description" id="description" value="{{ old('description')}}" class="form-control {{$errors->has('description') ? 'border-danger': ''}}" placeholder="Enter Modem Description">
                                {{-- @if($errors->has('description')) --}}
                                    <label class="col-sm-12 font-bold text-danger control-label">{{$errors->first('description')}}</label>
                                {{-- @endif --}}
                            </div>
                          </div> 
                        </div>

                        <div class="form-group" style="margin-top: 10px;">
                          <div class="row">     
                              <div class="col-md-2">
                                <label class="font-bold">Enabled</label>
                              </div>
                              <div class="col-md-2">
                                <input type="checkbox" name="active" id="active" value="" class="i-switch m-t-xs m-r form-control">
                              </div>
                          </div>
                        </div>

                        <div class="form-group" style="margin-top: 10px;">
                          <div class="row">     
                              <div class="col-md-2">
                                <label class="font-bold">API KEY</label>
                              </div>
                              <div class="col-md-2">
                                <strong id="key_show_id"><h5 id="api_key" class="text-black"></h5></strong>

                              </div>
                          </div>
                        </div>

                      
                    </div>
                  </div>
              </div>
              <div class="modal-footer">
                <input type="hidden" name="modem_id" id="modem_id" value="">
                <button type="submit" class="btn btn-primary btn-addon btn-md"><i class="fa fa-plus"></i> Submit</button>
                
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
              </div>
            </form>
            </div>
          </div>
        </div>

        

        <div class="wrapper-md" ng-controller="FlotChartDemoCtrl">
            <a class="btn btn-primary btn-addon btn-md pull-right mb-5" style="margin-bottom:10px; z-index: 99; position:relative;" href="{{route('add-modems')}}"><i class="fa fa-plus"></i> Add New Modem</a>
            <div class="row">
             
              <div class="col-md-12">
                   @if(session()->has('msg'))
                    <div class="alert alert-success font-weight-bold clientsuccess" role="alert">
                      {{session()->get('msg')}}
                    </div>
                  @endif 
                  <div class="panel panel-default dataTables_wrapper">
                      <div class="panel-heading font-bold">
                        SMS Operators List
                        
                      </div>
                      <div class="table-responsive">
                        <table class="display dataTable dtr-inline " style="width: 100%;" role="grid" aria-describedby="example_info">
                          <thead>
                            <tr>
                              <th>Name</th>
                              <th>Sim Number</th>
                              <th>Description</th>
                              <th>Enabled</th>
                              <th>Status</th>
                              <th class="actions">Action</th>
                            </tr>
                          </thead>

                          <tbody>
                            @foreach($modems as $key => $modem)
                            <tr>
                              <td>{{ $modem->name }}</td>
                              <td>{{ $modem->sim_number }}</td>
                              <td>{{ $modem->description }}</td>
                              <td>{{ $modem->active==1 ? 'Enabled' : 'Disabled' }}</td>
                              <td>{{ $modem->status==1 ? 'Active' : 'Down' }}</td>
                              <td>
                                  <a href="#" class="btn btn-sm btn-icon btn-pure btn-default modemEdtfrm"  data-toggle="modal" data-target="#operatorlistModal" data-original-title="edit" data-id="{{ $modem->id }}" data-name="{{ $modem->name }}" data-sim_number="{{ $modem->sim_number }}" data-description="{{ $modem->description }}" data-status="{{ $modem->active }}" data-api_key="{{ $modem->api_token }}"><i class="icon icon-eye" aria-hidden="true"></i></a>
                                    ----

                                  <a href="{{route('regenerate-modem-apitoken', $modem->id)}}" class="btn btn-sm btn-danger">Regenerate Key <i class="fa fa-plus"></i></a>
                              </td>
                            </tr>
                            @endforeach
                          </tbody>
                          
                          <tfoot>
                            <tr>
                              <tr>
                              <th>Name</th>
                              <th>Sim Number</th>
                              <th>Description</th>
                              <th>Enabled</th>
                              <th>Status</th>
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
  $('body').on('click','.modemEdtfrm', function(){
      let id = $(this).data('id');
      let name = $(this).data('name');
      let sim_number = $(this).data('sim_number');
      let description = $(this).data('description');
      let status = $(this).data('status');
      let api_key = $(this).data('api_key');

      $('#smsoperatoredit #modem_id').val(id);
      $('#smsoperatoredit #name').val(name);
      $('#smsoperatoredit #sim_number').val(sim_number);
      $('#smsoperatoredit #description').val(description);
      $('#smsoperatoredit #api_key').html(api_key);
      
      if (status == '1'){
        $('#smsoperatoredit #active').val(status);
        $('#smsoperatoredit #active').prop('checked',true);
      }

    });
</script>


@endsection