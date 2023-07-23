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
                  <h1 class="m-n font-thin h3 text-black font-bold">Reseller Users List</h1>
                  <small class="text-muted"></small>
              </div>
            </div>
        </div>

        <div class="modal bd-example-modal-md fade" id="resellerassignedsenders" tabindex="-1" role="dialog" aria-labelledby="operatorapiModalLabel" aria-hidden="true">
          <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title font-bold" id="operatorapiModalLabel">Reseller Senderid List <span class="clientname pull-right" style="margin-right:20px;"></span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="margin-top: -22px;">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <form role="form" action="{{route('client-balance')}}" id="smsoperatorapi" method="post">
                @csrf
                <div class="load-senderid" style="text-align: center; padding: 24px 10px;">
                  
                </div>
              
            </form>
            </div>
          </div>
        </div>

        <div class="modal bd-example-modal-md fade" id="resellerviewbalance" tabindex="-1" role="dialog" aria-labelledby="operatorapiModalLabel" aria-hidden="true">
          <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title font-bold" id="operatorapiModalLabel">Reseller SMS Balance List <span class="clientname pull-right" style="margin-right:40px;"></span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="margin-top: -22px;">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <form role="form" action="{{route('client-balance')}}" id="smsoperatorapi" method="post">
                @csrf
                <div class="load-balance">
                  <table class="table table-stripped">
                    <thead>
                      <tr>
                        <th>Mask</th>
                        <td class="balmask"></td>
                      </tr>
                      <tr>
                        <th>Non Mask</th>
                        <td class="balnonmask"></td>
                      </tr>
                      <tr>
                        <th>Voice</th>
                        <td class="balvoice"></td>
                      </tr>

                    </thead>
                  </table>
                </div>
              
            </form>
            </div>
          </div>
        </div>

        <!-- / main header -->
        <div class="wrapper-md" ng-controller="FlotChartDemoCtrl">
          <a class="btn btn-primary btn-addon btn-md pull-right mb-5" style="margin-bottom:10px; z-index: 9999; position:relative;" href="{{route('reseller-registration')}}"><i class="fa fa-plus"></i> Create New Reseller</a>
            <div class="row">
              <div class="col-md-12">
                  <div class="panel panel-default dataTables_wrapper">
                      <div class="panel-heading">
                        Reseller  <span class="usernotfound pull-right"></span>
                      </div>
                      <div class="table-responsive">
                        <table ui-jq="dataTable" class="rootreseller display nowrap dataTable dtr-inline collapsed" style="width: 100%;" role="grid" aria-describedby="example_info">
                          <thead>
                            <tr>
                              <th>ID</th>
                              <th>Name</th>
                              <th>Email</th>
                              <th>Company</th>
                              <th>Phone</th>
                              <th>Address</th>
                              <th>Country</th>
                              <th>City</th>
                              <th>State</th>
                              <th>Created From</th>
                              <th>Created By</th>
                              <th>Created At</th>
                              <th>Status</th>
                              <th class="actions">Action</th>
                            </tr>
                          </thead>
                          <tfoot>
                            <tr>
                              <th>ID</th>
                              <th>Name</th>
                              <th>Email</th>
                              <th>Company</th>
                              <th>Phone</th>
                              <th>Address</th>
                              <th>Country</th>
                              <th>City</th>
                              <th>State</th>
                              <th>Created From</th>
                              <th>Created By</th>
                              <th>Created At</th>
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
  /** Root Reseller **/

  var rootReseller = function(){
  //$('.rootuser').DataTable().destroy();
  $('.activedeal').css({'background-color':'#3e8ef7','color':'#fff'});    
  $('.rootreseller').DataTable({
      "order": [[ 0, "desc" ]],
      "processing": true,
      "language": {
        processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span> '
      },

      "serverSide": true,
      "ajax": { "url": "root-resellers-data","type": "get" },
      columnDefs: [
          
        //{ className: "actions", targets: 9  },  
        {
            targets: [ 0 ],
            visible: false,
            searchable: false
        }, 
        {
            targets: [ 5 ],
            visible: false,
            searchable: false
        }, 
        {
            targets: [ 6 ],
            visible: false,
            searchable: false
        }, 
        {
            targets: [ 7 ],
            visible: false,
            searchable: false
        }, 
        {
            targets: [ 8 ],
            visible: false,
            searchable: false
        }, 
        {
            targets: [ 9 ],
            visible: false,
            searchable: false
        }, 
        {
            targets: [ 10 ],
            visible: false,
            searchable: false
        }, 
        {
            targets: 12,
            render: function(data, type, full, meta) {
              if (full[12] == "y") {
                return "Active";
              } else {
                return "Inactive";
              }
            }
        },
        {  targets: -1,
          render: function (data, type, full, meta) {
            let id = full[0];
            let name = full[1];
            let mobile = full[4];
            $.ajax({
              url: "{{route('reseller-senderids')}}",
              type: "post",
              data: {
                resellerid: id
              },
              success: function(res) {
                console.log(res.length);
                $('.totalsenderid'+full[0]).html(`${res.length} <i class="icon-speech" aria-hidden="true"></i>`);
                // $.each(res, function(index,sender){
                //   $('.load-senderid').append(`
                //       <span class="font-bold" style="margin-left: 10px;">${sender}</span>
                //   `)
                // })
              },
              error: function(err) {
                console.log(err);
              }
            })
                                  
                { return '<a href="reseller-profile/'+full[0]+'/index" class="btn btn-sm btn-icon btn-pure btn-default rootuseredtfrm" data-original-title="edit" data-id="'+full[0]+'"><i class="icon icon-pencil" aria-hidden="true"></i></a>\n\
                  <a href="#" class="btn btn-sm btn-icon btn-pure btn-default viewresellersenderid totalsenderid'+full[0]+'" data-toggle="modal" data-target="#resellerassignedsenders" data-original-title="edit" data-id="'+full[0]+'" data-name="'+full[1]+'" data-mobile="'+full[4]+'"></a>\n\
                  <a href="#" class="btn btn-sm btn-icon btn-pure btn-default viewresellerbalance" data-toggle="modal" data-target="#resellerviewbalance" data-original-title="edit" data-id="'+full[0]+'" data-name="'+full[1]+'" data-mobile="'+full[4]+'"><i class="fa fa-money" aria-hidden="true"></i></a>\n\
                  <a href="#" class="btn btn-sm btn-icon btn-pure btn-default resellerloginfromroot" data-original-title="edit" data-email="'+full[2]+'"><i class="fa fa-sign-in" aria-hidden="true"></i></a>\n\
          <a href="#" class="btn btn-sm btn-icon btn-pure btn-default rootuserdtl" data-toggle="tooltip" data-original-title="Remove" data-id="'+full[0]+'"><i class="icon icon-trash" aria-hidden="true"></i></a>'; }
          }   
        },
      ],
      "aoColumns": [
          null,
          null,
          null,
          null,
          null,
          null,
          null,
          null,
          null,
          null,
          null,
          null,
          null,
          { "sWidth":"60px","sClass": "actions" }
      ],
      oLanguage: {sProcessing: '<div class="loader vertical-align-middle loader-rotate-plane" style="background-color: #2e688e; "></div>'}

    });
  }
  rootReseller();


  $('body').on('click','.viewresellersenderid', function(e){
    e.preventDefault();
    let id = $(this).data('id');
    let name = $(this).data('name');
    let mobile = $(this).data('mobile');
    $.ajax({
      url: "{{route('reseller-senderids')}}",
      type: "post",
      data: {
        resellerid: id
      },
      success: function(res) {
        $('.load-senderid').html('');
        $.each(res, function(index,sender){
          $('.load-senderid').append(`
              <span class="font-bold" style="margin-left: 10px;">${sender}</span>
          `)
        })
      },
      error: function(err) {
        console.log(err);
      }
    });
  });

  $('body').on('click','.resellerloginfromroot',function(e){
    e.preventDefault();
    let email = $(this).data('email');
    $.ajax({
      url: "reseller-login-from-root/"+email,
      type: "get",
      success: function(res)
      {
        window.location.href = 'resellers';
      },
      error: function(err)
      {
        console.log(err);
        $('.usernotfound').text(err.responseJSON.errmsg);
      }
    })
  });

  $('body').on('click','.viewresellerbalance', function(e){
    e.preventDefault();
    //clear data
    $('.balmask').text(``);
    $('.balnonmask').text(``);
    $('.balvoice').text(``);
    $('.clientname').text(``);

    let id = $(this).data('id');
    let name = $(this).data('name');
    let mobile = $(this).data('mobile');
    $.ajax({
      url: "{{route('reseller.balance')}}",
      type: "post",
      data: {
        userid: id
      },
      success: function(res) {
        $('.balmask').text(`${res['balance'].maskbalance} SMS`);
        $('.balnonmask').text(`${res['balance'].nonmaskbalance} SMS`);
        $('.balvoice').text(`${res['balance'].voicebalance} SMS`);
        $('.clientname').text(`Mobile: ${mobile} Reseller: ${name}`);
      },
      error: function(err) {
        console.log(err);
      }
    })
  });

</script>

@endsection
