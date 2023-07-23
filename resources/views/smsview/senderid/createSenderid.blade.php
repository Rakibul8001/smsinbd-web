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
                  <h1 class="m-n font-thin h3 text-black font-bold">Manage Client Sender IDs</h1>
                  <small class="text-muted"></small>
              </div>
            </div>
        </div>

        <!-- / main header -->

        <div class="wrapper-md" ng-controller="FlotChartDemoCtrl">
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

          @if(Auth::guard('root')->check())
          <form role="form" action="{{ route('create-senderid-post') }}" method="post">
            @csrf
            <div class="row">
              <div class="col-md-12">
                <div class="panel panel-default dataTables_wrapper">
                  <div class="panel-heading font-bold">
                    <h4>Create Sender Id</h4>
                  </div>

                  <div class="panel-body">
                    <div class="form-group">
                      <div class="row">
                        <div class="col-md-6">
                          <label class="font-bold">Master Sender ID <span class="text-danger">*</span></label>
                            <input type="text" name="sender_name" id="sender_name" value="{{old('sender_name')}}" class="form-control" placeholder="Enter Master Senderid Name" required maxlength="13">
                            {{-- @if($errors->has('sender_name')) --}}
                                <label class="col-sm-12 font-bold text-danger control-label">{{$errors->first('sender_name')}}</label>
                            {{-- @endif --}}
                            
                        </div>
                        <div class="col-md-6" style="margin-top: 20px;">
                          <label for="type" class="col-sm-2 font-bold" style="margin-top: 10px;">Sender ID Type</label>
                          <div class="col-md-2">
                              
                            <div class="radio">
                              
                              <label class="i-checks">
                                <input type="radio" name="type" value="1" checked>
                                <i></i>
                                Mask
                              </label>
                            </div>
                          </div>
                          <div class="col-md-2">
                            <div class="radio">
                              <label class="i-checks">
                                <input type="radio" name="type" value="2">
                                <i></i>
                                Nonmask
                              </label>
                            </div>
                          </div>
                          <div class="col-md-2">
                            <div class="radio">
                              <label class="i-checks">
                                <input type="radio" name="type" value="3">
                                <i></i>
                                Voice
                              </label>
                            </div>
                          </div>
                        </div>
                      </div> 
                    </div>
                    <div class="form-group">
                      <div class="row">
                        <div class="col-md-12">
                          <label class="font-bold col-md-1 text-right" for="description">Description</label>
                          
                          <div class="col-md-5">
                            <textarea name="description" id="description" class="form-control" placeholder="Enter Description" maxlength="255">{{old('description')}}</textarea>

                            {{-- @if($errors->has('description')) --}}
                                <label class="col-sm-12 font-bold text-danger control-label">{{$errors->first('description')}}</label>
                            {{-- @endif --}}
                          </div>
                        </div>
                      </div>
                    </div>


                    <div class="form-group general-senderid" style="margin-top: 10px;">
                      <table class="table table-bordered table-striped" style="width: 100%;" role="grid" aria-describedby="example_info">
                        <thead>
                          <tr>
                            <th>Input Operator</th>
                            <th>Output Operator</th>
                            <th>Gateway</th>
                            <th>Associate Sender ID</th>
                          </tr>
                        </thead>
                        <tbody>
                        <?php $i = 0; ?>
                        @foreach($gsmOperators as $operator)

                          <tr>
                            <td>
                              <input type="hidden" name="input_operator[{{$operator->id}}]" value="{{$operator->id}}"/>
                              {{$operator->name}}
                              </td>
                            <td>
                              <select name="output_operator[{{$operator->id}}]" class="form-control outputOperator" key="{{$operator->id}}" required>
                                <option value="">Select Output Operator</option>
                                @foreach($allOperators as $opt)
                                  <option value="{{ $opt->id }}">{{ $opt->name }}</option>
                                @endforeach
                              </select>
                            </td>
                            <td>
                              <select name="gateway[{{$operator->id}}]" id="gateways{{$operator->id}}" class="form-control" required>
                              </select>
                            </td>
                            <td>
                              <div class="" id="associateSender{{$operator->id}}">
                                <input type="text" name="associate_sender[{{$operator->id}}]" class="form-control" placeholder="Enter associate Sender ID" maxlength="13">
                              </div>
                              <div class="" id="teletalkSender{{$operator->id}}" style="display: none;">
                                <input type="text" name="username[{{$operator->id}}]" placeholder="Username" class="form-control">
                                <input type="text" name="password[{{$operator->id}}]" placeholder="Password" class="form-control">
                              </div>
                            </td>
                          </tr>
                          
                        @endforeach
                        </tbody>
                        <tfoot>
                        <tr>
                          <th>Input Operator</th>
                          <th>Output Operator</th>
                          <th>Gateway</th>
                          <th>Associate Sender ID</th>
                        </tr>
                        </tfoot>
                        <tbody>
                        </tbody>
                      </table>
                        
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

                    <button type="submit" class="btn btn-primary btn-addon btn-md pull-right"><i class="fa fa-plus"></i> Create Sender ID</button>

                  </div>
                </div>
              </div>
            </div>
          </form>
          @endif
            
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
  $(".outputOperator").change(function() {
    var outputOperator = $(this).val();
    var key = $(this).attr('key');

    if (outputOperator=='5') {
      $("#teletalkSender"+key).show();
      $("#associateSender"+key).hide();
    } else {
      $("#teletalkSender"+key).hide();
      $("#associateSender"+key).show();
    }

    if (outputOperator!='') {
      $.ajax({
        url: "{{route('gateway-of-operator')}}",
        type: "post",
        data: {
          operator: outputOperator
        },
        success: function(res) {
          var gateways = JSON.parse(res);
          var gatewayString = "";
          for (var i = gateways.length - 1; i >= 0; i--) {
             gatewayString += '<option value="'+gateways[i].id+'">'+gateways[i].name+'</option>';
          }

          $("#gateways"+key).html(gatewayString);
        },
        error: function(err) {
          console.log(err);
        }
      })
    }

  })

</script>

@endsection

