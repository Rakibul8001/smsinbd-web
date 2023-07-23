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
               height: 70px;
               width: 70px;
               text-align: center;
               line-height: 67px;
               font-size: 24px;
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
           <div class="pull-right pull-none-xs text-center">
           <a href class="m-b-md inline m">
               <span class="h3 block font-bold">Mask</span>
               <small>{{$totalmaskbal}}</small>
           </a>
           <a href class="m-b-md inline m">
               <span class="h3 block font-bold">Non Mask</span>
               <small>{{$totalnonmaskbal}}</small>
           </a>
           <a href class="m-b-md inline m">
               <span class="h3 block font-bold">Voice</span>
               <small>{{$totalvoicebal}}</small>
           </a>
           </div>
       </div>
       </div>
   </div>
   </div>
   <div class="wrapper bg-white b-b">
   @if(Auth::guard('reseller')->check())  
       @include('smsview.rootadmin.profiletab-reseller-client')
   @else 
       @include('smsview.rootadmin.profiletab')
   @endif
   </div>
   <div class="padder">    
       @if(session()->has('msg'))
           <div class="alert alert-success font-weight-bold clientsuccess" role="alert">
           {{session()->get('msg')}}
           </div>
       @endif  
       <div class="alert alert-danger font-weight-bold clientunsuccess" style="display:none;" role="alert"></div>
       <div>
       <div class="container profile-senderid" style="margin-top:30px;" data-current_profileid="{{$request->userid}}">
                       <div class="row">
                           

                           <div class="col-md-10 col-xs-12">
                              <div class="panel panel-default">
                                 <div class="panel-heading font-bold">Assign Senderid 
                                   </div>
                                <form role="form" action="{{route('assign-senderid-by-reseller', $user->id)}}" method="post">
                                @csrf
                                <div class="panel-body">
                                    
                                    <div class="form-group">
                                      <div class="row">
                                          <div class="col-md-12">
                                          </div>
                                          <div class="col-md-6">
                                              <label class="font-bold">Assigned Sender ID <span class="text-danger">*</span></label>

                                              <table class="table table-bordered">
                                                <thead>
                                                  <th>Sender Id</th>
                                                  <th class="text-center">Action</th>
                                                </thead>
                                                <tbody>
                                                 
                                                    @foreach($assignedSenderIds as $assignedSenderId)

                                                    <tr>
                                                      <td>{{$assignedSenderId->getSenderid->name}}</td>
                                                      <td class="text-center">
                                                        @if(Auth::guard('root')->check() || Auth::guard('reseller')->check()) 
                                                        <a href="#" data-assign_user_senderid="{{$user->id}}" data-sms_sender_id="{{$assignedSenderId->senderid}}" class="btn btn-sm btn-icon btn-pure btn-default senderidDelete"><i class="icon icon-trash"></i></a>
                                                        @endif
                                                      </td>
                                                    </tr>
                                                    @endforeach

                                                </tbody>
                                              </table>
                                          </div>
                                          <div class="col-md-6">
                                              <label for="resellerSenderid col-md-4">Select Senderid</label>
                                              <select  name="resellerSenderid[]" multiple class="form-control w-md col-md-8 select2" style="width: 100%;">
                                              @foreach($senderids as $senderid)

                                                  <option value="{{$senderid->senderid}}">{{$senderid->getSenderid->name}}</option>
                                                        
                                                     
                                                @endforeach 
                                                
                                              </select>
                                          </div>
                                      </div> 
                                    </div>
                                    

                                </div>
                                <div class="panel-footer text-right">
                                    <input type="hidden" name="sender_id" id="client_sender_id"/>
                                    <input type="hidden" name="client_sender_name" id="client_sender_name"/>
                                    <button type="submit" class="btn btn-primary btn-addon btn-md"><i class="fa fa-plus"></i> Submit</button>
                                </div>
                                </form>
                              </div>

                           </div>
                       </div>
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


@section('scripts')

<script>
  //dataTABLE
  $(document).ready(function() {

    



    $('body').on('click','.senderidDelete', function(){
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
            url: `/delete-senderid-by-reseller/${senderid_users_id}/${senderid}`,
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
