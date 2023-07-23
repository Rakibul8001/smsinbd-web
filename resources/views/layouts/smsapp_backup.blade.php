<!DOCTYPE html>
<html lang="en" class="">
<head>
  <meta charset="utf-8" />
  <title>
    @if(Auth::guard('root')->check())
      Welcome to SMSBD Root Admin panel
    @elseif(Auth::guard('manager')->check())
      Welcome to SMSBD Support Manager Admin panel
    @elseif(Auth::guard('reseller')->check())
      Welcome to SMSBD Reseller Admin panel
    @else
      Welcome to SMSBD Client Admin panel
    @endif  
  </title>
  <meta name="description" content="app, web app, responsive, responsive layout, admin, admin panel, admin dashboard, flat, flat ui, ui kit, AngularJS, ui route, charts, widgets, components" />
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <link rel="stylesheet" href="{{ asset('libs/assets/animate.css/animate.css') }}" type="text/css" />
  <link rel="stylesheet" href="{{ asset('libs/assets/font-awesome/css/font-awesome.min.css') }}" type="text/css" />
  <link rel="stylesheet" href="{{ asset('libs/assets/simple-line-icons/css/simple-line-icons.css') }}" type="text/css" />
  <link rel="stylesheet" href="{{ asset('libs/jquery/bootstrap/dist/css/bootstrap.css') }}" type="text/css" />

  <link rel="stylesheet" href="{{ asset('smsapp/css/font.css') }}" type="text/css" />
  <link rel="stylesheet" href="{{ asset('smsapp/css/app.css') }}" type="text/css" />
  <link rel="stylesheet" href="//cdn.datatables.net/1.10.20/css/jquery.dataTables.min.css" />
  <link rel="stylesheet" href="http://cdn.datatables.net/plug-ins/a5734b29083/integration/bootstrap/3/dataTables.bootstrap.css"/>
  <link rel="stylesheet" href="http://cdn.datatables.net/responsive/1.0.2/css/dataTables.responsive.css"/>
  <link rel="stylesheet" href="{{ asset('libs/assets/bootstrap-datepicker3.css') }}" type="text/css" />
  <link rel="stylesheet" href="{{ asset('smsapp/css/fileinput.css') }}" type="text/css" />
  <link rel="stylesheet" href="{{ asset('libs/jquery/bootstrap/dist/css/bootstrap-datetimepicker.min.css') }}" type="text/css" />
  <style type="text/css">
    .thumb-sm {
        display: inline-block;
        width: 70px;
    }

    .panel .dataTables_wrapper {
        padding-top: 10px;
        margin: 0 5px 11px 5px;
    }

    
    .border-danger {
        border: 1px solid #ef5757;
    }

    .text-danger {
      color: #ef5757;
    }

    .clientsuccess {
      font-size: 17px;
      font-weight: 600;
      text-align: center;
      text-transform: uppercase;
    }

    .clientunsuccess {
      font-size: 17px;
      font-weight: 600;
      text-align: center;
      text-transform: uppercase;
    }

    .doverify {
      cursor: pointer;
    }

    .dostatus {
      cursor: pointer;
    }

    #test-gdocsviewer {
      border: 5px red solid;
      padding: 20px;
      width: 650px;
      background: #ccc;
      text-align: center;
    }
    /* Style all gdocsviewer containers */
    .gdocsviewer {
      margin:10px;
    }

    .chosen-container {
      width: 100% !important;
    }
  
  .modal-header .close {
      margin-top: -22px;
  }

  .error {
      color: #d52626;
      font-weight: bold;
  } 

  /*.dataTables_wrapper {
      position: relative;
      clear: both;
      *zoom: 1;
      zoom: 1;
      overflow: hidden;
  }

  .dataTables_empty {
    display:none;
  }*/

  .navi ul.nav li li a {
    padding-left: 55px;
  }

  </style>

</head>
<body>
<div class="app app-header-fixed ">
  
  

  @include('siteutil.header')

  @if(Auth::guard('root')->check())

    @include('siteutil.left')

  @elseif(Auth::guard('manager')->check())

    @include('siteutil.manager-left')

  @elseif(Auth::guard('reseller')->check())

    @include('siteutil.reseller-left')

  @elseif(Auth::guard('web')->check())

    @include('siteutil.client-left')

  @endif


  @yield('appbody')
  
  @include('siteutil.footer')



</div>
<script language="javascript">
  function handleEnter (field, event) {
		var keyCode = event.keyCode ? event.keyCode : event.which ? event.which : event.charCode;
		if (keyCode == 13) {
			var i;
			for (i = 0; i < field.form.elements.length; i++)
				if (field == field.form.elements[i])
					break;
			i = (i + 1) % field.form.elements.length;
			field.form.elements[i].focus();
			return false;
		} 
		else
		return true;
	}      

  </script>
<script src="{{ asset('libs/jquery/jquery/dist/jquery.js') }}"></script>
<script src="{{ asset('libs/jquery/bootstrap/dist/js/bootstrap.js') }}"></script>
<script src="{{ asset('smsapp/js/ui-load.js') }}"></script>
<script src="{{ asset('smsapp/js/ui-jp.config.js') }}"></script>
<script src="{{ asset('smsapp/js/ui-jp.js') }}"></script>
<script src="{{ asset('smsapp/js/ui-nav.js') }}"></script>
<script src="{{ asset('smsapp/js/ui-toggle.js') }}"></script>
<script src="{{ asset('smsapp/js/ui-client.js') }}"></script>
<script src="//cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
<!-- <script src="https://cdn.datatables.net/fixedcolumns/3.3.0/js/dataTables.fixedColumns.min.js"></script> -->
<script type="text/javascript" language="javascript" src="//cdn.datatables.net/responsive/1.0.2/js/dataTables.responsive.js"></script>
<script type="text/javascript" language="javascript" src="//cdn.datatables.net/plug-ins/a5734b29083/integration/bootstrap/3/dataTables.bootstrap.js"></script>
<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/rowgroup/1.1.1/js/dataTables.rowGroup.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.min.js"></script>
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

<script src="{{ asset('libs/jquery/bootstrap-datepicker/bootstrap-datepicker.js')}}" type="text/javascript"></script>
<script src="{{ asset('smsapp/js/jquery.textareaCounter.plugin.js') }}"></script>
<script src="{{ asset('smsapp/js/fileinput.min.js') }}"></script>
<script src="{{ asset('smsapp/js/moment.min.js') }}"></script>
<script src="{{ asset('smsapp/js/bootstrap-datetimepicker.min.js') }}"></script>

<script type="text/javascript">
  if ($("#smsoperator").length > 0) {
      $("#smsoperator").validate({

          rules: {
              operator_name: {
                  required: true,
                  maxlength: 20
              },

              operator_prefix: {
                  required: true,
                  maxlength: 5,
              },
          },
          messages: {

              operator_name: {
                  required: "Operator name field is required",
              },
              operator_prefix: {
                  required: "Operator prefix field is required",
              },

          },
      })
  } 

  if ($("#smsoperatoredit").length > 0) {
      $("#smsoperatoredit").validate({

          rules: {
              operator_name: {
                  required: true,
                  maxlength: 20
              },

              operator_prefix: {
                  required: true,
                  maxlength: 5,
              },
          },
          messages: {

              operator_name: {
                  required: "Operator name field is required",
              },
              operator_prefix: {
                  required: "Operator prefix field is required",
              },

          },
      })
  }

  if ($('#smssenderadd').length > 0) {
    $('#smssenderadd').validate({
        rules: {
          sender_name: {
            required: true
          },
          
          sender_user: {
            required: true
          },

          sender_password: {
            required: true
          },
        },

        messages: {
          sender_name: {
            required: "Sender name is required"
          },

          sender_user: {
            required: "User is required"
          },

          sender_password: {
            required: "Password is required"
          },
        }
    });
  } 
</script>



<script type="text/javascript">
  $.ajaxSetup({
      headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
  });
</script>
{{-- Root User JS --}}
@if(Auth::guard('root')->check())

<script type="text/javascript">
 (function(document, window, $){

      $('body').on('hidden.bs.modal','#operatorapiModal', function(){
        $('#smsoperatorapi')[0].reset();//smsoperatorapi
        $('#gateway_id').val('');
        $('#operator_id').val('');
      });

      var rootUser = function(){
      //$('.rootuser').DataTable().destroy();
      $('.activedeal').css({'background-color':'#3e8ef7','color':'#fff'});    
      $('.rootuser').DataTable({
          "order": [[ 0, "desc" ]],
          "processing": true,
          "language": {
            processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span> '
          },
 
          "serverSide": true,
          "ajax": { "url": "root-users-data","type": "get" },
          columnDefs: [
              
            //{ className: "actions", targets: 9  },  
            {
                targets: [ 0 ],
                visible: false,
                searchable: false
            }, 
            {
                targets: [ 3 ],
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
            {  targets: 12,
              render: function (data, type, full, meta) {
                      if(full[12] == "y") {
                        return "Published";
                      } else {
                        return "Unpublished";
                      }
              }   
            },
            {  targets: -1,
              render: function (data, type, full, meta) {
                                      { return '<a href="root-user/'+full[0]+'/edit" class="btn btn-sm btn-icon btn-pure btn-default rootuseredtfrm" data-original-title="edit" data-id="'+full[0]+'"><i class="icon icon-pencil" aria-hidden="true"></i></a>\n\
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
      rootUser();

      /** Edit root user **/

   


      /** Root Manager **/

      var rootManager = function(){
      //$('.rootuser').DataTable().destroy();
      $('.activedeal').css({'background-color':'#3e8ef7','color':'#fff'});    
      $('.rootmanager').DataTable({
          "order": [[ 0, "desc" ]],
          "processing": true,
          "language": {
            processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span> '
          },
 
          "serverSide": true,
          "ajax": { "url": "root-managers-data","type": "get" },
          columnDefs: [
              
            //{ className: "actions", targets: 9  },  
            {
                targets: [ 0 ],
                visible: false,
                searchable: false
            }, 
            {  targets: -1,
              render: function (data, type, full, meta) {
                                      console.log(full);
                                      { return '<a href="root-manager/'+full[0]+'/edit" class="btn btn-sm btn-icon btn-pure btn-default rootuseredtfrm" data-original-title="edit" data-id="'+full[0]+'"><i class="icon icon-pencil" aria-hidden="true"></i></a>\n\
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
              { "sWidth":"60px","sClass": "actions" }
          ],
          oLanguage: {sProcessing: '<div class="loader vertical-align-middle loader-rotate-plane" style="background-color: #2e688e; "></div>'}

        });
      }
      rootManager();

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
                    return "Published";
                  } else {
                    return "Unpublished";
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
                                      
                                      { return '<a href="root-reseller/'+full[0]+'/edit" class="btn btn-sm btn-icon btn-pure btn-default rootuseredtfrm" data-original-title="edit" data-id="'+full[0]+'"><i class="icon icon-pencil" aria-hidden="true"></i></a>\n\
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

      /** Root Clients **/

      var rootClient = function(){
      //$('.rootuser').DataTable().destroy();
      $('.activedeal').css({'background-color':'#3e8ef7','color':'#fff'});    
      $('.clients').DataTable({
          "order": [[ 0, "desc" ]],
          "processing": true,
          "language": {
            processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span> '
          },
          "serverSide": true,
          "ajax": { "url": "root-clients-data","type": "get" },
          columnDefs: [ 
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
                targets: [11],
                render: function(data, type, full, meta) {
                  if (full[11] == 'y') {
                    return "Active";
                  } else {
                    return "Inactive";
                  }
                }
            },
            {  targets: -1,
              render: function (data, type, full, meta) {
                                      { return '<a href="root-client/'+full[0]+'/edit" class="btn btn-sm btn-icon btn-pure btn-default rootuseredtfrm" data-original-title="edit" data-id="'+full[0]+'"><i class="icon icon-pencil" aria-hidden="true"></i></a>\n\
                                        <a href="#" class="btn btn-sm btn-icon btn-pure btn-default viewbalance" data-toggle="modal" data-target="#clientviewbalance" data-original-title="edit" data-id="'+full[0]+'" data-name="'+full[1]+'" data-mobile="'+full[4]+'"><i class="fa fa-money" aria-hidden="true"></i></a>\n\
                                        <a href="#" class="btn btn-sm btn-icon btn-pure btn-default userloginfromroot" data-original-title="edit" data-email="'+full[2]+'"><i class="fa fa-sign-in" aria-hidden="true"></i></a>\n\
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
      rootClient();


      $('body').on('click','.viewbalance', function(e){
        e.preventDefault();
        let id = $(this).data('id');
        let name = $(this).data('name');
        let mobile = $(this).data('mobile');
        $.ajax({
          url: "{{route('client-balance')}}",
          type: "post",
          data: {
            userid: id
          },
          success: function(res) {
            $('.balmask').text(`${res['balance'].maskbalance} SMS`);
            $('.balnonmask').text(`${res['balance'].nonmaskbalance} SMS`);
            $('.balvoice').text(`${res['balance'].voicebalance} SMS`);
            $('.clientname').text(`Mobile: ${mobile} Client: ${name}`);
          },
          error: function(err) {
            console.log(err);
          }
        })
      });

      $('body').on('click','.viewresellerbalance', function(e){
        e.preventDefault();
        let id = $(this).data('id');
        let name = $(this).data('name');
        let mobile = $(this).data('mobile');
        $.ajax({
          url: "{{route('reseller-balance')}}",
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
        })
      });


      $('.from_date').datepicker({
        format: "yyyy-mm-dd", //"yyyy-mm-dd",
        todayBtn: true, 
        calendarWeeks: true,
        autoclose: true,
        todayHighlight: true, 
        toggleActive: true,
          
      });	

      $('.to_date').datepicker({
        format: "yyyy-mm-dd", //"yyyy-mm-dd",
        todayBtn: true, 
        calendarWeeks: true,
        autoclose: true,
        todayHighlight: true, 
        toggleActive: true,
          
      });	


      var clientSmsSendReport = function() {
          $.ajax({
            url: "{{route('root-client-total-sms-send')}}",
            type: "get",
            data: {
              fromdate: $('#from_date').val(),
              todate: $('#to_date').val(),
              userid: $('#userid').val()
            },
            success: function(res) {
              $('.totalsendsms').text(`Total SMS sent: ${res}`);
              $('.totalsendsms').addClass('font-bold');
            }
          });
          $('.clientsmsreport').DataTable().destroy();
          
          $('.clientsmsreport').DataTable( {
          "ajax": {
                "processing": true,
                "url": "{{route('root-clients-sms-send-data')}}",
                "dataType": 'json',
                "type": "post",
                "data": {
                  fromdate: $('#from_date').val(),
                  todate: $('#to_date').val(),
                  userid: $('#userid').val()
                },
                "beforeSend": function (xhr) {
                  
                },
              },
              "columnDefs":
              [
                {
                  "visible": false,
                  "searchable": false
                },
              ],
              "columns": [
                  { "data": "rowid" },
                  { "data": "name" },
                  { "data": "email" },
                  { "data": "senderid" },
                  { 
                      "render": function(data, type, full, meta)
                      {
                        console.log(full);
                        return '<a href="#" class="viewsmsdetails" style="color:blue;" data-toggle="modal" data-target="#viewsmsdetails" data-submittedat="'+full.submittedat+'" data-contact="'+full.contact+'" data-remarks="'+full.remarks+'" data-content="'+full.sms_content+'">'+full.contact+'</a>';
                      }
                  },
                  { "data": "smstype" },
                  { "data": "smscategory" },
                  { "data": "noofsms" },
                  { "data": "sendfrom" },
                  {
                      "render": function (data, type, full, meta)
                      { 
                        if (full.status == "Delivered") {
                          return '<a href="#" class="btn btn-success btn-sm">'+full.status+'</a>'; 
                        } else {
                          return '<a href="#" class="btn btn-danger btn-sm">'+full.status+'</a>'; 
                        }
                      }
                  },
              ],
          } );
      }

      clientSmsSendReport();


      $('body').on('click','.viewsmsdetails', function(e){
        e.preventDefault();
        let contact = $(this).data('contact');
        let remarks = $(this).data('remarks');
        let content = $(this).data('content');
        let submittedat = $(this).data('submittedat');

        $('.deliverycontact').text(contact);
        $('.deliverycontent').text(content);
        $('.submittedat').text(submittedat);
      });

      if ($('.dataTables_empty').text() == "Loading...") {
        $('.dataTables_empty').text('No Record Found');
      }

      $('body').on('click','.getclietsmsreport', function(){
        
        clientSmsSendReport();
        if (document.querySelector('table tbody > tr > td').className == "dataTables_empty") {
          
          setTimeout(() => $('.dataTables_empty').text('No Record Found'), 5000);
        }
      });



      var clientSmsSendConsulateReport = function() {
          $.ajax({
            url: "{{route('root-client-sms-sent-total-consulate-report')}}",
            type: "get",
            data: {
              fromdate: $('#from_date').val(),
              todate: $('#to_date').val(),
              userid: $('#userid').val()
            },
            success: function(res) {
              console.log(res);
              $('.totalconsulatesendsms').text(`Total SMS sent: ${res[0].totalsms}`);
              $('.totalconsulatesendsms').addClass('font-bold');
            }
          });
          $('.clientsmscountreport').DataTable().destroy();
          
          $('.clientsmscountreport').DataTable( {
          "ajax": {
                "processing": true,
                "url": "{{route('root-clients-send-sms-consulate-rpt')}}",
                "dataType": 'json',
                "type": "post",
                "data": {
                  fromdate: $('#from_date').val(),
                  todate: $('#to_date').val(),
                  userid: $('#userid').val()
                },
                "beforeSend": function (xhr) {
                  
                },
              },
              "columnDefs":
              [
                {
                  "visible": false,
                  "searchable": false
                },
              ],
              "columns": [
                  { "data": "rowid" },
                  { "data": "name" },
                  { "data": "email" },
                  { "data": "campaingname" },
                  { "data": "smscount" },
                  { "data": "smscategory" },
                  { "data": "ownertype" },
                  { "data": "owner" },
                  { "data": "submittedat" },
                  
              ],
          } );
      }

      clientSmsSendConsulateReport();

      $('body').on('click','.getrootclietcountsmsreport', function(){
        
        clientSmsSendConsulateReport();
        if (document.querySelector('table tbody > tr > td').className == "dataTables_empty") {
          
          setTimeout(() => $('.dataTables_empty').text('No Record Found'), 5000);
        }
      });



      $('body').on('click','.userloginfromroot',function(e){
        e.preventDefault();
        let email = $(this).data('email');
        $.ajax({
          url: "client-login-from-root/"+email,
          type: "get",
          success: function(res)
          {
            window.location.href = 'client';
          },
          error: function(err)
          {
            $('.usernotfound').text(err.responseText.errmsg);
          }
        })
      });

      $('body').on('click','.resellerloginfromroot',function(e){
        e.preventDefault();
        let email = $(this).data('email');
        $.ajax({
          url: "reseller-login-from-root/"+email,
          type: "get",
          success: function(res)
          {
            window.location.href = 'reseller';
          },
          error: function(err)
          {
            console.log(err);
            $('.usernotfound').text(err.responseJSON.errmsg);
          }
        })
      });


      var smsOperator = function(){
      $('.smsoperators').DataTable().destroy();
      $('.activedeal').css({'background-color':'#3e8ef7','color':'#fff'});    
      $('.smsoperators').DataTable({
          "order": [[ 0, "desc" ]],
          "processing": true,
          "language": {
            processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span> '
          },
 
          "serverSide": true,
          "ajax": { "url": "render-operators","type": "get" },
          columnDefs: [
              
            //{ className: "actions", targets: 9  },  
            {
                targets: [ 0 ],
                visible: false,
                searchable: false
            }, 
            {
              targets: [3],
              render: function (data, type, full, meta) {
                if (full[3] == 'y') {
                  {return 'Published'};
                } else {
                  {return 'Unpublished'};
                }
              }
            },
            {  targets: -1,
              render: function (data, type, full, meta) {
                    { 
                      //return '<a href="#" class="btn btn-sm btn-icon btn-pure btn-default operatoredtfrm"  data-toggle="modal" data-target="#operatorlistModal" data-original-title="edit" data-id="'+full[0]+'" data-operatorname="'+full[1]+'" data-operatorprefix="'+full[2]+'" data-status="'+full[3]+'" data-createdby="'+full[5]+'"><i class="icon icon-pencil" aria-hidden="true"></i></a>\n\
              //<a href="#" class="btn btn-sm btn-icon btn-pure btn-default operatorDtl" data-toggle="tooltip" data-original-title="Remove" data-id="'+full[0]+'"> <i class="icon icon-trash" aria-hidden="true"></i></a>'; 
                      return '<a href="#" class="btn btn-sm btn-icon btn-pure btn-default operatoredtfrm"  data-toggle="modal" data-target="#operatorlistModal" data-original-title="edit" data-id="'+full[0]+'" data-operatorname="'+full[1]+'" data-operatorprefix="'+full[2]+'" data-status="'+full[3]+'" data-createdby="'+full[5]+'"><i class="icon icon-pencil" aria-hidden="true"></i></a>'; 
                    }
              }   
            },
          ],
          /*"columns": [
              { "data": "operator_id" },
              { "data": "operator_name" },
              { "data": "operator_prefix" },
              { "data": "status" },
              { "data": "name" },
              { "data": "root_user_id" },
              { "data": "gateway_user" },
              { "data": "gateway_password" },
              { "data": "api_url" },
              { "data": "gateway_status" },
              { "data": "gateway_created_by" },
              { "data": "gateway_updated_by" },
              { "data": "gateway_created_name" },
              { "data": "gateway_updated_name" },
          ],*/
          "aoColumns": [
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
      smsOperator();

      $('body').on('click','.operatorDtl', function(){
        let id = $(this).data('id');

        swal({
          title: "Are you sure?",
          text: "Once deleted, you will not be able to recover this imaginary file!",
          icon: "warning",
          buttons: true,
        })
        .then((willDelete) => {
          if (willDelete) {
            $.ajax({
              url: "{{route('delete-operator')}}",
              type: 'post',
              data: {
                id: id
              },
              success: function(res){
                console.log(res);
                swal(res.msg, {
                  icon: "success",
                });
                smsOperator();
              },
              error: function(err){
                console.log(err.responseJSON.errmsg);
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

      

      $('body').on('click','.operatoredtfrm', function(){
        let id = $(this).data('id');
        let operator_name = $(this).data('operatorname');
        let operator_prefix = $(this).data('operatorprefix');
        let status = $(this).data('status');
        let createdby = $(this).data('createdby');

        $('#smsoperatoredit #id').val(id);
        $('#smsoperatoredit #operator_name').val(operator_name);
        $('#smsoperatoredit #operator_prefix').val(operator_prefix);
        $('#smsoperatoredit #created_by').val(createdby);
        
        if (status == 'y'){
          $('#smsoperatoredit #status').val(status);
          $('#smsoperatoredit #status').prop('checked',true);
        }

        
        
      });

      /* ----- Operator end ------*/

      /**-------Gateway Start------ */

      var smsGateways = function(){
      $('.smsgateways').DataTable().destroy();
      $('.activedeal').css({'background-color':'#3e8ef7','color':'#fff'});    
      $('.smsgateways').DataTable({
          "order": [[ 0, "desc" ]],
          "processing": true,
          "language": {
            processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span> '
          },
 
          "serverSide": true,
          "ajax": { "url": "render-gateways","type": "get" },
          columnDefs: [
              
            //{ className: "actions", targets: 9  },  
            {
                targets: [ 0 ],
                visible: false,
                searchable: false
            }, 
            {
                targets: [ 4 ],
                visible: false,
                searchable: false
            }, 
            {
              targets: [5],
              render: function (data, type, full, meta) {
                if (full[5] == 'y') {
                  {return 'Published'};
                } else {
                  {return 'Unpublished'};
                }
              }
            },
            {  targets: -1,
              render: function (data, type, full, meta) {
                    { 
                      return '<a href="#" class="btn btn-sm btn-icon btn-pure btn-default operatorapifrm"  data-toggle="modal" data-target="#operatorapiModal" data-original-title="edit" data-id="'+full[0]+'" data-gatewayname="'+full[1]+'" data-gatewayuser="'+full[2]+'" data-gatewaypassword="'+full[3]+'" data-apiurl="'+full[4]+'" data-gatewaystatus="'+full[5]+'"><i class="icon icon-pencil" aria-hidden="true"></i></a>';

                      //return '<a href="#" class="btn btn-sm btn-icon btn-pure btn-default operatorapifrm"  data-toggle="modal" data-target="#operatorapiModal" data-original-title="edit" data-id="'+full[0]+'" data-gatewayname="'+full[1]+'" data-gatewayuser="'+full[2]+'" data-gatewaypassword="'+full[3]+'" data-apiurl="'+full[4]+'" data-gatewaystatus="'+full[5]+'"><i class="icon icon-pencil" aria-hidden="true"></i></a>\n\
                      //<a href="#" class="btn btn-sm btn-icon btn-pure btn-default operatorDtl" data-toggle="tooltip" data-original-title="Remove" data-id="'+full[0]+'"> <i class="icon icon-trash" aria-hidden="true"></i></a>'; 
                    }
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
              { "sWidth":"60px","sClass": "actions" }
          ],
          oLanguage: {sProcessing: '<div class="loader vertical-align-middle loader-rotate-plane" style="background-color: #2e688e; "></div>'}

        });
      }
      smsGateways();


      $('body').on('click','.operatorapifrm', function(){
        let gatewayid = $(this).data('id');
        let gatewayname = $(this).data('gatewayname');
        let gatewayuser = $(this).data('gatewayuser');
        let gatewaypassword = $(this).data('gatewaypassword');
        let apiurl = $(this).data('apiurl');
        let gatewaystatus = $(this).data('gatewaystatus');
        let gatewaycreatedby = $(this).data('gatewaycreatedby');
        let gatewayupdatedby = $(this).data('gatewayupdatedby');

        $('#smsoperatorapi #gateway_id').val(gatewayid);
        $('#smsoperatorapi #operator_name_api').val(gatewayname);
        $('#smsoperatorapi #user').val(gatewayuser);
        $('#smsoperatorapi #password').val(gatewaypassword);
        $('#smsoperatorapi #api_url').val(apiurl);
        if (gatewaystatus == 'y'){
          $('#smsoperatorapi #api_status').val(gatewaystatus);
          $('#smsoperatorapi #api_status').prop('checked', true);
          
        } else {
          $('#smsoperatorapi #api_status').prop('checked', false);
        }

        
        
        
      });

      /**-------Gateway End------- */

      let smssenderidtype = "{{@$senderidtype}}";
      var renderSenderId = function() {
          $('#smssender').DataTable().destroy();
          $('#smssender').DataTable( {
          "order": [[ 0, "desc" ]],  
          "ajax": {
              "processing": true,
              "url": "{{route('rander-sms-senderid',[@$senderidtype])}}",
              "dataType": 'json',
              "type": "get",
              "beforeSend": function (xhr) {
                  }
              },
              "columnDefs":
              [
                {
                  "visible": false,
                  "searchable": false
                },
                {
                  targets: [0],
                  visible: false,
                  searchable: false
                },
                {
                  targets: [2],
                  render: function (data, type, full, meta) {
                    if (full.operator_name != null) {
                      {return full.operator_name.operator_name};
                    } else {
                      {return 'General'};
                    }
                  }
                },
                {
                  targets: [3],
                  render: function (data, type, full, meta) {
                    if (full.status == '1') {
                      {return 'Active'};
                    } else {
                      {return 'Inactive'};
                    }
                  }
                },
                {
                  targets: [4],
                  render: function (data, type, full, meta) {
                    if (full.default == '1') {
                      {return 'Yes'};
                    } else {
                      {return 'No'};
                    }
                  }
                },
                {
                  targets: [5],
                  visible: smssenderidtype == "teletalk" ? true : false
                },
                {
                  targets: [6],
                  visible: smssenderidtype == "teletalk" ? true : false
                },
              ],
              "columns": [
                  { "data": "id" },
                  { "data": "sender_name" },
                  { "data": "operator_name" },
                  { "data": "status" },
                  { "data": "default" },
                  { "data": "user" },
                  { "data": "password" },
                  { "data": "created_by" },
                  { "data": "updated_by" },
                  {
                    "render": function (data, type, full, meta)
                      { 
                        let senderidtype = "{{!empty(@$senderidtype) ? @$senderidtype : 'general'}}";
                        
                        if (senderidtype == 'teletalk')
                        {
                          if (full.status == 1 && full.operator_name == null) {
                            return '<a href="#" class="btn btn-sm btn-icon btn-pure btn-default senderidedtfrm"  data-toggle="modal" data-target="#smsSenderIdTeletalkAddForm" data-original-title="edit" data-id="'+full.id+'" data-sendername="'+full.sender_name+'" data-gatewayinfo='+full.gateway_info+' data-operatorid="'+full.operator_id+'" data-status="'+full.status+'" data-createdby="'+full.created_by+'" data-updatedby="'+full.updated_by+'" data-default="'+full.default+'" data-user="'+full.user+'" data-password="'+full.password+'"><i class="icon icon-pencil" aria-hidden="true"></i></a>\n\
                            <a href="#" class="btn btn-sm btn-icon btn-pure btn-default assignsenderid"  data-toggle="modal" data-target="#assignsenderid" data-original-title="edit" data-id="'+full.id+'" data-sendername="'+full.sender_name+'"><i class="fa fa-users" aria-hidden="true"></i></a>\n\
                            <a href="#" class="btn btn-sm btn-icon btn-pure btn-default senderidDtl" data-toggle="tooltip" data-original-title="Remove" data-id="'+full.id+'"> <i class="icon icon-trash" aria-hidden="true"></i></a>'; 
                          } else if (full.status == 1 && full.operator_name != null) {
                            return '<a href="#" class="btn btn-sm btn-icon btn-pure btn-default senderidedtfrm"  data-toggle="modal" data-target="#smsSenderIdTeletalkAddForm" data-original-title="edit" data-id="'+full.id+'" data-sendername="'+full.sender_name+'" data-gatewayinfo='+full.gateway_info+' data-operatorid="'+full.operator_id+'" data-status="'+full.status+'" data-createdby="'+full.created_by+'" data-updatedby="'+full.updated_by+'" data-default="'+full.default+'" data-user="'+full.user+'" data-password="'+full.password+'"><i class="icon icon-pencil" aria-hidden="true"></i></a>\n\
                            <a href="#" class="btn btn-sm btn-icon btn-pure btn-default senderidDtl" data-toggle="tooltip" data-original-title="Remove" data-id="'+full.id+'"> <i class="icon icon-trash" aria-hidden="true"></i></a>'; 
                          } else {
                            return '<a href="#" class="btn btn-sm btn-icon btn-pure btn-default senderidedtfrm"  data-toggle="modal" data-target="#smsSenderIdTeletalkAddForm" data-original-title="edit" data-id="'+full.id+'" data-sendername="'+full.sender_name+'" data-gatewayinfo='+full.gateway_info+' data-operatorid="'+full.operator_id+'" data-status="'+full.status+'" data-createdby="'+full.created_by+'" data-updatedby="'+full.updated_by+'" data-default="'+full.default+'" data-user="'+full.user+'" data-password="'+full.password+'"><i class="icon icon-pencil" aria-hidden="true"></i></a>\n\
                            <a href="#" class="btn btn-sm btn-icon btn-pure btn-default operatorDtl" data-toggle="tooltip" data-original-title="Remove" data-id="'+full.id+'"> <i class="icon icon-trash" aria-hidden="true"></i></a>'; 
                          }
                        } else {
                          if (full.status == 1 && full.operator_name == null) {
                            return '<a href="#" class="btn btn-sm btn-icon btn-pure btn-default senderidedtfrm"  data-toggle="modal" data-target="#smsSenderIdAddForm" data-original-title="edit" data-id="'+full.id+'" data-sendername="'+full.sender_name+'" data-gatewayinfo='+full.gateway_info+' data-operatorid="'+full.operator_id+'" data-status="'+full.status+'" data-createdby="'+full.created_by+'" data-updatedby="'+full.updated_by+'" data-default="'+full.default+'" data-user="'+full.user+'" data-password="'+full.password+'"><i class="icon icon-pencil" aria-hidden="true"></i></a>\n\
                            <a href="#" class="btn btn-sm btn-icon btn-pure btn-default assignsenderid"  data-toggle="modal" data-target="#assignsenderid" data-original-title="edit" data-id="'+full.id+'" data-sendername="'+full.sender_name+'"><i class="fa fa-users" aria-hidden="true"></i></a>\n\
                            <a href="#" class="btn btn-sm btn-icon btn-pure btn-default senderidDtl" data-toggle="tooltip" data-original-title="Remove" data-id="'+full.id+'"> <i class="icon icon-trash" aria-hidden="true"></i></a>'; 
                          } else if (full.status == 1 && full.operator_name != null) {
                            return '<a href="#" class="btn btn-sm btn-icon btn-pure btn-default senderidedtfrm"  data-toggle="modal" data-target="#smsSenderIdAddForm" data-original-title="edit" data-id="'+full.id+'" data-sendername="'+full.sender_name+'" data-gatewayinfo='+full.gateway_info+' data-operatorid="'+full.operator_id+'" data-status="'+full.status+'" data-createdby="'+full.created_by+'" data-updatedby="'+full.updated_by+'" data-default="'+full.default+'" data-user="'+full.user+'" data-password="'+full.password+'"><i class="icon icon-pencil" aria-hidden="true"></i></a>\n\
                            <a href="#" class="btn btn-sm btn-icon btn-pure btn-default senderidDtl" data-toggle="tooltip" data-original-title="Remove" data-id="'+full.id+'"> <i class="icon icon-trash" aria-hidden="true"></i></a>'; 
                          } else {
                            return '<a href="#" class="btn btn-sm btn-icon btn-pure btn-default senderidedtfrm"  data-toggle="modal" data-target="#smsSenderIdAddForm" data-original-title="edit" data-id="'+full.id+'" data-sendername="'+full.sender_name+'" data-gatewayinfo='+full.gateway_info+' data-operatorid="'+full.operator_id+'" data-status="'+full.status+'" data-createdby="'+full.created_by+'" data-updatedby="'+full.updated_by+'" data-default="'+full.default+'" data-user="'+full.user+'" data-password="'+full.password+'"><i class="icon icon-pencil" aria-hidden="true"></i></a>\n\
                            <a href="#" class="btn btn-sm btn-icon btn-pure btn-default operatorDtl" data-toggle="tooltip" data-original-title="Remove" data-id="'+full.id+'"> <i class="icon icon-trash" aria-hidden="true"></i></a>'; 
                          }
                        }
                      }
                      /*"render": function (data, type, full, meta)
                      { 
                        if (full.status == 1) {
                          return '<a href="#" class="btn btn-sm btn-icon btn-pure btn-default senderidedtfrm"  data-toggle="modal" data-target="#smsSenderIdAddForm" data-original-title="edit" data-id="'+full.id+'" data-sendername="'+full.sender_name+'" data-gatewayinfo='+full.gateway_info+' data-operatorid="'+full.operator_id+'" data-status="'+full.status+'" data-createdby="'+full.created_by+'" data-updatedby="'+full.updated_by+'" data-default="'+full.default+'" data-user="'+full.user+'" data-password="'+full.password+'"><i class="icon icon-pencil" aria-hidden="true"></i></a>\n\
                          <a href="#" class="btn btn-sm btn-icon btn-pure btn-default assignsenderid"  data-toggle="modal" data-target="#assignsenderid" data-original-title="edit" data-id="'+full.id+'" data-sendername="'+full.sender_name+'"><i class="fa fa-users" aria-hidden="true"></i></a>\n\
                          <a href="#" class="btn btn-sm btn-icon btn-pure btn-default operatorDtl" data-toggle="tooltip" data-original-title="Remove" data-id="'+full.id+'"> <i class="icon icon-trash" aria-hidden="true"></i></a>'; 
                        } else {
                          return '<a href="#" class="btn btn-sm btn-icon btn-pure btn-default senderidedtfrm"  data-toggle="modal" data-target="#smsSenderIdAddForm" data-original-title="edit" data-id="'+full.id+'" data-sendername="'+full.sender_name+'" data-gatewayinfo='+full.gateway_info+' data-operatorid="'+full.operator_id+'" data-status="'+full.status+'" data-createdby="'+full.created_by+'" data-updatedby="'+full.updated_by+'" data-default="'+full.default+'" data-user="'+full.user+'" data-password="'+full.password+'"><i class="icon icon-pencil" aria-hidden="true"></i></a>\n\
                          <a href="#" class="btn btn-sm btn-icon btn-pure btn-default operatorDtl" data-toggle="tooltip" data-original-title="Remove" data-id="'+full.id+'"> <i class="icon icon-trash" aria-hidden="true"></i></a>'; 
                        }
                      }
                      */
                  },
              ]
          } );
      }

      renderSenderId();

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
         $('.load-senderid').load("/load-assigned-sender-id/"+id);
         $('#activeclients').trigger("chosen:updated");
         
      });

      $('body').on('click','.senderclientdtl', function(){
        let assign_user_senderid = $(this).data('assign_user_senderid');
        let sms_sender_id = $(this).data('sms_sender_id');
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
              url: `delete-client-senderid/${assign_user_senderid}/${sms_sender_id}`,
              type: 'get',
              success: function(res) {
                swal(res.msg, {
                  icon: "success",
                });
                tr.fadeOut(400,function(){
                    tr.remove();
                });
                renderSenderId();
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


      /**reseller senderid assigned by root */
      var renderSenderIdForReseller = function() {
          $('#smssenderidforreseller').DataTable().destroy();
          $('#smssenderidforreseller').DataTable( {
          "order": [[ 0, "desc" ]],  
          "ajax": {
              "processing": true,
              "url": "{{route('rander-sms-senderid',[@$senderidtype])}}",
              "dataType": 'json',
              "type": "get",
              "beforeSend": function (xhr) {
                  }
              },
              "columnDefs":
              [
                {
                  "visible": false,
                  "searchable": false
                },
                {
                  targets: [0],
                  visible: false,
                  searchable: false
                },
                {
                  targets: [2],
                  render: function (data, type, full, meta) {
                    if (full.operator_name != null) {
                      {return full.operator_name.operator_name};
                    } else {
                      {return 'General'};
                    }
                  }
                },
                {
                  targets: [3],
                  render: function (data, type, full, meta) {
                    if (full.status == '1') {
                      {return 'Published'};
                    } else {
                      {return 'Unpublished'};
                    }
                  }
                },
                {
                  targets: [4],
                  render: function (data, type, full, meta) {
                    if (full.default == '1') {
                      {return 'Yes'};
                    } else {
                      {return 'No'};
                    }
                  }
                },
                {
                  targets: [5],
                  visible: smssenderidtype == "teletalk" ? true : false
                },
                {
                  targets: [6],
                  visible: smssenderidtype == "teletalk" ? true : false
                },
              ],
              "columns": [
                  { "data": "id" },
                  { "data": "sender_name" },
                  { "data": "operator_name" },
                  { "data": "status" },
                  { "data": "default" },
                  { "data": "user" },
                  { "data": "password" },
                  { "data": "created_by" },
                  { "data": "updated_by" },
                  {
                      "render": function (data, type, full, meta)
                      { 
                        let senderidtype = "{{!empty(@$senderidtype) ? @$senderidtype : 'general'}}";
                        
                        if (senderidtype == 'teletalk')
                        {
                          if (full.status == 1 && full.operator_name == null) {
                            return '<a href="#" class="btn btn-sm btn-icon btn-pure btn-default senderidedtfrm"  data-toggle="modal" data-target="#smsSenderIdTeletalkAddForm" data-original-title="edit" data-id="'+full.id+'" data-sendername="'+full.sender_name+'" data-gatewayinfo='+full.gateway_info+' data-operatorid="'+full.operator_id+'" data-status="'+full.status+'" data-createdby="'+full.created_by+'" data-updatedby="'+full.updated_by+'" data-default="'+full.default+'" data-user="'+full.user+'" data-password="'+full.password+'"><i class="icon icon-pencil" aria-hidden="true"></i></a>\n\
                            <a href="#" class="btn btn-sm btn-icon btn-pure btn-default assignsenderidtoreseller"  data-toggle="modal" data-target="#assignsenderidtoreseller" data-original-title="edit" data-id="'+full.id+'" data-sendername="'+full.sender_name+'"><i class="fa fa-users" aria-hidden="true"></i></a>\n\
                            <a href="#" class="btn btn-sm btn-icon btn-pure btn-default senderidDtl" data-toggle="tooltip" data-original-title="Remove" data-id="'+full.id+'"> <i class="icon icon-trash" aria-hidden="true"></i></a>'; 
                          } else if (full.status == 1 && full.operator_name != null) {
                            return '<a href="#" class="btn btn-sm btn-icon btn-pure btn-default senderidedtfrm"  data-toggle="modal" data-target="#smsSenderIdTeletalkAddForm" data-original-title="edit" data-id="'+full.id+'" data-sendername="'+full.sender_name+'" data-gatewayinfo='+full.gateway_info+' data-operatorid="'+full.operator_id+'" data-status="'+full.status+'" data-createdby="'+full.created_by+'" data-updatedby="'+full.updated_by+'" data-default="'+full.default+'" data-user="'+full.user+'" data-password="'+full.password+'"><i class="icon icon-pencil" aria-hidden="true"></i></a>\n\
                            <a href="#" class="btn btn-sm btn-icon btn-pure btn-default senderidDtl" data-toggle="tooltip" data-original-title="Remove" data-id="'+full.id+'"> <i class="icon icon-trash" aria-hidden="true"></i></a>'; 
                          } else {
                            return '<a href="#" class="btn btn-sm btn-icon btn-pure btn-default senderidedtfrm"  data-toggle="modal" data-target="#smsSenderIdTeletalkAddForm" data-original-title="edit" data-id="'+full.id+'" data-sendername="'+full.sender_name+'" data-gatewayinfo='+full.gateway_info+' data-operatorid="'+full.operator_id+'" data-status="'+full.status+'" data-createdby="'+full.created_by+'" data-updatedby="'+full.updated_by+'" data-default="'+full.default+'" data-user="'+full.user+'" data-password="'+full.password+'"><i class="icon icon-pencil" aria-hidden="true"></i></a>\n\
                            <a href="#" class="btn btn-sm btn-icon btn-pure btn-default operatorDtl" data-toggle="tooltip" data-original-title="Remove" data-id="'+full.id+'"> <i class="icon icon-trash" aria-hidden="true"></i></a>'; 
                          }
                        } else {
                          if (full.status == 1 && full.operator_name == null) {
                            return '<a href="#" class="btn btn-sm btn-icon btn-pure btn-default senderidedtfrm"  data-toggle="modal" data-target="#smsSenderIdAddForm" data-original-title="edit" data-id="'+full.id+'" data-sendername="'+full.sender_name+'" data-gatewayinfo='+full.gateway_info+' data-operatorid="'+full.operator_id+'" data-status="'+full.status+'" data-createdby="'+full.created_by+'" data-updatedby="'+full.updated_by+'" data-default="'+full.default+'" data-user="'+full.user+'" data-password="'+full.password+'"><i class="icon icon-pencil" aria-hidden="true"></i></a>\n\
                            <a href="#" class="btn btn-sm btn-icon btn-pure btn-default assignsenderidtoreseller"  data-toggle="modal" data-target="#assignsenderidtoreseller" data-original-title="edit" data-id="'+full.id+'" data-sendername="'+full.sender_name+'"><i class="fa fa-users" aria-hidden="true"></i></a>\n\
                            <a href="#" class="btn btn-sm btn-icon btn-pure btn-default senderidDtl" data-toggle="tooltip" data-original-title="Remove" data-id="'+full.id+'"> <i class="icon icon-trash" aria-hidden="true"></i></a>'; 
                          } else if (full.status == 1 && full.operator_name != null) {
                            return '<a href="#" class="btn btn-sm btn-icon btn-pure btn-default senderidedtfrm"  data-toggle="modal" data-target="#smsSenderIdAddForm" data-original-title="edit" data-id="'+full.id+'" data-sendername="'+full.sender_name+'" data-gatewayinfo='+full.gateway_info+' data-operatorid="'+full.operator_id+'" data-status="'+full.status+'" data-createdby="'+full.created_by+'" data-updatedby="'+full.updated_by+'" data-default="'+full.default+'" data-user="'+full.user+'" data-password="'+full.password+'"><i class="icon icon-pencil" aria-hidden="true"></i></a>\n\
                            <a href="#" class="btn btn-sm btn-icon btn-pure btn-default senderidDtl" data-toggle="tooltip" data-original-title="Remove" data-id="'+full.id+'"> <i class="icon icon-trash" aria-hidden="true"></i></a>'; 
                          } else {
                            return '<a href="#" class="btn btn-sm btn-icon btn-pure btn-default senderidedtfrm"  data-toggle="modal" data-target="#smsSenderIdAddForm" data-original-title="edit" data-id="'+full.id+'" data-sendername="'+full.sender_name+'" data-gatewayinfo='+full.gateway_info+' data-operatorid="'+full.operator_id+'" data-status="'+full.status+'" data-createdby="'+full.created_by+'" data-updatedby="'+full.updated_by+'" data-default="'+full.default+'" data-user="'+full.user+'" data-password="'+full.password+'"><i class="icon icon-pencil" aria-hidden="true"></i></a>\n\
                            <a href="#" class="btn btn-sm btn-icon btn-pure btn-default operatorDtl" data-toggle="tooltip" data-original-title="Remove" data-id="'+full.id+'"> <i class="icon icon-trash" aria-hidden="true"></i></a>'; 
                          }
                        }
                      }
                  },
              ]
          } );
      }

      renderSenderIdForReseller();

      $('body').on('click','.assignsenderidtoreseller', function(){
          $('#activeclients').empty();
          let id = $(this).data('id');
          let sendername = $(this).data('sendername');
          
          $('#client_sender_id').val(id);
          $('#client_sender_name').val(sendername);
          $('#activeclients').trigger("chosen:updated");
          
      });
      $('#activeclients').trigger("chosen:updated");
      

      $('#assignsenderidtoreseller').on('shown.bs.modal', function(){
         let id = $('#client_sender_id').val();
         let sendername = $('#client_sender_name').val();
         
         $('#activeclients').empty();
         $('.load-senderid').load("/load-assigned-reseller-sender-id/"+id);
         $('#activeclients').trigger("chosen:updated");
         
      });

      $('body').on('click','.senderidresellerdtl', function(){
        let assign_user_senderid = $(this).data('assign_user_senderid');
        let sms_sender_id = $(this).data('sms_sender_id');
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
              url: `delete-reseller-senderid/${assign_user_senderid}/${sms_sender_id}`,
              type: 'get',
              success: function(res) {
                swal(res.msg, {
                  icon: "success",
                });
                tr.fadeOut(400,function(){
                    tr.remove();
                });
                renderSenderId();
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

      /**reseller senderid assigned by root end */
      

      $('body').on('click','.addsenderid', function(){
        $('#smssenderadd')[0].reset();
        $('input[name="frmmode"]').val('ins');
        $('#smssender_rec_id').val('');
        //$('.general-senderid').show();
        //$('.talitalk-senderid').hide();
      });

      $('body').on('click','.addsenderidteletalk', function(){
        $('#smssenderaddteletalk')[0].reset();
        $('input[name="frmmode"]').val('ins');
        $('#smssender_rec_id').val('');
        //$('.general-senderid').show();
        //$('.talitalk-senderid').hide();
      });

      $('#smsSenderIdAddForm').on('hidden.bs.modal', function () {
          $(this).find('form').trigger('reset');
          $('.senderidhead').text('Add SMS Sender ID');
          $('.senderidbtn').html('<i class="fa fa-save"></i> Submit');
          $('input[name="frmmode"]').val('ins');
          // $('#sendertype_general').parent().show();
          // $('#sendertype_general').prop("checked",true);
          // $('#sendertype_taletalk').prop("checked",false);
          // $('#sendertype_taletalk').parent().show();
          //$('.general-senderid').show();
          //$('.talitalk-senderid').show();
      });

      $('#smsSenderIdTeletalkAddForm').on('hidden.bs.modal', function () {
          $(this).find('form').trigger('reset');
          $('.senderidhead').text('Add SMS Sender ID');
          $('.senderidbtn').html('<i class="fa fa-save"></i> Submit');
          $('input[name="frmmode"]').val('ins');
          // $('#sendertype_general').parent().show();
          // $('#sendertype_general').prop("checked",true);
          // $('#sendertype_taletalk').prop("checked",false);
          // $('#sendertype_taletalk').parent().show();
          //$('.general-senderid').show();
          //$('.talitalk-senderid').show();
      });


      $('body').on('click','.senderidedtfrm', function(){
        let id = $(this).data('id');
        let gatewayinfo = $(this).data('gatewayinfo');
        let sendername = $(this).data('sendername');
        let status = $(this).data('status');
        let recdefault = $(this).data('default');
        let operatorid = $(this).data('operatorid');
        let talitalkuser = $(this).data('user');
        let talitalkpassword = $(this).data('password');
        
        let hasnumber = parseInt(sendername);

        if (hasnumber) {
            $('#sender_name').val(sendername);
            $('#sender_name_teletalk').val(sendername);
        } else {
          if (sendername.indexOf(' ') >= 0) {
            $('#sender_name').val(sendername.replace(/-+/g, ' '));
            $('#sender_name_teletalk').val(sendername.replace(/-+/g, ' '));
          } else if (sendername.indexOf('-') >= 0) {
            $('#sender_name').val(sendername.replace(/\s+/g, ' '));
            $('#sender_name_teletalk').val(sendername.replace(/\s+/g, ' '));
          } else {
            $('#sender_name').val(sendername);
            $('#sender_name_teletalk').val(sendername);
          }
        }
        
        $('input[name="frmmode"]').val('edt');
        $('input[name="smssender_rec_id"]').val(id);
        

        $('.senderidhead').text('Edit SMS Sender ID')
        $('.senderidbtn').html('<i class="fa fa-pencil"></i> Edit');

        if (operatorid == null) {
          // $('#sendertype_general').parent().show();
          // $('#sendertype_general').prop("checked",true);
          // $('#sendertype_taletalk').prop("checked",false);
          // $('#sendertype_taletalk').parent().hide();
          // $('.general-senderid').show();
          // $('.talitalk-senderid').hide();
          if (gatewayinfo !== null) {
              gatewayinfo.forEach(function(sender){
                hasnumber = parseInt(sender.associate_sender_id);

                if (sender.associate_sender_id != null)
                {
                    if (sender.associate_sender_id.indexOf('\"') >= 0) {
                      $('#associate_sender_id'+sender.edit_associate_id).val(sender.associate_sender_id.replace(/\\\"+/g, ' '));
                      $('#gateway'+sender.edit_associate_id).val(sender.associate_gateway);
                    } else {
                      $('#associate_sender_id'+sender.edit_associate_id).val(sender.associate_sender_id);
                      $('#gateway'+sender.edit_associate_id).val(sender.associate_gateway);
                    }
                }
              });
          }

          
          if (status == 1) {
            $('#senderid_status_yes').prop("checked", true);
            
          } else {
            $('#senderid_status_no').prop("checked", true);
          }
          
          if (recdefault == 1) {
            $('#senderid_default_yes').prop("checked", true);
          } else {
            $('#senderid_default_no').prop("checked", true);
          }
        }

        if (operatorid != null){
          // $('#sendertype_general').prop("checked",false);
          // $('#sendertype_general').parent().hide();
          // $('#sendertype_taletalk').parent().show();
          // $('#sendertype_taletalk').prop("checked",true);
          // $('.general-senderid').hide();
          // $('.talitalk-senderid').show();
          $('#sender_user').val(talitalkuser);
          $('#sender_password').val(talitalkpassword)

          if (status == 1) {
            $('#senderid_status_yes').prop("checked", true);
            
          } else {
            $('#senderid_status_no').prop("checked", true);
          }
          
          if (recdefault == 1) {
            $('#senderid_default_yes').prop("checked", true);
          } else {
            $('#senderid_default_no').prop("checked", true);
          }
        }
        
      });

      // $('.general-senderid').show();
      // $('.talitalk-senderid').hide();
      $('body').on('click','input[type="radio"]', function(){
        //if($(this).prop('checked') == true) {
          // if ($(this).val() == 'general') {
          //   $('.talitalk-senderid').hide();
          //   $('.general-senderid').show();
          // }

          // if ($(this).val() == 'talitalk') {
          //   $('.general-senderid').hide();
          //   $('.talitalk-senderid').show();
          // }
        //}

      });




      var renderAccountsHead = function() {
          $('#accountshead').DataTable().destroy();
          $('#accountshead').DataTable( {
          "order": [[ 0, "desc" ]],  
          "ajax": {
              "processing": true,
              "url": "{{route('render-accounts-head')}}",
              "dataType": 'json',
              "type": "get",
              "beforeSend": function (xhr) {
                  }
              },
              "columnDefs":
              [
                {
                  "visible": false,
                  "searchable": false
                },
                {
                  targets: [0],
                  visible: false,
                  searchable: false
                },
                {
                  targets: [5],
                  render: function (data, type, full, meta) {
                    if (full.status == '1') {
                      {return 'Published'};
                    } else {
                      {return 'Unpublished'};
                    }
                  }
                },
              ],
              "columns": [
                  { "data": "id" },
                  { "data": "acc_head" },
                  { "data": "parent" },
                  { "data": "created_by" },
                  { "data": "updated_by" },
                  { "data": "status" },
                  { "data": "user_type" },
                  {
                      "render": function (data, type, full, meta)
                      { 
                        
                          return '<a href="#" class="btn btn-sm btn-icon btn-pure btn-default accountsrooteditform"  data-toggle="modal" data-target="#addrootaccountshead" data-original-title="edit" data-id="'+full.id+'" data-acchead="'+full.acc_head+'" data-status="'+full.status+'"><i class="icon icon-pencil" aria-hidden="true"></i></a>\n\
                          <a href="#" class="btn btn-sm btn-icon btn-pure btn-default addsubrootaccountshead"  data-toggle="modal" data-target="#addsubrootaccountshead" data-original-title="edit" data-id="'+full.id+'" data-acchead="'+full.acc_head+'"><i class="icon icon-plus" aria-hidden="true"></i></a>\n\
                          <a href="#" class="btn btn-sm btn-icon btn-pure btn-default accountsdtl" data-toggle="tooltip" data-original-title="Remove" data-id="'+full.id+'"> <i class="icon icon-trash" aria-hidden="true"></i></a>'; 
                        
                      }
                  },
              ]
          } );
      }

      renderAccountsHead();


      $('body').on('click','.accountsrooteditform', function(){
        let id = $(this).data('id');
        let acchead = $(this).data('acchead');
        let status = $(this).data('status');
        $('.rootaccountidhead').text('Edit Accounts')

        $('#acc_head').val(acchead);
        $('#account_rootrec_id').val(id);
        $('#accrootfrmmode').val('edt');

        if (status == 1)
        {
          $('#status_yes').prop('checked',true);
          $('#status_yes').val(1);
          $('#status_no').prop('checked',false);
          $('#status_no').val(0);
        } else {
          $('#status_no').prop('checked',true);
          $('#status_no').val(0);
          $('#status_yes').prop('checked',false);
          $('#status_yes').val(1);
        }
      });


      var renderGroupAccountsHead = function() {
          $('#groupaccountshead').DataTable().destroy();
          var groupColumn = 2;
          $('#groupaccountshead').DataTable( {
          "order": [[ groupColumn, 'asc' ]],
          "displayLength": 25,
          "ajax": {
              "processing": true,
              "url": "{{route('render-group-accounts-head')}}",
              "dataType": 'json',
              "type": "get",
              "beforeSend": function (xhr) {
                  }
              },
              "columnDefs":
              [
                {
                  "visible": false,
                  "searchable": false
                },
                {
                  targets: [0],
                  visible: false,
                  searchable: false
                },
                {
                  targets: [5],
                  render: function (data, type, full, meta) {
                    if (full.status == '1') {
                      {return 'Published'};
                    } else {
                      {return 'Unpublished'};
                    }
                  }
                },
              ],
              "columns": [
                  { "data": "id" },
                  { "data": "acc_head" },
                  { "data": "parent" },
                  { "data": "created_by" },
                  { "data": "updated_by" },
                  { "data": "status" },
                  { "data": "user_type" },
                  {
                      "render": function (data, type, full, meta)
                      { 
                        
                          return '<a href="#" class="btn btn-sm btn-icon btn-pure btn-default accountsrooteditform"  data-toggle="modal" data-target="#addrootaccountshead" data-original-title="edit" data-id="'+full.id+'" data-acchead="'+full.acc_head+'" data-status="'+full.status+'"><i class="icon icon-pencil" aria-hidden="true"></i></a>\n\
                          <a href="#" class="btn btn-sm btn-icon btn-pure btn-default addsubrootaccountshead"  data-toggle="modal" data-target="#addsubrootaccountshead" data-original-title="edit" data-id="'+full.id+'" data-acchead="'+full.acc_head+'"><i class="icon icon-plus" aria-hidden="true"></i></a>\n\
                          <a href="#" class="btn btn-sm btn-icon btn-pure btn-default accountsdtl" data-toggle="tooltip" data-original-title="Remove" data-id="'+full.id+'"> <i class="icon icon-trash" aria-hidden="true"></i></a>'; 
                        
                      }
                  },
              ],
              "drawCallback": function ( settings ) {
                var api = this.api();
                var rows = api.rows( {page:'current'} ).nodes();
                var last=null;
    
                api.column(groupColumn, {page:'current'} ).data().each( function ( group, i ) {
                    if ( last !== group ) {
                        $(rows).eq( i ).before(
                            '<tr class="group"><td colspan="7" class="bg-primary" style="font-weight: 900; color:#fff">'+group+'</td></tr>'
                        );
    
                        last = group;
                    }
                } );
              },
          } );
      }

      renderGroupAccountsHead();

      // Order by the grouping
      $('#groupaccountshead tbody').on( 'click', 'tr.group', function () {
          var currentOrder = table.order()[0];
          if ( currentOrder[0] === groupColumn && currentOrder[1] === 'asc' ) {
              table.order( [ groupColumn, 'desc' ] ).draw();
          }
          else {
              table.order( [ groupColumn, 'asc' ] ).draw();
          }
      } );


      var renderBottomAccountsHead = function() {
          $('#bottomaccountshead').DataTable().destroy();
          var groupColumn = 2;
          var groupColumn2 = 3;
          $('#bottomaccountshead').DataTable( {
          "order": [[ groupColumn, 'asc' ],[ groupColumn2, 'asc' ]],
          "displayLength": 25,
          "ajax": {
              "processing": true,
              "url": "{{route('render-bottom-accounts-head')}}",
              "dataType": 'json',
              "type": "get",
              "beforeSend": function (xhr) {
                  }
              },
              "columnDefs":
              [
                {
                  "visible": false,
                  "searchable": false
                },
                {
                  targets: [0],
                  visible: false,
                  searchable: false
                },
                {
                  targets: [6],
                  render: function (data, type, full, meta) {
                    if (full.status == '1') {
                      {return 'Published'};
                    } else {
                      {return 'Unpublished'};
                    }
                  }
                },
              ],
              "columns": [
                  { "data": "id" },
                  { "data": "acc_head" },
                  { "data": "rootparent" },
                  { "data": "parent" },
                  { "data": "created_by" },
                  { "data": "updated_by" },
                  { "data": "status" },
                  { "data": "user_type" },
                  {
                      "render": function (data, type, full, meta)
                      { 
                        
                          return '<a href="#" class="btn btn-sm btn-icon btn-pure btn-default accountsrooteditform"  data-toggle="modal" data-target="#addrootaccountshead" data-original-title="edit" data-id="'+full.id+'" data-acchead="'+full.acc_head+'" data-status="'+full.status+'"><i class="icon icon-pencil" aria-hidden="true"></i></a>\n\
                          <a href="#" class="btn btn-sm btn-icon btn-pure btn-default accountsdtl" data-toggle="tooltip" data-original-title="Remove" data-id="'+full.id+'"> <i class="icon icon-trash" aria-hidden="true"></i></a>'; 
                        
                      }
                  },
              ],
              "drawCallback": function ( settings ) {
                var api = this.api();
                var rows = api.rows( {page:'current'} ).nodes();
                var first=null;
                var last=null;
    
                api.column(groupColumn, {page:'current'} ).data().each( function ( group, i ) {
                    if ( first !== group ) {
                        $(rows).eq( i ).before(
                            '<tr class="group"><td colspan="8" class="bg-primary" style="font-weight: 900; color:#fff">'+group+'<span class="pull-right">Root</span></td></tr>'
                        );
    
                        first = group;
                    }
                } );

                api.column(groupColumn2, {page:'current'} ).data().each( function ( group, i ) {
                    if ( last !== group ) {
                        $(rows).eq( i ).before(
                            '<tr class="group"><td colspan="8" class="bg-primary" style="font-weight: 900; color:#fff">'+group+'<span class="pull-right">Group</span></td></tr>'
                        );
    
                        last = group;
                    }
                } );
              },
          } );
      }

      renderBottomAccountsHead();


      $('body').on('click', '.addsubrootaccountshead', function(){
        let id = $(this).data('id');
        let acchead = $(this).data('acchead');
        $('#account_grouprec_id').val(id);
        $('.accountsparent').text(acchead);
      });


      $('body').on('click','.accountsdtl', function(){
        let id = $(this).data('id');
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
              url: `delete-accounts-head/${id}`,
              type: 'get',
              success: function(res) {
                swal(res.msg, {
                  icon: "success",
                });
                tr.fadeOut(400,function(){
                    tr.remove();
                });
                renderSenderId();
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


      function readUrl(input) {
            if (input.files && input.files[0]) {
                let reader = new FileReader();
                reader.onload = function(e) {
                    $('.nidimg').attr('src', e.target.result);
                }
                
                reader.readAsDataURL(input.files[0]);
            }
        }
        
        
        
        $('.nid').change(function(){
            readUrl(this);
            
        });

        function readApplicationUrl(input) {
            if (input.files && input.files[0]) {
                let reader = new FileReader();
                reader.onload = function(e) {
                    $('.applicationimg').attr('src', e.target.result);
                }
                
                reader.readAsDataURL(input.files[0]);
            }
        }
        
        
        
        $('.application').change(function(){
          readApplicationUrl(this);
            
        });

        function readCustppphotoUrl(input) {
            if (input.files && input.files[0]) {
                let reader = new FileReader();
                reader.onload = function(e) {
                    $('.custppphotoimg').attr('src', e.target.result);
                }
                
                reader.readAsDataURL(input.files[0]);
            }
        }
        
        
        
        $('.custppphoto').change(function(){
          readCustppphotoUrl(this);
            
        });
        
        function readTradeLicencephotoUrl(input) {
            if (input.files && input.files[0]) {
                let reader = new FileReader();
                reader.onload = function(e) {
                    $('.tradelicenceimg').attr('src', e.target.result);
                }
                
                reader.readAsDataURL(input.files[0]);
            }
        }
        
        
        /* Client Document upload */
        $('.tradelicence').change(function(){
          readTradeLicencephotoUrl(this);
            
        });
        
        $('body').on('focus','.nid', function(){
          $(this).removeClass('border-danger')
          $('.nid+.text-danger').hide();
          $('.nid').append('<label class="col-sm-12 font-bold text-success control-label nid-white">[**Doc Type** jpeg, jpg, png|max:200kb]</label>');
        });

        $('body').on('focus','.application', function(){
          $(this).removeClass('border-danger')
          $('.application+.text-danger').hide();
        });

        $('body').on('focus','.custppphoto', function(){
          $(this).removeClass('border-danger')
          $('.custppphoto+.text-danger').hide();
        });

        $('body').on('focus','.tradelicence', function(){
          $(this).removeClass('border-danger')
          $('.tradelicence+.text-danger').hide();
        });

      

      @if(isset($request->user))

      $('.doverify').on('click', function(){
      
        let verified = $(this).data('verified');


        //verified = $('.doverify').attr('verified', !verified);

        $.ajax({
          url: "{{route('root-client-decument-verify',['user' => $request->user])}}",
          method: 'post',

          success: function(r) {
            
            
            
            if (r.msg == 0) {
                $('.doverify').text('Verified: No');
                $('.doverify').addClass('text-danger');
                $('.doverify').removeClass('text-success');
                $('.doverify').attr('data-verified', 1);
            }

            if (r.msg == 1) {
                $('.doverify').text('Verified: Yes');
                $('.doverify').addClass('text-success');
                $('.doverify').removeClass('text-danger');
                $('.doverify').attr('data-verified', 0);
            }
            console.log(r.msg);
          },

          error: function(e) {
            $('.clientunsuccess').css({'display':'block'}).text(e.responseJSON.errmsg);
            setTimeout(function(){
              $('.clientunsuccess').fadeOut();
            },3000)
            
          }

        });
      });

      
      $('.dostatus').on('click', function(){
        let status = $(this).data('status');


        //verified = $('.doverify').attr('verified', !verified);

        $.ajax({
          url: "{{route('root-client-status',['user' => $request->user])}}",
          method: 'post',

          success: function(r) {
            
            
            
            if (r.msg == 'n') {
                $('.dostatus').text('Status: No');
                $('.dostatus').addClass('text-danger');
                $('.dostatus').removeClass('text-success');
                $('.dostatus').attr('data-status', 'n');
            }

            if (r.msg == 'y') {
                $('.dostatus').text('Status: Yes');
                $('.dostatus').addClass('text-success');
                $('.dostatus').removeClass('text-danger');
                $('.dostatus').attr('data-status', 'y');
            }
            console.log(r.msg);
          },

          error: function(e) {
            console.log(e);
            
          }

        });
      });

      @endif

      /** End root tab */

      /**SMS Sale */
      $('.validity_date').datepicker({
        format: "yyyy-mm-dd", //"yyyy-mm-dd",
        todayBtn: true, 
        calendarWeeks: true,
        autoclose: true,
        todayHighlight: true, 
        toggleActive: true,
          
      });	

      $('.invoice_date').datepicker({
        format: "yyyy-mm-dd", //"yyyy-mm-dd",
        todayBtn: true, 
        calendarWeeks: true,
        autoclose: true,
        todayHighlight: true, 
        toggleActive: true,
          
      });	


      function addCommas(nStr) {
          nStr += '';
          var x = nStr.split('.');
          var x1 = x[0];
          var x2 = x.length > 1 ? '.' + x[1] : '';
          var rgx = /(\d+)(\d{3})/;
          while (rgx.test(x1)) {
              x1 = x1.replace(rgx, '$1' + ',' + '$2');
          }
          return x1 + x2;
      }

      let invoiceTotal = 0;
      $('#invoice_client').focus();
      if (localStorage.getItem('saleItem')) {
        let storeProduct = localStorage.getItem('saleItem');
        let productInCart = JSON.parse(storeProduct);
  
        $('.carttable tbody').empty();
        let i = 1;
        productInCart.forEach((product,index) => {
          
          invoiceTotal += product.price;
          if (product.invoice_vat != null)
          {
            $('#invoice_vat').val(product.invoice_vat)
            let invoiceVat = ($('#invoice_vat').val()/100*invoiceTotal);
            $('.invoicevat').text(addCommas(invoiceVat.toFixed(2)));
            let grandtotal = addCommas((invoiceTotal-invoiceVat).toFixed(2));
            $('.grandtotal').text(grandtotal);
          } else {
            $('#invoice_vat').val(0)
            let invoiceVat = ($('#invoice_vat').val()/100*invoiceTotal);
            $('.invoicevat').text(addCommas(invoiceVat.toFixed(2)));
            let grandtotal = addCommas((invoiceTotal-invoiceVat).toFixed(2));
            $('.grandtotal').text(grandtotal);
          }

          $('.invoicetotal').text(addCommas(invoiceTotal.toFixed(2)));
          $('#invoice_client').val(product.client);
          $('#invoice_date').val(product.invoice_date);
          $('#paymentoption').val(product.paymentoption);
          $('#paymentby').val(product.paymentby);
          $('#remarks').val(product.remarks);
          $('.carttable tbody').prepend('<tr>'+
                      '<td>'+i+'</td>'+
                      '<td>'+product.sms_type+'</td>'+
                      '<td>'+product.smsqty+'</td>'+
                      '<td>'+product.price+'</td>'+
                      '<td class="text-center">'+
                          //'<a href="#" class="icon icon-pencil btn edt-currency" data-transid="" data-smstype="'+product.sms_type+'" data-smsqty="'+product.smsqty+'" data-price="'+product.price+'" data-validity_date="'+product.validity_date+'"></a> '+
                          '<a href="#" data-recindex="'+index+'" class="icon icon-trash btn dlt-prdb" data-id=""></a>'+
                      '</td>'+
                  '</tr>');

                  i++;
        });
      }

      $('body').on('keyup','#invoice_vat',function(){
        let invoiceVat = ($(this).val()/100*invoiceTotal);
        $('.invoicevat').text(invoiceVat);
        let grandtotal = addCommas(invoiceTotal-invoiceVat);
        $('.grandtotal').text(grandtotal);

        let storeProduct = localStorage.getItem('saleItem');
        let productInCart = JSON.parse(storeProduct);
        let productarr = [];
        if(localStorage.getItem('saleItem'))
        {
          productInCart.forEach(async (product,index) => {
              await productarr.push({
                client: product.client,
                invoice_date: product.invoice_date,
                sms_type: product.sms_type,
                user_type: product.user_type,
                smsqty: product.smsqty,               
                price: product.price,
                validity_date: product.validity_date,
                invoice_vat: parseFloat($('#invoice_vat').val()),
                paymentoption: $('#paymentoption').val(),
                paymentby: $('#paymentby').val(),
                remarks: $('#remarks').val()
              });
              await localStorage.setItem('saleItem', JSON.stringify(productarr));
          });
        }

      });


      $('body').on('change','#paymentoption',function(){

        if ($('#invoice_vat').val() == "") {
          $('#invoice_vat').focus();
        }
        let storeProduct = localStorage.getItem('saleItem');
        let productInCart = JSON.parse(storeProduct);
        let productarr = [];
        if(localStorage.getItem('saleItem'))
        {
          productInCart.forEach(async (product,index) => {
              await productarr.push({
                client: product.client,
                invoice_date: product.invoice_date,
                sms_type: product.sms_type,
                user_type: product.user_type,
                smsqty: product.smsqty,               
                price: product.price,
                validity_date: product.validity_date,
                invoice_vat: product.invoice_vat,
                paymentoption: $('#paymentoption').val(),
                paymentby: $('#paymentby').val(),
                remarks: $('#remarks').val()
              });
              await localStorage.setItem('saleItem', JSON.stringify(productarr));
          });
        }

      });

      $('body').on('change','#paymentby',function(){

          if ($('#invoice_vat').val() == "") {
            $('#invoice_vat').focus();
          }

          if ($('#paymentoption').val() == "") {
            $('#paymentoption').focus();
          }
          let storeProduct = localStorage.getItem('saleItem');
          let productInCart = JSON.parse(storeProduct);
          let productarr = [];
          if(localStorage.getItem('saleItem'))
          {
            productInCart.forEach(async (product,index) => {
                await productarr.push({
                  client: product.client,
                  invoice_date: product.invoice_date,
                  sms_type: product.sms_type,
                  user_type: product.user_type,
                  smsqty: product.smsqty,               
                  price: product.price,
                  validity_date: product.validity_date,
                  invoice_vat: product.invoice_vat,
                  paymentoption: $('#paymentoption').val(),
                  paymentby: $('#paymentby').val(),
                  remarks: $('#remarks').val()
                });
                await localStorage.setItem('saleItem', JSON.stringify(productarr));
            });
          }

      });

      $('body').on('keyup','#remarks',function(){
          if ($('#invoice_vat').val() == "") {
            $('#invoice_vat').focus();
          }

          if ($('#paymentoption').val() == "") {
            $('#paymentoption').focus();
          }

          if ($('#paymentby').val() == "") {
            $('#paymentby').focus();
          }
          let storeProduct = localStorage.getItem('saleItem');
          let productInCart = JSON.parse(storeProduct);
          let productarr = [];
          if(localStorage.getItem('saleItem'))
          {
            productInCart.forEach(async (product,index) => {
                await productarr.push({
                  client: product.client,
                  invoice_date: product.invoice_date,
                  sms_type: product.sms_type,
                  user_type: product.user_type,
                  smsqty: product.smsqty,               
                  price: product.price,
                  validity_date: product.validity_date,
                  invoice_vat: product.invoice_vat,
                  paymentoption: product.paymentoption,
                  paymentoption: $('#paymentoption').val(),
                  paymentby: $('#paymentby').val(),
                  remarks: $('#remarks').val()
                });
                await localStorage.setItem('saleItem', JSON.stringify(productarr));
            });
          }

      });


      $('body').on('click','.smssale',function(){
        let salearr = [];
        let sameprdqty = 0;
        let sameprdprice = 0;
        let f = 0;
        let ctdate = new Date();
        let ctday = ctdate.getDay();
        let hours = ctdate.getHours();
        let minutes = ctdate.getMinutes();
        let rootuser = '{{request()->user()->id}}'

        if ($('#invoice_client').val() == "")
        {
          document.querySelectorAll('#invoice_client')[0].focus();
          document.querySelector('.clienterr').style.display = 'block';
          return false;
        } else {
          document.querySelector('.clienterr').style.display = 'none';
        }

        if ($('#invoice_date').val() == "")
        {
          document.querySelector('#invoice_date').focus();
          document.querySelector('.invoicedateerr').style.display = 'block';
          //$('.invoicedateerr').css({'display':'block'});
          return false;
        } else {
          $('.invoicedateerr').css({'display':'none'});
          document.querySelector('.invoicedateerr').style.display = 'none';
        }

        if ($('#sms_type').val() == "")
        {
          document.querySelector('#sms_type').focus();
          document.querySelector('.smstypeerr').style.display = 'block';
          //$('.invoicedateerr').css({'display':'block'});
          return false;
        } else {
          document.querySelector('.smstypeerr').style.display = 'none';
        }

        if ($('#smsqty').val() == "")
        {
          document.querySelector('#smsqty').focus();
          document.querySelector('.smsqtyerr').style.display = 'block';
          //$('.invoicedateerr').css({'display':'block'});
          return false;
        } else {
          document.querySelector('.smsqtyerr').style.display = 'none';
        }

        if ($('#price').val() == "")
        {
          document.querySelector('#price').focus();
          document.querySelector('.priceerr').style.display = 'block';
          //$('.invoicedateerr').css({'display':'block'});
          return false;
        } else {
          document.querySelector('.priceerr').style.display = 'none';
        }

        if ($('#validity_date').val() == "")
        {
          document.querySelector('#validity_date').focus();
          document.querySelector('.validitydateerr').style.display = 'block';
          //$('.invoicedateerr').css({'display':'block'});
          return false;
        } else {
          document.querySelector('.validitydateerr').style.display = 'none';
        }
        
        if (localStorage.getItem('saleItem')) {
          let storeProduct = localStorage.getItem('saleItem');
          let productInCart = JSON.parse(storeProduct);
          productInCart.forEach(product => {

            if (product.sms_type == $('#sms_type').val() && product.validity_date == $('#validity_date').val())
            {
              product.smsqty += parseFloat($('#smsqty').val());

              product.price += parseFloat($('#price').val());
              f = 1;
            }

          });

          if (f == 0) {
            productInCart.push({
              client: $('#invoice_client').val(),
              invoice_date: $('#invoice_date').val(),
              sms_type: $('#sms_type').val(),
              user_type: $('#user_type').val(),
              smsqty: parseFloat($('#smsqty').val()),               
              price: parseFloat($('#price').val()),
              validity_date: $('#validity_date').val(),
              invoice_vat: 0,
              paymentoption: $('#paymentoption').val(),
              paymentby: $('#paymentby').val(),
              remarks: $('#remarks').val()
            });

          }
          localStorage.setItem('saleItem', JSON.stringify(productInCart));
          
        } else {

          salearr.push({
            client: $('#invoice_client').val(),
            invoice_date: $('#invoice_date').val(),
            sms_type: $('#sms_type').val(),
            user_type: $('#user_type').val(),
            smsqty: parseFloat($('#smsqty').val()),               
            price: parseFloat($('#price').val()),
            validity_date: $('#validity_date').val(),
            invoice_vat: 0,
            paymentoption: $('#paymentoption').val(),
            paymentby: $('#paymentby').val(),
            remarks: $('#remarks').val()
          });

          localStorage.setItem('saleItem', JSON.stringify(salearr));
          
        }


        let storeProduct = localStorage.getItem('saleItem');
        let productInCart = JSON.parse(storeProduct);

        $('.carttable tbody').empty();
        let i = 1;
        
        productInCart.forEach((product,index) => {
          invoiceTotal += product.price;
          if (product.invoice_vat != null)
          {
            $('#invoice_vat').val(product.invoice_vat)
            let invoiceVat = ($('#invoice_vat').val()/100*invoiceTotal);
            $('.invoicevat').text(addCommas(invoiceVat.toFixed(2)));
            let grandtotal = addCommas((invoiceTotal-invoiceVat).toFixed(2));
            $('.grandtotal').text(grandtotal);
          } else {
            $('#invoice_vat').val(0)
            let invoiceVat = ($('#invoice_vat').val()/100*invoiceTotal);
            $('.invoicevat').text(addCommas(invoiceVat.toFixed(2)));
            let grandtotal = addCommas((invoiceTotal-invoiceVat).toFixed(2));
            $('.grandtotal').text(grandtotal);
          }
          $('.invoicetotal').text(addCommas(invoiceTotal.toFixed(2)));
          $('.carttable tbody').prepend('<tr>'+
                      '<td>'+i+'</td>'+
                      '<td>'+product.sms_type+'</td>'+
                      '<td>'+product.smsqty+'</td>'+
                      '<td>'+product.price+'</td>'+
                      '<td class="text-center">'+
                          //'<a href="#" class="icon icon-pencil btn edt-currency" data-transid="" data-smstype="'+product.sms_type+'" data-smsqty="'+product.smsqty+'" data-price="'+product.price+'" data-validity_date="'+product.validity_date+'"></a> '+
                          '<a href="#" data-recindex="'+index+'" class="icon icon-trash btn dlt-prdb" data-id=""></a>'+
                      '</td>'+
                  '</tr>');

                  i++;
        });

        $('#smsqty').val('');
        $('#price').val('');
        $('#validity_date').val('');
        $('#paymentoption').val('');
        $('#remarks').val('');
        $('#sms_type').focus();
      });


      $('body').on('click','.clearinvoice', function(){
        swal({
          title: "Are you sure?",
          text: "Once deleted, you will not be able to recover this invoice!",
          icon: "warning",
          buttons: true,
        })
        .then((willDelete) => {
          console.log(willDelete);
          if (willDelete) {
            localStorage.removeItem('saleItem');
            swal('Invoice deleted successfully', {
              icon: "success",
            });

            $('.carttable tbody').empty();
            
            $('#invoice_vat').val(0)
            $('.invoicetotal').text('');
            $('.grandtotal').text('');
            invoiceTotal = 0;

          } 
          /*else {
            swal("Your imaginary file is safe!");
          }
          */
        });


      });

      $('body').on('click','.dlt-prdb',function(e){
        e.preventDefault();

        let id = $(this).data('recindex');
        let tr = $(this).closest('tr');
        let storeItem = localStorage.getItem('saleItem');
        let productInCart = JSON.parse(storeItem);

        swal({
          title: "Are you sure?",
          text: "Once deleted, you will not be able to recover this item!",
          icon: "warning",
          buttons: true,
        })
        .then((willDelete) => {
          console.log(willDelete);
          if (willDelete) {
            productInCart.forEach((product,index) => {
              if (id == index)
              {
                productInCart.splice(index,1);
                swal('Cart item deleted successfully', {
                  icon: "success",
                });
                localStorage.setItem('saleItem', JSON.stringify(productInCart));
                
                tr.fadeOut(400,function(){
                    tr.remove();
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

      let savprdarr = [];
      $('body').on('click','.save-invoice', function(){
        let storeItem = localStorage.getItem('saleItem');
        let productInCart = JSON.parse(storeItem);
        savprdarr = [];

        if ($('#invoice_vat').val() == "")
        {
          document.querySelector('#invoice_vat').focus();
          document.querySelector('.vaterr').style.display = 'block';
          //$('.invoicedateerr').css({'display':'block'});
          return false;
        } else {
          document.querySelector('.vaterr').style.display = 'none';
        }

        if ($('#paymentoption').val() == "")
        {
          document.querySelector('#paymentoption').focus();
          document.querySelector('.paymentoptionerr').style.display = 'block';
          //$('.invoicedateerr').css({'display':'block'});
          return false;
        } else {
          document.querySelector('.paymentoptionerr').style.display = 'none';
        }

        if ($('#paymentoption').val() != 'cash')
        {
          document.querySelector('#paymentby').focus();
          document.querySelector('.paymentbyerr').style.display = 'block';
          //$('.invoicedateerr').css({'display':'block'});
          return false;
        } else {
          document.querySelector('.paymentbyerr').style.display = 'none';
        }

        if ($('#remarks').val() == "")
        {
          document.querySelector('#remarks').focus();
          document.querySelector('.remarkserr').style.display = 'block';
          //$('.invoicedateerr').css({'display':'block'});
          return false;
        } else {
          document.querySelector('.remarkserr').style.display = 'none';
        }

        if (localStorage.getItem('saleItem'))
        {
          productInCart.forEach(async product => {
            await savprdarr.push({
                client: product.client,
                invoice_date: product.invoice_date,
                sms_type: product.sms_type,
                user_type: product.user_type,
                smsqty: product.smsqty,               
                price: product.price,
                validity_date: product.validity_date,
                invoice_vat: product.invoice_vat,
                paymentoption: product.paymentoption,
                paymentby: product.paymentby,
                remarks: product.remarks
              });
          });
        }

        if (savprdarr.length > 0) {
          $.ajax({
            url: "{{route('product-sale')}}",
            type: 'post',
            data: {
              dataarr: savprdarr,
            },
            beforeSend: function(){
              
            },
            success: function(res) {
              localStorage.removeItem('saleItem');
              swal(res.msg, {
                icon: "success",
              });

              $('.carttable tbody').empty();
              
              $('#invoice_vat').val(0)
              $('.invoicetotal').text('');
              $('.grandtotal').text('');
              $('#paymentoption').val('');
              $('#paymentby').val('');
              $('#remarks').val('');
              invoiceTotal = 0;
            },
            error: function(err) {
              console.log(err.responseJSON.errmsg);
            }
          })
        }
      });

      /** Root Clients invoice **/

      var rootInvoices = function(){
      $('.rootinvoicelist').DataTable({
          "order": [[ 0, "desc" ]],
          "processing": true,
          "language": {
            processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span> '
          },
          "serverSide": true,
          "ajax": { "url": "show-client-invoices","type": "get" },
          columnDefs: [
              
            //{ className: "actions", targets: 9  },  
            {
                targets: [ 0 ],
                visible: false,
                searchable: false
            }, 
            {  targets: -1,
              render: function (data, type, full, meta) {
                                      console.log(full);
                                      { return '<a href="#" class="btn btn-sm btn-icon btn-pure btn-default rootuseredtfrm" data-original-title="edit" data-id="'+full[0]+'"><i class="icon icon-pencil" aria-hidden="true"></i></a>\n\
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
              { "sWidth":"60px","sClass": "actions" }
          ],
          oLanguage: {sProcessing: '<div class="loader vertical-align-middle loader-rotate-plane" style="background-color: #2e688e; "></div>'}

        });
      }
      rootInvoices();


      var rootResellerInvoices = function(){
      $('.resellerinvoicelist').DataTable({
          "order": [[ 0, "desc" ]],
          "processing": true,
          "language": {
            processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span> '
          },
          "serverSide": true,
          "ajax": { "url": "show-reseller-invoices","type": "get" },
          columnDefs: [
              
            //{ className: "actions", targets: 9  },  
            {
                targets: [ 0 ],
                visible: false,
                searchable: false
            }, 
            {  targets: -1,
              render: function (data, type, full, meta) {
                                      console.log(full);
                                      { return '<a href="root-client/'+full[0]+'/edit" class="btn btn-sm btn-icon btn-pure btn-default rootuseredtfrm" data-original-title="edit" data-id="'+full[0]+'"><i class="icon icon-pencil" aria-hidden="true"></i></a>\n\
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
              { "sWidth":"60px","sClass": "actions" }
          ],
          oLanguage: {sProcessing: '<div class="loader vertical-align-middle loader-rotate-plane" style="background-color: #2e688e; "></div>'}

        });
      }
      rootResellerInvoices();


    })(document, window, jQuery);
  </script>

  @elseif(Auth::guard('manager')->check())

  <script type="text/javascript">
    (function(document, window, $){
   
   
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
             "ajax": { "url": "manager-resellers-data","type": "get" },
             columnDefs: [
                 
               //{ className: "actions", targets: 9  },  
               {
                   targets: [ 0 ],
                   visible: false,
                   searchable: false
               }, 
               {  targets: -1,
                 render: function (data, type, full, meta) {
                                         console.log(full);
                                         { return '<a href="#" class="btn btn-sm btn-icon btn-pure btn-default restdealedtfrm" data-toggle="modal" data-target="#restaurantDealEditForm" data-original-title="edit" data-id="'+full[0]+'"><i class="icon icon-pencil" aria-hidden="true"></i></a>\n\
                                   <a href="#" class="btn btn-sm btn-icon btn-pure btn-default restdealdtl" data-toggle="tooltip" data-original-title="Remove" data-id="'+full[0]+'"><i class="icon icon-trash" aria-hidden="true"></i></a>'; }
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
                 { "sWidth":"60px","sClass": "actions" }
             ],
             oLanguage: {sProcessing: '<div class="loader vertical-align-middle loader-rotate-plane" style="background-color: #2e688e; "></div>'}
   
           });
         }
         rootReseller();
   
         /** Root Clients **/
   
         var rootClient = function(){
         //$('.rootuser').DataTable().destroy();
         $('.activedeal').css({'background-color':'#3e8ef7','color':'#fff'});    
         $('.clients').DataTable({
             "order": [[ 0, "desc" ]],
             "processing": true,
             "language": {
               processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span> '
             },
    
             "serverSide": true,
             "ajax": { "url": "manager-clients-data","type": "get" },
             columnDefs: [
                 
               //{ className: "actions", targets: 9  },  
               {
                   targets: [ 0 ],
                   visible: false,
                   searchable: false
               }, 
               {  targets: -1,
                 render: function (data, type, full, meta) {
                                         console.log(full);
                                         { return '<a href="#" class="btn btn-sm btn-icon btn-pure btn-default restdealedtfrm" data-toggle="modal" data-target="#restaurantDealEditForm" data-original-title="edit" data-id="'+full[0]+'"><i class="icon icon-pencil" aria-hidden="true"></i></a>\n\
                                   <a href="#" class="btn btn-sm btn-icon btn-pure btn-default restdealdtl" data-toggle="tooltip" data-original-title="Remove" data-id="'+full[0]+'"><i class="icon icon-trash" aria-hidden="true"></i></a>'; }
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
                 { "sWidth":"60px","sClass": "actions" }
             ],
             oLanguage: {sProcessing: '<div class="loader vertical-align-middle loader-rotate-plane" style="background-color: #2e688e; "></div>'}
   
           });
         }
         rootClient();
       })(document, window, jQuery);
     </script>

@elseif(Auth::guard('reseller')->check())

<script type="text/javascript">
  (function(document, window, $){
 
       
       /** Root Clients **/
 
       var rootClient = function(){
       //$('.rootuser').DataTable().destroy();
       $('.activedeal').css({'background-color':'#3e8ef7','color':'#fff'});    
       $('.clients').DataTable({
           "order": [[ 0, "desc" ]],
           "processing": true,
           "language": {
             processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span> '
           },
  
           "serverSide": true,
           "ajax": { "url": "reseller-clients-data","type": "get" },
           columnDefs: [
               
             //{ className: "actions", targets: 9  },  
             {
                 targets: [ 0 ],
                 visible: false,
                 searchable: false
             }, 
             {  targets: -1,
               render: function (data, type, full, meta) {
                                       console.log(full);
                                       { return '<a href="#" class="btn btn-sm btn-icon btn-pure btn-default restdealedtfrm" data-toggle="modal" data-target="#restaurantDealEditForm" data-original-title="edit" data-id="'+full[0]+'"><i class="icon icon-pencil" aria-hidden="true"></i></a>\n\
                                        <a href="#" class="btn btn-sm btn-icon btn-pure btn-default userloginfromreseller" data-original-title="edit" data-email="'+full[2]+'"><i class="fa fa-sign-in" aria-hidden="true"></i></a>\n\
                                 <a href="#" class="btn btn-sm btn-icon btn-pure btn-default restdealdtl" data-toggle="tooltip" data-original-title="Remove" data-id="'+full[0]+'"><i class="icon icon-trash" aria-hidden="true"></i></a>'; }
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
               { "sWidth":"60px","sClass": "actions" }
           ],
           oLanguage: {sProcessing: '<div class="loader vertical-align-middle loader-rotate-plane" style="background-color: #2e688e; "></div>'}
 
         });
       }
       rootClient();

       $('body').on('click','.userloginfromreseller',function(e){
        e.preventDefault();
        let email = $(this).data('email');
        $.ajax({
          url: "client-login-from-reseller/"+email,
          type: "get",
          success: function(res)
          {
            window.location.href = 'client';
          },
          error: function(err)
          {
            $('.usernotfound').text(err.responseJSON.errmsg);
          }
        })
      });


       var renderSenderId = function() {
          $('#smssender').DataTable().destroy();
          $('#smssender').DataTable( {
          "order": [[ 0, "desc" ]],  
          "ajax": {
              "processing": true,
              "url": "{{route('rander-sms-senderid-for-reseller')}}",
              "dataType": 'json',
              "type": "get",
              "beforeSend": function (xhr) {
                  }
              },
              "columnDefs":
              [
                {
                  "visible": false,
                  "searchable": false
                },
                {
                  targets: [0],
                  visible: false,
                  searchable: false
                },
                {
                  targets: [2],
                  render: function (data, type, full, meta) {
                    if (full.operator_name != null) {
                      {return full.operator_name.operator_name};
                    } else {
                      {return 'General'};
                    }
                  }
                },
                {
                  targets: [3],
                  render: function (data, type, full, meta) {
                    if (full.status == '1') {
                      {return 'Published'};
                    } else {
                      {return 'Unpublished'};
                    }
                  }
                },
                {
                  targets: [4],
                  render: function (data, type, full, meta) {
                    if (full.default == '1') {
                      {return 'Yes'};
                    } else {
                      {return 'No'};
                    }
                  }
                },
              ],
              "columns": [
                  { "data": "id" },
                  { "data": "sender_name" },
                  { "data": "operator_name" },
                  { "data": "status" },
                  { "data": "default" },
                  { "data": "user" },
                  { "data": "password" },
                  { "data": "created_by" },
                  { "data": "updated_by" },
                  {
                      "render": function (data, type, full, meta)
                      { 
                        if (full.status == 1) {
                          return '<a href="#" class="btn btn-sm btn-icon btn-pure btn-default assignsenderid"  data-toggle="modal" data-target="#assignsenderid" data-original-title="edit" data-id="'+full.id+'" data-sendername="'+full.sender_name+'"><i class="fa fa-users" aria-hidden="true"></i></a>'; 
                        } else {
                          return '<span style="background-color:red; padding:5px; color:#fff;">Inactive</span>'; 
                        }
                      }
                  },
              ]
          } );
      }

      renderSenderId();



      $('.from_date').datepicker({
        format: "yyyy-mm-dd", //"yyyy-mm-dd",
        todayBtn: true, 
        calendarWeeks: true,
        autoclose: true,
        todayHighlight: true, 
        toggleActive: true,
          
      });	

      $('.to_date').datepicker({
        format: "yyyy-mm-dd", //"yyyy-mm-dd",
        todayBtn: true, 
        calendarWeeks: true,
        autoclose: true,
        todayHighlight: true, 
        toggleActive: true,
          
      });	


      var clientSmsSendReport = function() {
          $.ajax({
            url: "{{route('reseller-client-total-sms-send')}}",
            type: "get",
            data: {
              fromdate: $('#from_date').val(),
              todate: $('#to_date').val(),
              userid: $('#userid').val()
            },
            success: function(res) {
              $('.totalsendsms').text(`Total SMS sent: ${res}`);
              $('.totalsendsms').addClass('font-bold');
            }
          });
          $('.resellerclientsmsreport').DataTable().destroy();
          
          $('.resellerclientsmsreport').DataTable( {
          "ajax": {
                "processing": true,
                "url": "{{route('reseller-clients-sms-send-data')}}",
                "dataType": 'json',
                "type": "post",
                "data": {
                  fromdate: $('#from_date').val(),
                  todate: $('#to_date').val(),
                  userid: $('#userid').val()
                },
                "beforeSend": function (xhr) {
                  
                },
              },
              "columnDefs":
              [
                {
                  "visible": false,
                  "searchable": false
                },
              ],
              "columns": [
                  { "data": "rowid" },
                  { "data": "name" },
                  { "data": "email" },
                  { "data": "senderid" },
                  { "data": "contact" },
                  { "data": "smstype" },
                  { "data": "smscategory" },
                  { "data": "noofsms" },
                  { "data": "sendfrom" },
                  {
                      "render": function (data, type, full, meta)
                      { 
                        if (full.status == "Delivered") {
                          return '<a href="#" class="btn btn-success btn-sm">'+full.status+'</a>'; 
                        } else {
                          return '<a href="#" class="btn btn-danger btn-sm">'+full.status+'</a>'; 
                        }
                      }
                  }
              ],
          } );
      }

      clientSmsSendReport();

      if ($('.dataTables_empty').text() == "Loading...") {
        $('.dataTables_empty').text('No Record Found');
      }

      $('body').on('click','.getresellerclietsmsreport', function(){
        
        clientSmsSendReport();
        if (document.querySelector('table tbody > tr > td').className == "dataTables_empty") {
          
          setTimeout(() => $('.dataTables_empty').text('No Record Found'), 5000);
        }
      });


      var clientSmsSendConsulateReport = function() {
          $.ajax({
            url: "{{route('reseller-client-sms-sent-total-consulate-report')}}",
            type: "get",
            data: {
              fromdate: $('#from_date').val(),
              todate: $('#to_date').val(),
              userid:$('#userid').val()
            },
            success: function(res) {
              $('.totalconsulatesendsms').text(`Total SMS sent: ${res[0].totalsms}`);
              $('.totalconsulatesendsms').addClass('font-bold');
            }
          });
          $('.resellerclientsmscountreport').DataTable().destroy();
          
          $('.resellerclientsmscountreport').DataTable( {
          "ajax": {
                "processing": true,
                "url": "{{route('reseller-clients-send-sms-consulate-rpt')}}",
                "dataType": 'json',
                "type": "post",
                "data": {
                  fromdate: $('#from_date').val(),
                  todate: $('#to_date').val(),
                  userid:$('#userid').val()
                },
                "beforeSend": function (xhr) {
                  
                },
              },
              "columnDefs":
              [
                {
                  "visible": false,
                  "searchable": false
                },
              ],
              "columns": [
                  { "data": "rowid" },
                  { "data": "name" },
                  { "data": "email" },
                  { "data": "campaingname" },
                  { "data": "smscount" },
                  { "data": "smscategory" },
                  { "data": "ownertype" },
                  { "data": "owner" },
                  { "data": "submittedat" },
                  
              ],
          } );
      }

      clientSmsSendConsulateReport();

      $('body').on('click','.getresellerclietcountsmsreport', function(){
        
        clientSmsSendConsulateReport();
        if (document.querySelector('table tbody > tr > td').className == "dataTables_empty") {
          
          setTimeout(() => $('.dataTables_empty').text('No Record Found'), 5000);
        }
      });



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
         $('.load-senderid').load("/load-assigned-reseller-client-sender-id/"+id);
         $('#smsoperatorapi').attr('action',`{{route('assign-reseller-client-senderid')}}`)
         $('#activeclients').trigger("chosen:updated");
         
      });

      $('body').on('click','.senderclientdtl', function(){
        let assign_user_senderid = $(this).data('assign_user_senderid');
        let sms_sender_id = $(this).data('sms_sender_id');
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
              url: `delete-reseller-client-senderid/${assign_user_senderid}/${sms_sender_id}`,
              type: 'get',
              success: function(res) {
                swal(res.msg, {
                  icon: "success",
                });
                tr.fadeOut(400,function(){
                    tr.remove();
                });
                renderSenderId();
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



      /**SMS Sale */
      $('.validity_date').datepicker({
        format: "yyyy-mm-dd", //"yyyy-mm-dd",
        todayBtn: true, 
        calendarWeeks: true,
        autoclose: true,
        todayHighlight: true, 
        toggleActive: true,
          
      });	

      $('.invoice_date').datepicker({
        format: "yyyy-mm-dd", //"yyyy-mm-dd",
        todayBtn: true, 
        calendarWeeks: true,
        autoclose: true,
        todayHighlight: true, 
        toggleActive: true,
          
      });	


      function addCommas(nStr) {
          nStr += '';
          var x = nStr.split('.');
          var x1 = x[0];
          var x2 = x.length > 1 ? '.' + x[1] : '';
          var rgx = /(\d+)(\d{3})/;
          while (rgx.test(x1)) {
              x1 = x1.replace(rgx, '$1' + ',' + '$2');
          }
          return x1 + x2;
      }

      let invoiceTotal = 0;
      $('#invoice_client').focus();
      if (localStorage.getItem('saleItem')) {
        let storeProduct = localStorage.getItem('saleItem');
        let productInCart = JSON.parse(storeProduct);
  
        $('.carttable tbody').empty();
        let i = 1;
        productInCart.forEach((product,index) => {
          
          invoiceTotal += product.price;
          if (product.invoice_vat != null)
          {
            $('#invoice_vat').val(product.invoice_vat)
            let invoiceVat = ($('#invoice_vat').val()/100*invoiceTotal);
            $('.invoicevat').text(addCommas(invoiceVat.toFixed(2)));
            let grandtotal = addCommas((invoiceTotal-invoiceVat).toFixed(2));
            $('.grandtotal').text(grandtotal);
          } else {
            $('#invoice_vat').val(0)
            let invoiceVat = ($('#invoice_vat').val()/100*invoiceTotal);
            $('.invoicevat').text(addCommas(invoiceVat.toFixed(2)));
            let grandtotal = addCommas((invoiceTotal-invoiceVat).toFixed(2));
            $('.grandtotal').text(grandtotal);
          }

          $('.invoicetotal').text(addCommas(invoiceTotal.toFixed(2)));
          $('#invoice_client').val(product.client);
          $('#invoice_date').val(product.invoice_date);
          $('#paymentoption').val(product.paymentoption);
          $('#paymentby').val(product.paymentby);
          $('#remarks').val(product.remarks);
          $('.carttable tbody').prepend('<tr>'+
                      '<td>'+i+'</td>'+
                      '<td>'+product.sms_type+'</td>'+
                      '<td>'+product.smsqty+'</td>'+
                      '<td>'+product.price+'</td>'+
                      '<td class="text-center">'+
                          //'<a href="#" class="icon icon-pencil btn edt-currency" data-transid="" data-smstype="'+product.sms_type+'" data-smsqty="'+product.smsqty+'" data-price="'+product.price+'" data-validity_date="'+product.validity_date+'"></a> '+
                          '<a href="#" data-recindex="'+index+'" class="icon icon-trash btn dlt-prdb" data-id=""></a>'+
                      '</td>'+
                  '</tr>');

                  i++;
        });
      }

      $('body').on('keyup','#invoice_vat',function(){
        let invoiceVat = ($(this).val()/100*invoiceTotal);
        $('.invoicevat').text(invoiceVat);
        let grandtotal = addCommas(invoiceTotal-invoiceVat);
        $('.grandtotal').text(grandtotal);

        let storeProduct = localStorage.getItem('saleItem');
        let productInCart = JSON.parse(storeProduct);
        let productarr = [];
        if(localStorage.getItem('saleItem'))
        {
          productInCart.forEach(async (product,index) => {
              await productarr.push({
                client: product.client,
                invoice_date: product.invoice_date,
                sms_type: product.sms_type,
                user_type: product.user_type,
                smsqty: product.smsqty,               
                price: product.price,
                validity_date: product.validity_date,
                invoice_vat: parseFloat($('#invoice_vat').val()),
                paymentoption: $('#paymentoption').val(),
                paymentby: $('#paymentby').val(),
                remarks: $('#remarks').val()
              });
              await localStorage.setItem('saleItem', JSON.stringify(productarr));
          });
        }

      });


      $('body').on('change','#paymentoption',function(){

        if ($('#invoice_vat').val() == "") {
          $('#invoice_vat').focus();
        }
        let storeProduct = localStorage.getItem('saleItem');
        let productInCart = JSON.parse(storeProduct);
        let productarr = [];
        if(localStorage.getItem('saleItem'))
        {
          productInCart.forEach(async (product,index) => {
              await productarr.push({
                client: product.client,
                invoice_date: product.invoice_date,
                sms_type: product.sms_type,
                user_type: product.user_type,
                smsqty: product.smsqty,               
                price: product.price,
                validity_date: product.validity_date,
                invoice_vat: product.invoice_vat,
                paymentoption: $('#paymentoption').val(),
                paymentby: $('#paymentby').val(),
                remarks: $('#remarks').val()
              });
              await localStorage.setItem('saleItem', JSON.stringify(productarr));
          });
        }

      });

      $('body').on('change','#paymentby',function(){

          if ($('#invoice_vat').val() == "") {
            $('#invoice_vat').focus();
          }

          if ($('#paymentoption').val() == "") {
            $('#paymentoption').focus();
          }
          let storeProduct = localStorage.getItem('saleItem');
          let productInCart = JSON.parse(storeProduct);
          let productarr = [];
          if(localStorage.getItem('saleItem'))
          {
            productInCart.forEach(async (product,index) => {
                await productarr.push({
                  client: product.client,
                  invoice_date: product.invoice_date,
                  sms_type: product.sms_type,
                  user_type: product.user_type,
                  smsqty: product.smsqty,               
                  price: product.price,
                  validity_date: product.validity_date,
                  invoice_vat: product.invoice_vat,
                  paymentoption: $('#paymentoption').val(),
                  paymentby: $('#paymentby').val(),
                  remarks: $('#remarks').val()
                });
                await localStorage.setItem('saleItem', JSON.stringify(productarr));
            });
          }

      });

      $('body').on('keyup','#remarks',function(){
          if ($('#invoice_vat').val() == "") {
            $('#invoice_vat').focus();
          }

          if ($('#paymentoption').val() == "") {
            $('#paymentoption').focus();
          }

          if ($('#paymentby').val() == "") {
            $('#paymentby').focus();
          }
          let storeProduct = localStorage.getItem('saleItem');
          let productInCart = JSON.parse(storeProduct);
          let productarr = [];
          if(localStorage.getItem('saleItem'))
          {
            productInCart.forEach(async (product,index) => {
                await productarr.push({
                  client: product.client,
                  invoice_date: product.invoice_date,
                  sms_type: product.sms_type,
                  user_type: product.user_type,
                  smsqty: product.smsqty,               
                  price: product.price,
                  validity_date: product.validity_date,
                  invoice_vat: product.invoice_vat,
                  paymentoption: product.paymentoption,
                  paymentoption: $('#paymentoption').val(),
                  paymentby: $('#paymentby').val(),
                  remarks: $('#remarks').val()
                });
                await localStorage.setItem('saleItem', JSON.stringify(productarr));
            });
          }

      });


      $('body').on('click','.smssale',function(){
        let salearr = [];
        let sameprdqty = 0;
        let sameprdprice = 0;
        let f = 0;
        let ctdate = new Date();
        let ctday = ctdate.getDay();
        let hours = ctdate.getHours();
        let minutes = ctdate.getMinutes();
        let rootuser = '{{request()->user()->id}}'

        $.ajax({
          url: '{{route("reseller-sms-balance-check")}}',
          type: 'post',
          data: {
            sms_type: $('#sms_type').val(),
            smsqty: $('#smsqty').val()
          },
          beforeSend: function()
          {
            if ($('#invoice_client').val() == "")
            {
              document.querySelectorAll('#invoice_client')[0].focus();
              document.querySelector('.clienterr').style.display = 'block';
              return false;
            } else {
              document.querySelector('.clienterr').style.display = 'none';
            }

            if ($('#invoice_date').val() == "")
            {
              document.querySelector('#invoice_date').focus();
              document.querySelector('.invoicedateerr').style.display = 'block';
              //$('.invoicedateerr').css({'display':'block'});
              return false;
            } else {
              $('.invoicedateerr').css({'display':'none'});
              document.querySelector('.invoicedateerr').style.display = 'none';
            }

            if ($('#sms_type').val() == "")
            {
              document.querySelector('#sms_type').focus();
              document.querySelector('.smstypeerr').style.display = 'block';
              //$('.invoicedateerr').css({'display':'block'});
              return false;
            } else {
              document.querySelector('.smstypeerr').style.display = 'none';
            }

            if ($('#smsqty').val() == "")
            {
              document.querySelector('#smsqty').focus();
              document.querySelector('.smsqtyerr').style.display = 'block';
              //$('.invoicedateerr').css({'display':'block'});
              return false;
            } else {
              document.querySelector('.smsqtyerr').style.display = 'none';
            }

            if ($('#price').val() == "")
            {
              document.querySelector('#price').focus();
              document.querySelector('.priceerr').style.display = 'block';
              //$('.invoicedateerr').css({'display':'block'});
              return false;
            } else {
              document.querySelector('.priceerr').style.display = 'none';
            }

            if ($('#validity_date').val() == "")
            {
              document.querySelector('#validity_date').focus();
              document.querySelector('.validitydateerr').style.display = 'block';
              //$('.invoicedateerr').css({'display':'block'});
              return false;
            } else {
              document.querySelector('.validitydateerr').style.display = 'none';
            }
          },
          success: function(res)
          {
            document.querySelector('.balancecheckerr').style.display = 'none';
            if ($('#invoice_client').val() == "")
            {
              document.querySelectorAll('#invoice_client')[0].focus();
              document.querySelector('.clienterr').style.display = 'block';
              return false;
            } else {
              document.querySelector('.clienterr').style.display = 'none';
            }

            if ($('#invoice_date').val() == "")
            {
              document.querySelector('#invoice_date').focus();
              document.querySelector('.invoicedateerr').style.display = 'block';
              //$('.invoicedateerr').css({'display':'block'});
              return false;
            } else {
              $('.invoicedateerr').css({'display':'none'});
              document.querySelector('.invoicedateerr').style.display = 'none';
            }

            if ($('#sms_type').val() == "")
            {
              document.querySelector('#sms_type').focus();
              document.querySelector('.smstypeerr').style.display = 'block';
              //$('.invoicedateerr').css({'display':'block'});
              return false;
            } else {
              document.querySelector('.smstypeerr').style.display = 'none';
            }

            if ($('#smsqty').val() == "")
            {
              document.querySelector('#smsqty').focus();
              document.querySelector('.smsqtyerr').style.display = 'block';
              //$('.invoicedateerr').css({'display':'block'});
              return false;
            } else {
              document.querySelector('.smsqtyerr').style.display = 'none';
            }

            if ($('#price').val() == "")
            {
              document.querySelector('#price').focus();
              document.querySelector('.priceerr').style.display = 'block';
              //$('.invoicedateerr').css({'display':'block'});
              return false;
            } else {
              document.querySelector('.priceerr').style.display = 'none';
            }

            if ($('#validity_date').val() == "")
            {
              document.querySelector('#validity_date').focus();
              document.querySelector('.validitydateerr').style.display = 'block';
              //$('.invoicedateerr').css({'display':'block'});
              return false;
            } else {
              document.querySelector('.validitydateerr').style.display = 'none';
            }
            
            if (localStorage.getItem('saleItem')) {
              let storeProduct = localStorage.getItem('saleItem');
              let productInCart = JSON.parse(storeProduct);
              productInCart.forEach(product => {

                if (product.sms_type == $('#sms_type').val() && product.validity_date == $('#validity_date').val())
                {
                  product.smsqty += parseFloat($('#smsqty').val());

                  product.price += parseFloat($('#price').val());
                  f = 1;
                }

              });

              if (f == 0) {
                productInCart.push({
                  client: $('#invoice_client').val(),
                  invoice_date: $('#invoice_date').val(),
                  sms_type: $('#sms_type').val(),
                  user_type: $('#user_type').val(),
                  smsqty: parseFloat($('#smsqty').val()),               
                  price: parseFloat($('#price').val()),
                  validity_date: $('#validity_date').val(),
                  invoice_vat: 0,
                  paymentoption: $('#paymentoption').val(),
                  paymentby: $('#paymentby').val(),
                  remarks: $('#remarks').val()
                });

              }
              localStorage.setItem('saleItem', JSON.stringify(productInCart));
              
            } else {

              salearr.push({
                client: $('#invoice_client').val(),
                invoice_date: $('#invoice_date').val(),
                sms_type: $('#sms_type').val(),
                user_type: $('#user_type').val(),
                smsqty: parseFloat($('#smsqty').val()),               
                price: parseFloat($('#price').val()),
                validity_date: $('#validity_date').val(),
                invoice_vat: 0,
                paymentoption: $('#paymentoption').val(),
                paymentby: $('#paymentby').val(),
                remarks: $('#remarks').val()
              });

              localStorage.setItem('saleItem', JSON.stringify(salearr));
              
            }


            let storeProduct = localStorage.getItem('saleItem');
            let productInCart = JSON.parse(storeProduct);

            $('.carttable tbody').empty();
            let i = 1;
            
            productInCart.forEach((product,index) => {
              invoiceTotal += product.price;
              if (product.invoice_vat != null)
              {
                $('#invoice_vat').val(product.invoice_vat)
                let invoiceVat = ($('#invoice_vat').val()/100*invoiceTotal);
                $('.invoicevat').text(addCommas(invoiceVat.toFixed(2)));
                let grandtotal = addCommas((invoiceTotal-invoiceVat).toFixed(2));
                $('.grandtotal').text(grandtotal);
              } else {
                $('#invoice_vat').val(0)
                let invoiceVat = ($('#invoice_vat').val()/100*invoiceTotal);
                $('.invoicevat').text(addCommas(invoiceVat.toFixed(2)));
                let grandtotal = addCommas((invoiceTotal-invoiceVat).toFixed(2));
                $('.grandtotal').text(grandtotal);
              }
              $('.invoicetotal').text(addCommas(invoiceTotal.toFixed(2)));
              $('.carttable tbody').prepend('<tr>'+
                          '<td>'+i+'</td>'+
                          '<td>'+product.sms_type+'</td>'+
                          '<td>'+product.smsqty+'</td>'+
                          '<td>'+product.price+'</td>'+
                          '<td class="text-center">'+
                              //'<a href="#" class="icon icon-pencil btn edt-currency" data-transid="" data-smstype="'+product.sms_type+'" data-smsqty="'+product.smsqty+'" data-price="'+product.price+'" data-validity_date="'+product.validity_date+'"></a> '+
                              '<a href="#" data-recindex="'+index+'" class="icon icon-trash btn dlt-prdb" data-id=""></a>'+
                          '</td>'+
                      '</tr>');

                      i++;
            });

            $('#smsqty').val('');
            $('#price').val('');
            $('#validity_date').val('');
            $('#paymentoption').val('');
            $('#remarks').val('');
            $('#sms_type').focus();
            
          },
          error: function(err)
          {
            document.querySelector('.balancecheckerr').style.display = 'block';
            document.querySelector('.balancecheckerr').textContent = err.responseJSON.errmsg;
            
            return false;
          }
        });

          
      });



      $('body').on('click','.clearinvoice', function(){
        swal({
          title: "Are you sure?",
          text: "Once deleted, you will not be able to recover this invoice!",
          icon: "warning",
          buttons: true,
        })
        .then((willDelete) => {
          console.log(willDelete);
          if (willDelete) {
            localStorage.removeItem('saleItem');
            swal('Invoice deleted successfully', {
              icon: "success",
            });

            $('.carttable tbody').empty();
            
            $('#invoice_vat').val(0)
            $('.invoicetotal').text('');
            $('.grandtotal').text('');
            invoiceTotal = 0;

          } 
          /*else {
            swal("Your imaginary file is safe!");
          }
          */
        });


      });

      $('body').on('click','.dlt-prdb',function(e){
        e.preventDefault();

        let id = $(this).data('recindex');
        let tr = $(this).closest('tr');
        let storeItem = localStorage.getItem('saleItem');
        let productInCart = JSON.parse(storeItem);

        swal({
          title: "Are you sure?",
          text: "Once deleted, you will not be able to recover this item!",
          icon: "warning",
          buttons: true,
        })
        .then((willDelete) => {
          console.log(willDelete);
          if (willDelete) {
            productInCart.forEach((product,index) => {
              if (id == index)
              {
                productInCart.splice(index,1);
                swal('Cart item deleted successfully', {
                  icon: "success",
                });
                localStorage.setItem('saleItem', JSON.stringify(productInCart));
                
                tr.fadeOut(400,function(){
                    tr.remove();
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

      let savprdarr = [];
      $('body').on('click','.save-invoice', function(){
        let storeItem = localStorage.getItem('saleItem');
        let productInCart = JSON.parse(storeItem);
        savprdarr = [];

        if ($('#invoice_vat').val() == "")
        {
          document.querySelector('#invoice_vat').focus();
          document.querySelector('.vaterr').style.display = 'block';
          //$('.invoicedateerr').css({'display':'block'});
          return false;
        } else {
          document.querySelector('.vaterr').style.display = 'none';
        }

        if ($('#paymentoption').val() == "")
        {
          document.querySelector('#paymentoption').focus();
          document.querySelector('.paymentoptionerr').style.display = 'block';
          //$('.invoicedateerr').css({'display':'block'});
          return false;
        } else {
          document.querySelector('.paymentoptionerr').style.display = 'none';
        }

        if ($('#paymentoption').val() != 'cash')
        {
          document.querySelector('#paymentby').focus();
          document.querySelector('.paymentbyerr').style.display = 'block';
          //$('.invoicedateerr').css({'display':'block'});
          return false;
        } else {
          document.querySelector('.paymentbyerr').style.display = 'none';
        }

        if ($('#remarks').val() == "")
        {
          document.querySelector('#remarks').focus();
          document.querySelector('.remarkserr').style.display = 'block';
          //$('.invoicedateerr').css({'display':'block'});
          return false;
        } else {
          document.querySelector('.remarkserr').style.display = 'none';
        }

        if (localStorage.getItem('saleItem'))
        {
          productInCart.forEach(async product => {
            await savprdarr.push({
                client: product.client,
                invoice_date: product.invoice_date,
                sms_type: product.sms_type,
                user_type: product.user_type,
                smsqty: product.smsqty,               
                price: product.price,
                validity_date: product.validity_date,
                invoice_vat: product.invoice_vat,
                paymentoption: product.paymentoption,
                paymentby: product.paymentby,
                remarks: product.remarks
              });
          });
        }

        if (savprdarr.length > 0) {
          $.ajax({
            url: "{{route('reseller-product-sale-toclient')}}",
            type: 'post',
            data: {
              dataarr: savprdarr
            },
            beforeSend: function(){
              
            },
            success: function(res) {
              document.querySelector('.balancecheckerr').style.display = 'none';
              localStorage.removeItem('saleItem');
              swal(res.msg, {
                icon: "success",
              });

              $('.carttable tbody').empty();
              
              $('#invoice_vat').val(0)
              $('.invoicetotal').text('');
              $('.grandtotal').text('');
              $('#paymentoption').val('');
              $('#paymentby').val('');
              $('#remarks').val('');
              invoiceTotal = 0;
            },
            error: function(err) {
              document.querySelector('.balancecheckerr').style.display = 'block';
              document.querySelector('.balancecheckerr').textContent = err.responseJSON.errmsg;
            }
          })
        }
      });

      /** Root Clients invoice **/

      var resellerClientInvoices = function(){
      $('.rootinvoicelist').DataTable({
          "order": [[ 0, "desc" ]],
          "processing": true,
          "language": {
            processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span> '
          },
          "serverSide": true,
          "ajax": { "url": "show-reseller-client-invoices","type": "get" },
          columnDefs: [
              
            //{ className: "actions", targets: 9  },  
            {
                targets: [ 0 ],
                visible: false,
                searchable: false
            }, 
            {  targets: -1,
              render: function (data, type, full, meta) {
                                      console.log(full);
                                      { return '<a href="#" class="btn btn-sm btn-icon btn-pure btn-default rootuseredtfrm" data-original-title="edit" data-id="'+full[0]+'"><i class="icon icon-pencil" aria-hidden="true"></i></a>\n\
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
              { "sWidth":"60px","sClass": "actions" }
          ],
          oLanguage: {sProcessing: '<div class="loader vertical-align-middle loader-rotate-plane" style="background-color: #2e688e; "></div>'}

        });
      }
      resellerClientInvoices();



     })(document, window, jQuery);
   </script>

@elseif(Auth::guard('web')->check())

<script type="text/javascript">
  (function(document, window, $){

    

    var clientAssignedSenderid = function() {
          $('.clientassignsenderid').DataTable().destroy();
          $('.clientassignsenderid').DataTable( {
          "ajax": {
              "processing": true,
              "url": "{{route('client-senderid-list',[Auth::guard('web')->user()->id])}}",
              "dataType": 'json',
              "type": "get",
              "beforeSend": function (xhr) {
                  }
              },
              "columnDefs":
              [
                {
                  "visible": false,
                  "searchable": false
                },
              ],
              "columns": [
                  { "data": "rowid" },
                  { "data": "senderid" },
                  { "data": "senderid_status" },
                  {
                      "render": function (data, type, full, meta)
                      { 
                          return '<a href="#" class="btn btn-sm btn-icon btn-pure btn-default setclientsenderid" data-userid="'+ full.user_id + '" data-default="'+ full.default +'" data-smssenderid="'+ full.sms_sender_id +'" style="line-height: 27px;">'+full.default+'</a>'; 
                      }
                  },
                  { "data": "created_at" },
              ],
          } );
      }

      clientAssignedSenderid();

      $('.from_date').datepicker({
        format: "yyyy-mm-dd", //"yyyy-mm-dd",
        todayBtn: true, 
        calendarWeeks: true,
        autoclose: true,
        todayHighlight: true, 
        toggleActive: true,
          
      });	

      $('.to_date').datepicker({
        format: "yyyy-mm-dd", //"yyyy-mm-dd",
        todayBtn: true, 
        calendarWeeks: true,
        autoclose: true,
        todayHighlight: true, 
        toggleActive: true,
          
      });	


      var clientSmsSendReport = function() {
          $.ajax({
            url: "{{route('client-total-sms-send')}}",
            type: "get",
            data: {
              fromdate: $('#from_date').val(),
              todate: $('#to_date').val()
            },
            success: function(res) {
              $('.totalsendsms').text(`Total SMS sent: ${res}`);
              $('.totalsendsms').addClass('font-bold');
            }
          });
          $('.clientsmsreport').DataTable().destroy();
          
          $('.clientsmsreport').DataTable( {
          "ajax": {
                "processing": true,
                "url": "{{route('client-sms-send-data')}}",
                "dataType": 'json',
                "type": "post",
                "data": {
                  fromdate: $('#from_date').val(),
                  todate: $('#to_date').val()
                },
                "beforeSend": function (xhr) {
                  
                },
              },
              "columnDefs":
              [
                {
                  "visible": false,
                  "searchable": false
                },
              ],
              "columns": [
                  { "data": "rowid" },
                  { "data": "name" },
                  { "data": "email" },
                  { "data": "senderid" },
                  { "data": "contact" },
                  { "data": "smstype" },
                  { "data": "smscategory" },
                  { "data": "noofsms" },
                  { "data": "sendfrom" },
                  {
                      "render": function (data, type, full, meta)
                      { 
                        if (full.status == "Delivered") {
                          return '<a href="#" class="btn btn-success btn-sm">'+full.status+'</a>'; 
                        } else {
                          return '<a href="#" class="btn btn-danger btn-sm">'+full.status+'</a>'; 
                        }
                      }
                  }
              ],
          } );
      }

      clientSmsSendReport();

      if ($('.dataTables_empty').text() == "Loading...") {
        $('.dataTables_empty').text('No Record Found');
      }

      $('body').on('click','.getclietsmsreport', function(){
        
        clientSmsSendReport();
        if (document.querySelector('table tbody > tr > td').className == "dataTables_empty") {
          
          setTimeout(() => $('.dataTables_empty').text('No Record Found'), 5000);
        }
      });

      var clientFaildSmsSendReport = function() {
          $('.clientfaildsmsreport').DataTable().destroy();
          $('.clientfaildsmsreport').DataTable( {
          "ajax": {
              "processing": true,
              "url": "{{route('client-faild-sms-send-data')}}",
              "dataType": 'json',
              "type": "post",
              "data": {
                fromdate: $('#from_date').val(),
                todate: $('#to_date').val()
              },
              "beforeSend": function (xhr) {
                  }
              },
              "columnDefs":
              [
                {
                  "visible": false,
                  "searchable": false
                },
              ],
              "columns": [
                  { "data": "rowid" },
                  { "data": "name" },
                  { "data": "email" },
                  { "data": "senderid" },
                  { "data": "contact" },
                  { "data": "smstype" },
                  { "data": "smscategory" },
                  { "data": "noofsms" },
                  { "data": "sendfrom" },
                  {
                      "render": function (data, type, full, meta)
                      { 
                        if (full.status == "Delivered") {
                          return '<a href="#" class="btn btn-success btn-icon">'+full.status+'</a>'; 
                        } else {
                          return '<a href="#" class="btn btn-denger btn-icon">'+full.status+'</a>'; 
                        }
                      }
                  }
              ],
          } );
      }

      clientFaildSmsSendReport();


      $('body').on('click','.getclietfaildsmsreport', function(){
        clientFaildSmsSendReport();
      });


      var clientSmsSendConsulateReport = function() {
          $.ajax({
            url: "{{route('client-sms-sent-total-consulate-report')}}",
            type: "get",
            data: {
              fromdate: $('#from_date').val(),
              todate: $('#to_date').val()
            },
            success: function(res) {
              console.log(res);
              $('.totalconsulatesendsms').text(`Total SMS sent: ${res[0].totalsms}`);
              $('.totalconsulatesendsms').addClass('font-bold');
            }
          });
          $('.clientsmscountreport').DataTable().destroy();
          

          $('.clientsmscountreport').DataTable({
            "order": [[ 0, "desc" ]],
            "processing": true,
            "language": {
              processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span> '
            },
            "serverSide": true,
            "ajax": { 
              "url": "{{route('clients-send-sms-consulate-rpt')}}",
              "type": "get",
              "data": {
                  fromdate: $('#from_date').val(),
                  todate: $('#to_date').val()
                },
            },
            columnDefs: [
                
              {
                 targets: [1],
                 render: function(data, type, full, meta) {
                   let campaign = full[1].split("-");
                   return campaign[1];
                 }
              },
              {  targets: -1,
                render: function (data, type, full, meta) {
                    { return '<a href="#" class="btn btn-sm btn-icon btn-pure btn-default text-center campaignview" data-original-title="edit" data-toggle="modal" data-target="#campaigndetailsview" data-campaign="'+full[1]+'"><i class="icon icon-eye" aria-hidden="true"></i></a>'; }
                }   
              },
            ],
            "aoColumns": [
                null,
                null,
                null,
                null,
                { "sWidth":"60px","sClass": "actions" }
            ],
            oLanguage: {sProcessing: '<div class="loader vertical-align-middle loader-rotate-plane" style="background-color: #2e688e; "></div>'}

          });
      }

      clientSmsSendConsulateReport();

      $('body').on('click','.getrootclietcountsmsreport', function(){
        
        clientSmsSendConsulateReport();
        
      });

      $('body').on('click', '.campaignview', function(e){
        e.preventDefault();
        const campaign = $(this).data('campaign');

        $.ajax({
          url: "{{route('campaign-review')}}",
          type: "post",
          data: {
            campaign: campaign
          },
          success: function(res) {
            console.log(res);
          },
          error: function(err) {
            console.log(errr);
          }
        })
      });


      $('body').on('click','.setclientsenderid', function(e){
        e.preventDefault();
        let currentitem = $(this);
        let userid = $(this).data('userid');
        let smssenderid = $(this).data('smssenderid');
        $.ajax({
          url: "{{route('set-default-client-senderid')}}",
          type: "post",
          data: {
            clientid: userid,
            senderid: smssenderid
          },
          success: function(res) {
            console.log(res);
            $('.setclientsenderid').text(0);
            currentitem.text(res);
          },
          error: function(err) {

          }
        })

      })
    
       /** Clients **/
 
       function readUrl(input) {
            if (input.files && input.files[0]) {
                let reader = new FileReader();
                reader.onload = function(e) {
                    $('.nidimg').attr('src', e.target.result);
                }
                
                reader.readAsDataURL(input.files[0]);
            }
        }
        
        
        
        $('.nid').change(function(){
            readUrl(this);
            
        });

        function readApplicationUrl(input) {
            if (input.files && input.files[0]) {
                let reader = new FileReader();
                reader.onload = function(e) {
                    $('.applicationimg').attr('src', e.target.result);
                }
                
                reader.readAsDataURL(input.files[0]);
            }
        }
        
        
        
        $('.application').change(function(){
          readApplicationUrl(this);
            
        });

        function readCustppphotoUrl(input) {
            if (input.files && input.files[0]) {
                let reader = new FileReader();
                reader.onload = function(e) {
                    $('.custppphotoimg').attr('src', e.target.result);
                }
                
                reader.readAsDataURL(input.files[0]);
            }
        }
        
        
        
        $('.custppphoto').change(function(){
          readCustppphotoUrl(this);
            
        });
        
        function readTradeLicencephotoUrl(input) {
            if (input.files && input.files[0]) {
                let reader = new FileReader();
                reader.onload = function(e) {
                    $('.tradelicenceimg').attr('src', e.target.result);
                }
                
                reader.readAsDataURL(input.files[0]);
            }
        }
        
        
        /* Client Document upload */
        $('.tradelicence').change(function(){
          readTradeLicencephotoUrl(this);
            
        });
        
        $('body').on('focus','.nid', function(){
          $(this).removeClass('border-danger')
          $('.nid+.text-danger').hide();
          $('.nid').append('<label class="col-sm-12 font-bold text-success control-label nid-white">[**Doc Type** jpeg, jpg, png|max:200kb]</label>');
        });

        $('body').on('focus','.application', function(){
          $(this).removeClass('border-danger')
          $('.application+.text-danger').hide();
        });

        $('body').on('focus','.custppphoto', function(){
          $(this).removeClass('border-danger')
          $('.custppphoto+.text-danger').hide();
        });

        $('body').on('focus','.tradelicence', function(){
          $(this).removeClass('border-danger')
          $('.tradelicence+.text-danger').hide();
        });


        $('body').on('click','input[name="numbertype"]', function(){
          if ($(this).prop('checked',true))
          {
            switch($(this).val())
            {
              case 'single':
                document.querySelector('.contact_number').style.display = 'block';
                document.querySelector('.contactgroup').style.display = 'none';
                document.querySelector('.contactgroup_file').style.display = 'none';
                break;
              case 'contgroup':
                document.querySelector('.contact_number').style.display = 'none';
                document.querySelector('.contactgroup_file').style.display = 'none';
                document.querySelector('.contactgroup').style.display = 'block';
                break;
              case 'uploadfile':
                document.querySelector('.contactgroup_file').style.display = 'block';
                document.querySelector('.contact_number').style.display = 'none';
                document.querySelector('.contactgroup').style.display = 'none';
                
                break;
            }
          }
        });

        $('body').on('click','input[name="formtype"]', function(){
          if ($(this).prop('checked', true))
          {
            switch($(this).val())
            {
              case 'smsform':
                document.querySelector('.contactuplodfile').style.display = 'none';
                document.querySelector('.messagesendfrm').style.display = 'block';
                break;
              case 'fileuploadform':
                
                document.querySelector('.contactuplodfile').style.display = 'block';
                document.querySelector('.messagesendfrm').style.display = 'none';
                break;

            }
          }
        });

        $('#recipient .count_me').textareaCount({		    
            'maxCharacterSize': 765,
            'textAlign': 'right',
            'warningColor': '#CC3300',  
            'warningNumber': 160,
            'isCharacterCount': true,
            'isWordCount': false, 
            'displayFormat': '#input Characters | #left Characters Left',
            'originalStyle': 'contacts-count',
            'counterCssClass':'#recipient .charleft',
            }, function (data) {
              var parts = 1; 
              var isUnicode = isDoubleByte($('#recipient .count_me').val()); 
              var typeRadio = $('input:radio[name=sms_type]:checked').val();
              var charPerSMS = 160;
              //recipientsmsRadiosText recipientsmsRadiosUnicode

            $('.charleft').css({'width':'auto','float':'right'});

            if(isUnicode)
            {  
              charPerSMS = 70;

              if (data.input > 70) {

                parts = Math.ceil(data.input / 67);

                charPerSMS = 67;

              }

              if(typeRadio=="text")
              {				 

                $("#recipientsmsRadiosText").prop('checked', true);

              }

              $("#recipientsmsRadiosUnicode").prop('checked', true);
            } else {

                var isUnicodeNormal = isDoubleByteNormal($('#recipient .count_me').val());
                if(isUnicodeNormal) {   
                  
                  charPerSMS = 140;

                  if (data.input > 140) {

                    parts = Math.ceil(data.input / 134);

                    charPerSMS = 134;

                  }

                }else{

                  charPerSMS = 160;

                  if (data.input > 160) {

                    parts = Math.ceil(data.input / 153);

                    charPerSMS = 153;

                  }

                }



                if(typeRadio=="unicode") {				 

                  $("#recipientsmsRadiosUnicode").prop('checked', true);

                }

                $("#recipientsmsRadiosText").prop('checked', true);

            }		


              // let userid = "{{Auth::guard('web')->user()->id}}";

              // $.ajax({
              //   url: "{{route('total-sms-on-message-setup')}}",
              //   type: "post",
              //   data: {
              //     userid: userid,
              //     parts: parts
              //   },
              //   success: function(res)
              //   {
              //     console.log(res);
              //   }
              // });
              $('#totalsms').val(parts);
              $('#recipient .parts-count').text('| ' + parts + ' SMS ('+charPerSMS+' Char./SMS)');

          }); 




            function isDoubleByte(str) {

                for (var i = 0, n = str.length; i < n; i++) {

                //if (str.charCodeAt( i ) > 255 && str.charCodeAt( i )!== 8364 ) 

                if (str.charCodeAt( i ) > 255) 

                { return true; }

                }

                return false;

            }	



            function isDoubleByteNormal(str) {

                for (var i = 0, n = str.length; i < n; i++) {

                    if (str.charCodeAt( i ) ==91
                      || str.charCodeAt( i ) ==92
                      || str.charCodeAt( i ) ==93
                      || str.charCodeAt( i ) ==94
                      || str.charCodeAt( i ) ==123
                      || str.charCodeAt( i ) ==124
                      || str.charCodeAt( i ) ==125
                      || str.charCodeAt( i ) ==126
                    ) { return true; }

                }

                return false;

            }

            $("#file").fileinput({ 

                uploadUrl: '#', // you must set a valid URL here else you will get an error

                allowedFileExtensions : ['txt','xls','xlsx','csv'],



                maxFileSize: 5000,

                maxFilesNum: 1,

                uploadAsync: false,



                overwriteInitial: true,'showPreview' : false,'showUpload' : false,'showRemove' : true,'showCaption' : true,		

            });



            $("#file").on("filepredelete", function(jqXHR) {

                var abort = true;

                if (confirm("Are you sure you want to delete this image?")) {

                abort = false;

                }

                return abort;

            });


            /** contact group **/
 
            var rendergrouplist = function(){
            $('.grouplist').DataTable().destroy();
            $('.activedeal').css({'background-color':'#3e8ef7','color':'#fff'});    
            $('.grouplist').DataTable({
                "order": [[ 0, "desc" ]],
                "processing": true,
                "language": {
                  processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span> '
                },
        
                "serverSide": true,
                "ajax": { "url": "render-groups","type": "get" },
                columnDefs: [
                  {
                    targets: [3],
                    render: function (data, type, full, meta) {
                      if (full[3] == 1) {
                        return 'Published'
                      } else {
                        return 'Unpublished'
                      }
                    }
                  },
                  {  targets: -1,
                    render: function (data, type, full, meta) {
                                            { return '<a href="#" class="btn btn-sm btn-icon btn-pure btn-default contactgroupedtfrm" data-toggle="modal" data-target="#addContactGroup" data-original-title="edit" data-id="'+full[0]+'" data-groupname="'+full[1]+'" data-status="'+full[3]+'"><i class="icon icon-pencil" aria-hidden="true"></i></a>\n\
                                      <a href="#" class="btn btn-sm btn-icon btn-pure btn-default contactgroupdtl" data-toggle="tooltip" data-original-title="Remove" data-id="'+full[0]+'"><i class="icon icon-trash" aria-hidden="true"></i></a>'; }
                    }   
                  },
                ],
                "aoColumns": [
                    null,
                    null,
                    null,
                    null,
                    { "sWidth":"60px","sClass": "actions" }
                ],
                oLanguage: {sProcessing: '<div class="loader vertical-align-middle loader-rotate-plane" style="background-color: #2e688e; "></div>'}
      
              });
            }
            rendergrouplist();

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
                    },3000);
                    rendergrouplist();
                  },
                  error: function(err) {
                    $('.notinsrecord').text(err.responseJSON.errmsg);
                    $('.notinsrecord').fadeIn();
                    setTimeout(function(){
                      $('.notinsrecord').fadeOut();
                    },3000);
                  }
                })
            });

            $('body').on('click','.contactgroupedtfrm', function(){
              let id = $(this).data('id');
              let groupname = $(this).data('groupname');
              let status = $(this).data('status')
              $('#id').val(id);
              $('#group_name').val(groupname);
              if (status == 1)
              {
                $('#senderid_status_yes').prop('checked',true);
              }

              if (status == 0)
              {
                $('#senderid_status_no').prop('checked',true);
              }
              $('#frmmode').val('edt');
              $('.contactgrouphead').text('Edit New Contact Group')
            });

            $('#addContactGroup').on('hidden.bs.modal', function(){
              $('#id').val('');
              $('#group_name').val('');
              $('#frmmode').val('ins');
              $('.contactgrouphead').text('Add New Contact Group')
            });


            $('body').on('click','.contactgroupdtl', function(){
              let id = $(this).data('id');
              let tr = $(this).closest('tr');
              swal({
                title: "Are you sure?",
                text: "Once deleted, you will not be able to recover this record!",
                icon: "warning",
                buttons: true,
              })
              .then((willDelete) => {
                if (willDelete) {
                  $.ajax({
                    url: "{{route('delete-group')}}",
                    type: 'post',
                    data: {
                      id: id
                    },
                    success: function(res){
                      console.log(res);
                      swal(res.msg, {
                        icon: "success",
                      });
                      tr.fadeOut(400,function(){
                          tr.remove();
                      });
                    },
                    error: function(err){
                      console.log(err.responseJSON.errmsg);
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


            /** contact group **/
 
            var rendercontactingroup = function(){
            $('.contactlist').DataTable().destroy();
            $('.activedeal').css({'background-color':'#3e8ef7','color':'#fff'});    
            $('.contactlist').DataTable({
                "order": [[ 0, "desc" ]],
                "processing": true,
                "language": {
                  processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span> '
                },
        
                "serverSide": true,
                "ajax": { "url": "render-contactin-groups","type": "get" },
                columnDefs: [
                  {
                    targets: [3],
                    render: function (data, type, full, meta) {
                      if (full[3] == 1) {
                        return 'Published'
                      } else {
                        return 'Unpublished'
                      }
                    }
                  },
                  {  targets: -1,
                    render: function (data, type, full, meta) {
                                            { return '<a href="#" class="btn btn-sm btn-icon btn-pure btn-default contactgroupedtfrm" data-toggle="modal" data-target="#addContactInGroup" data-original-title="edit" data-id="'+full[0]+'" data-groupname="'+full[1]+'"><i class="icon icon-pencil" aria-hidden="true"></i></a>\n\
                                      <a href="#" class="btn btn-sm btn-icon btn-pure btn-default contactgroupdtl" data-toggle="tooltip" data-original-title="Remove" data-id="'+full[0]+'"><i class="icon icon-trash" aria-hidden="true"></i></a>'; }
                    }   
                  },
                ],
                "aoColumns": [
                    null,
                    null,
                    null,
                    null,
                    { "sWidth":"60px","sClass": "actions" }
                ],
                oLanguage: {sProcessing: '<div class="loader vertical-align-middle loader-rotate-plane" style="background-color: #2e688e; "></div>'}
      
              });
            }
            rendercontactingroup();

            $('body').on('click','input[name="contactformtype"]', function(){
              document.querySelector('#contact_number').value = '';
              document.querySelector('#contact_name').value = '';
              document.querySelector('#email').value = '';
              document.querySelector('#gender').value = '';
              document.querySelector('#dob').value = '';
              $('#contactgroup').val('').trigger('chosen:updated');
              if($(this).val() == 'single')
              {
                document.querySelector('.singlenumber').style.display = 'block';
                document.querySelector('.multiplenumber').style.display = 'none';
              }

              if($(this).val() == 'multiple')
              {
                document.querySelector('.singlenumber').style.display = 'none';
                document.querySelector('.multiplenumber').style.display = 'block';
              }
            });

            //create-contacts

            $('body').on('click','.btncontactingroup',function(e){
              e.preventDefault();
              let form = $('#contactingroupfrm')[0];
              let form_data = new FormData(form);
              let contactformtype = document.querySelectorAll('input[name="contactformtype"]');
              let formtype = '';
              let data = {};
              let isValidate = true;
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

                    if ($('#contactgroup').val() == null)
                    {
                      alert("Contact group can't left empty");
                      isValidate = false;
                      $('#contactgroup').focus();
                      return false;
                    }
                    form_data.append('contact_number', document.querySelector('#contact_number').value);
                    form_data.append('contact_name', document.querySelector('#contact_name').value);
                    form_data.append('email', document.querySelector('#email').value);
                    form_data.append('gender', document.querySelector('#gender').value);
                    form_data.append('dob', document.querySelector('#dob').value);
                  }

                  if (formtype == 'multiple')
                  {
                    if ($('#contactgroup').val() == null)
                    {
                      alert("Contact group can't left empty");
                      isValidate = false;
                      $('#contactgroup').focus()
                      return false;
                    }

                    if ($('#file').val() == "")
                    {
                      alert("File can't left empty");
                      isValidate = false;
                      $('#file').focus();
                      return false;
                    }
                    document.querySelector('#contact_number').value = '';
                    document.querySelector('#contact_name').value = '';
                    document.querySelector('#email').value = '';
                    document.querySelector('#gender').value = '';
                    document.querySelector('#dob').value = '';
                  }
                }
              });

              $.ajax({
                url: 'create-contacts',
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
                  rendercontactingroup();
                  setTimeout(function(){
                    $('.insrecord').fadeOut();
                    document.querySelector('#contact_number').value = '';
                    document.querySelector('#contact_name').value = '';
                    document.querySelector('#email').value = '';
                    document.querySelector('#gender').value = '';
                    document.querySelector('#dob').value = '';
                    document.querySelector('#file').value = '';
                    $('.file-caption-name').children().text('');
                    $('#contactgroup').val('').trigger('chosen:updated');
                    $('#id').val('');
                    $('#frmmode').val('ins');
                  },3000);
                  
                },
                error: function(err) {
                  $('.notinsrecord').text(err.responseJSON.errmsg);
                  $('.notinsrecord').fadeIn();
                  setTimeout(function(){
                    $('.notinsrecord').fadeOut();
                  },3000);
                }
              });
            });
        

            $('#dob').datepicker({
              format: "yyyy-mm-dd", //"yyyy-mm-dd",
              todayBtn: true, 
              calendarWeeks: true,
              autoclose: true,
              todayHighlight: true, 
              toggleActive: true,
                
            });	


            $('body').on('change', '#senderid', function(){
                let senderid = $(this).val();

                $.ajax({
                  url: "{{route('senderid-type')}}",
                  type: "post",
                  data: {
                    senderid: senderid
                  },
                  success: function(res)
                  {
                    $('#sms_category').val(res);
                  },
                  error: function(err)
                  {

                  }
                });
            });

            $('body').on('change','#smssent_contactgroup', function(){
              $.ajax({
                //url: "{{route('total-contacts-ina-group')}}",
                url: "{{route('valid-mobile-by-prefix')}}",
                type: "post",
                data: {
                  contactgroup: $(this).val()
                },
                success: function(res)
                {
                  console.log(res);
                  let totalcontact = 0;
                  if (Array.isArray(res))
                  {
                    res.forEach(contact => {
                        totalcontact += 1;
                    });
                  }

                  $('#total_contacts').val(totalcontact);
                },
                error: function(err)
                {
                  if (Array.isArray(err.responseJSON.errmsg) && ! err.responseJSON.errmsg.length > 0)
                  {
                    $('#total_contacts').val(0)
                  }
                }
              });
            });


            $('body').on('click','input[name="numbertype"]', function(){
              if ($(this).val() == 'contgroup')
              {
                $('.setupsmsform').text('Setup Bulk Sms');
              } else {
                $('.setupsmsform').text('SMS Form');
              }
            });

            $('body').on('click', '.setsmssend', function(){
              let form = $('#smssendfrm')[0];
              let form_data = new FormData(form);
              let numbertype = '';
              
              if ($('#numbertypesingle').prop('checked') == true)
              {
                numbertype = $('#numbertypesingle').val();
              }

              if ($('#numbertypegroup').prop('checked') == true)
              {
                numbertype = $('#numbertypegroup').val();
              }

              if ($('#numbertypeupload').prop('checked') == true)
              {
                numbertype = $('#numbertypeupload').val();
              }

              form_data.append('cam_name',$('#cam_name').val());
              form_data.append('senderid',$('#senderid').val());
              form_data.append('numbertype',numbertype);
              form_data.append('contact_number',$('#contact_number').val());
              form_data.append('contactgroup[]',$('#smssent_contactgroup').val());
              form_data.append('message',$('#msgcontent').val());
              form_data.append('target_time',$('input[name="target_time"]').val());
              form_data.append('file',$('input[type=file]')[0].files[0]);

              let url = "";

              if ($('#send_later_checkbox').prop('checked') == true)
              {
                url = "{{route('set-schedule-sms-messages')}}";
              } else {
                url ="{{route('manage-sms-messages')}}";
              }
              $.ajax({
                url: url,
                type: "post",
                processData: false,
                contentType: false,
                data: form_data,
                beforeSend: function()
                {
                  
                  if($('#senderid').val() == "")
                  {
                    alert("Sender ID can't left empty");
                    $('#senderid').focus();
                    return false;
                  }
                  if ($('#numbertypesingle').prop('checked') == true)
                  {
                    if($('#contact_number').val() == "")
                    {
                      alert("Contact number can't left empty");
                      $('#contact_number').focus();
                      return false;
                    }
                  }
                  if ($('#numbertypegroup').prop('checked') == true)
                  {
                    if($('#smssent_contactgroup').val() == null)
                    {
                      alert("Contact group can't left empty");
                      $('#smssent_contactgroup').focus();
                      return false;
                    }
                  }

                  if ($('#numbertypeupload').prop('checked') == true)
                  {
                    if($('#file12').val() == null)
                    {
                      alert("Contact file can't left empty");
                      $('#file12').focus();
                      return false;
                    }
                  }

                  if($('#msgcontent').val() == "")
                  {
                    alert("Sms content can't left empty");
                    $('#msgcontent').focus();
                    return false;
                  }


                  $('.setupsmsroot').css({'display':'block'});
                  if (numbertype == 'single')
                  {
                    $('.setupsms').text('Single sms Processing....');
                  }

                  if (numbertype == 'contgroup')
                  {
                    $('.setupsms').text('Bulk sms processing....');
                  }
                },
                success: function(res)
                {
                  if (numbertype == 'single')
                  {
                    $('#contact_number').val('');
                    $('#msgcontent').val('');
                    $('.setupsms').css({'color':'green'}).text(res.msg);
                    $('#numbertypesingle').prop('checked',true);
                    $('#numbertypeupload').prop('checked',false);
                    $('#numbertypegroup').prop('checked',false);
                    document.querySelector('.contact_number').style.display = 'block';
                    document.querySelector('.contactgroup').style.display = 'none';
                  }

                  if (numbertype == 'contgroup')
                  {
                    $('#smssent_contactgroup').val('').trigger('chosen:updated');
                    $('#numbertypesingle').prop('checked',false);
                    $('#numbertypeupload').prop('checked',false);
                    $('#numbertypegroup').prop('checked',true);
                    $('#msgcontent').val('');
                    $('.setupsms').css({'color':'green'}).text(res.msg);
                    document.querySelector('.contact_number').style.display = 'none';
                    document.querySelector('.contactgroup').style.display = 'block';
                  }

                  if (numbertype == 'uploadfile')
                  {
                    $('#numbertypesingle').prop('checked',false);
                    $('#numbertypegroup').prop('checked',false);
                    $('#numbertypeupload').prop('checked',true);
                    $('#msgcontent').val('');
                    $('.setupsms').css({'color':'green'}).text(res.msg);
                    document.querySelector('.contact_number').style.display = 'none';
                    document.querySelector('.contactgroup').style.display = 'none';
                    document.querySelector('.contactgroup_file').style.display = 'block';
                  }

                  $('#numbertypesingle').prop('checked',true);
                  $('#numbertypegroup').prop('checked',false);
                  $('#numbertypeupload').prop('checked',false);
                  $('#msgcontent').val('');
                  if(res.nomask)
                  {
                    $('.setupsms').css({'color':'green'}).text(res.msg);
                    $('.nonmaskbal').text(res.nomask)

                  }
                  if(res.mask) {

                    $('.setupsms').css({'color':'green'}).text(res.msg);
                    $('.maskbal').text(res.mask)

                  }

                  if(res.voice) {

                    $('.setupsms').css({'color':'green'}).text(res.msg);
                    $('.voicebal').text(res.voice)

                  }
                  document.querySelector('.contact_number').style.display = 'block';
                  document.querySelector('.contactgroup').style.display = 'none';
                  document.querySelector('.contactgroup_file').style.display = 'none';

                  form_data.append('numbertype',numbertype);
                  form_data.append('contact_number',$('#contact_number').val(''));
                  form_data.append('contactgroup[]',$('#smssent_contactgroup').val('').trigger('chosen:updated'));
                  form_data.append('message',$('#msgcontent').val(''));
                  form_data.append('file',$('input[type=file]')[0].files[0]);

                  setTimeout(function(){
                    $('.setupsmsroot').css({'display':'none'});
                    $('#contact_number').focus();
                  },2000);
                },
                error: function(err)
                {
                  $('.setupsms').css({'color':'red','text-align':'center'}).text(err.responseJSON.errmsg);
                  setTimeout(function(){
                    $('.setupsmsroot').css({'display':'none'});
                    $('#contact_number').focus();
                    $('.setupsms').css({'color':'green','text-align':'center'}).text('');
                  },3000);
                }
              });
            });


            /** campaing list **/
 
            var rendercampainglist = function(){
            $('.campainglist').DataTable().destroy();
            $('.activedeal').css({'background-color':'#3e8ef7','color':'#fff'});    
            $('.campainglist').DataTable({
                "order": [[ 0, "desc" ]],
                "processing": true,
                "language": {
                  processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span> '
                },
        
                "serverSide": true,
                "ajax": { "url": "render-campaing","type": "get" },
                columnDefs: [
                 
                  {
                      targets: [ 10 ],
                      visible: false,
                      searchable: false
                  },
                  {
                      targets: [ 11 ],
                      visible: false,
                      searchable: false
                  },
                  //{ width: '20%', targets: [4] },
                  {
                    targets: [8],
                    render: function (data, type, full, meta) {
                      if (full[6] == 1) {
                        return 'Delivered'
                      } else {
                        return 'Pending'
                      }
                    }
                  },
                  {  targets: -1,
                    render: function (data, type, full, meta) {
                                            { return '<a href="#" class="btn btn-sm btn-icon btn-pure btn-default sendpendingsms" data-toggle="modal" data-target="#sendpendingsms" data-original-title="edit" data-campaing="'+full[0]+'" data-userid="'+full[8]+'" data-senderid="'+full[9]+'"><i class="icon-paper-plane" aria-hidden="true"></i></a>'; }
                    }   
                  },
                ],
                //"autoWidth": false,
                //fixedColumns: true,
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
                    { "sWidth":"60px","sClass": "actions" }
                ],
                oLanguage: {sProcessing: '<div class="loader vertical-align-middle loader-rotate-plane" style="background-color: #2e688e; "></div>'}
      
              });
            }
            rendercampainglist();

            $('body').on('click','.sendpendingsms', function(){
              let campaing = $(this).data('campaing');
              let senderid = $(this).data('senderid');

              $.ajax({
                url: "{{route('send-pending-sms')}}",
                type: "post",
                data: {
                  campaing: campaing,
                  senderid: senderid
                },
                success: function(res)
                {
                  console.log(res);
                },
                erros: function(err)
                {
                  console.log(err);
                }
              });
            });

     })(document, window, jQuery);
   </script>
   

@endif

<script type="text/javascript">
  $(document).ready(function(){
      var url = window.location.pathname;
      var baseUrl = 'http://login.smsinbd.com/';
      var filename = url.substring(url.lastIndexOf('/')+1);
      let currentroute = '{{Route::currentRouteName()}}';
      let currentuser = '{{Auth::user()->id}}';
      if(currentroute == 'client-settings') {
        filename = `${currentroute}/${currentuser}/${filename}`;
      }
      $('.nav.nav-sub.dk').each(function(){
          var pchild = $(this).children().find("a[href='"+baseUrl+filename+"']").attr('href'); 
          
          $(this).children().find("a[href='"+baseUrl+filename+"']").parent().css({'background-color': '#222222'})
          $(this).children().find("a[href='"+baseUrl+filename+"']").parent().parent().parent().addClass('active');
          
      });

      setTimeout(function(){
        $('.clientsuccess').fadeOut();
      },3000);


      $('input[type="checkbox"]').on('click', function(){
        if ($(this).is(":checked")) {
          $(this).val('y');
        } else {
          $(this).val('n');
        }
      });

      
      /** Control root tab */

      let tabbtn = localStorage.getItem('tab');

      if (tabbtn == '#tab_1') {
        $('.nav-tabs a[data-target="#tab_2"]').parent().removeClass('active');
        $('.nav-tabs a[data-target="#tab_2"]').attr('aria-expanded','false');
        
        $('.nav-tabs a[data-target="#tab_3"]').parent().removeClass('active');
        $('.nav-tabs a[data-target="#tab_3"]').attr('aria-expanded','false');

        $('.nav-tabs a[data-target="#tab_1"]').parent().addClass('active');
        $('.nav-tabs a[data-target="#tab_1"]').attr('aria-expanded','true');

        $('#tab_1').addClass('active');
        $('#tab_2').removeClass('active');
        $('#tab_3').removeClass('active');
      }

      if (tabbtn == '#tab_2') {
        $('.nav-tabs a[data-target="#tab_1"]').parent().removeClass('active');
        $('.nav-tabs a[data-target="#tab_1"]').attr('aria-expanded','false');

        $('.nav-tabs a[data-target="#tab_2"]').parent().addClass('active');
        $('.nav-tabs a[data-target="#tab_2"]').attr('aria-expanded','true');
        
        $('.nav-tabs a[data-target="#tab_3"]').parent().removeClass('active');
        $('.nav-tabs a[data-target="#tab_3"]').attr('aria-expanded','false');
        
        $('#tab_2').addClass('active');
        $('#tab_1').removeClass('active');
        $('#tab_3').removeClass('active');
      }


      if (tabbtn == '#tab_3') {
        $('.nav-tabs a[data-target="#tab_1"]').parent().removeClass('active');
        $('.nav-tabs a[data-target="#tab_1"]').attr('aria-expanded','false');
        
        $('.nav-tabs a[data-target="#tab_2"]').parent().removeClass('active');
        $('.nav-tabs a[data-target="#tab_2"]').attr('aria-expanded','false');

        $('.nav-tabs a[data-target="#tab_3"]').parent().addClass('active');
        $('.nav-tabs a[data-target="#tab_3"]').attr('aria-expanded','true');
        
        $('#tab_3').addClass('active');
        $('#tab_2').removeClass('active');
        $('#tab_1').removeClass('active');
      }

      $('.nav-tabs a[data-target="#tab_1"]').click(function(){
        localStorage.setItem('tab','#tab_1');
      });

      $('.nav-tabs a[data-target="#tab_2"]').click(function(){
        localStorage.setItem('tab','#tab_2');
      });

      $('.nav-tabs a[data-target="#tab_3"]').click(function(){
        localStorage.setItem('tab','#tab_3');
      });

      /**jquery choosen value from database */
      /**
       * $('#autoship_option')
    .find('option:first-child').prop('selected', true)
    .end().trigger('chosen:updated');
       */
        
      $('.date-timepicker').datetimepicker({format:'Y-MM-D H:m:s'});
      
  })
</script>

{{-- End Root User JS --}}

</body>
</html>
