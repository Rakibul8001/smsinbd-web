<!DOCTYPE html>
<html lang="en" class="">
<head>
  <meta charset="utf-8" />
  <title>
    @if(Auth::guard('root')->check())
      SMS Admin Panel ||  login.bdlists.com
    @elseif(Auth::guard('manager')->check())
      Support Staff SMS Panel ||  smsinbd.com 
    @elseif(Auth::guard('reseller')->check())
      Reseller SMS Panel ||  smsinbd.com 
    @else
      SMS Panel ||  smsinbd.com 
    @endif  
  </title>
  <meta name="description" content="Professional SMS Broadcast Panel. smsinbd.com is a project of Data Host IT" />
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <link rel="stylesheet" href="{{ asset('libs/assets/animate.css/animate.css') }}" type="text/css" />
  <link rel="stylesheet" href="{{ asset('libs/assets/font-awesome/css/font-awesome.min.css') }}" type="text/css" />
  <link rel="stylesheet" href="{{ asset('libs/assets/simple-line-icons/css/simple-line-icons.css') }}" type="text/css" />
  <link rel="stylesheet" href="{{ asset('libs/jquery/bootstrap/dist/css/bootstrap.css') }}" type="text/css" />

  <link rel="stylesheet" href="{{ asset('smsapp/css/font.css') }}" type="text/css" />
  <link rel="stylesheet" href="{{ asset('smsapp/css/app.css') }}" type="text/css" />
  <link rel="stylesheet" href="{{ asset('libs/assets/all/jquery.dataTables.min.css') }}" />
  <link rel="stylesheet" href="{{ asset('libs/assets/datatable/dataTables.bootstrap.css') }}"/>
  <link rel="stylesheet" href="{{ asset('libs/assets/datatable/dataTables.responsive.css') }}"/>
  <link rel="stylesheet" href="{{ asset('libs/assets/bootstrap-datepicker3.css') }}" type="text/css" />
  <link rel="stylesheet" href="{{ asset('smsapp/css/fileinput.css') }}" type="text/css" />
  <!-- <link rel="stylesheet" href="asset('libs/jquery/bootstrap/dist/css/bootstrap-datetimepicker.min.css')" type="text/css" /> -->
  <link href="{{ asset('libs/assets/select2/select2.min.css') }}" rel="stylesheet" />
  <link href="{{ asset('libs/assets/jquery-confirm/jquery-confirm.min.css') }}" rel="stylesheet" />

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

    .doresellerverify {
      cursor: pointer;
    }

    .doresellerstatus {
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

  .dataTables_wrapper .dataTables_paginate .paginate_button {
    padding: 0;
  }

  </style>

  <!-- scripts -->
  <script src="{{ asset('libs/jquery/jquery/dist/jquery.js') }}"></script>
<script src="{{ asset('libs/jquery/bootstrap/dist/js/bootstrap.js') }}"></script>
<script src="{{ asset('libs/assets/jquery-confirm/jquery-confirm.min.js') }}"></script>
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
    <div class="currentrout" data-current_route="{{Route::currentRouteName()}}"></div>
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

<script src="{{ asset('smsapp/js/ui-load.js') }}"></script>
<script src="{{ asset('smsapp/js/ui-jp.config.js') }}"></script>
<script src="{{ asset('smsapp/js/ui-jp.js') }}"></script>
<script src="{{ asset('smsapp/js/ui-nav.js') }}"></script>
<script src="{{ asset('smsapp/js/ui-toggle.js') }}"></script>
<script src="{{ asset('smsapp/js/ui-client.js') }}"></script>

<script src="{{ asset('libs/assets/all/jquery.dataTables.min.js') }}"></script>

<script type="text/javascript" language="javascript" src="{{ asset('libs/assets/all/dataTables.responsive.js') }}"></script>
<script type="text/javascript" language="javascript" src="{{ asset('libs/assets/all/dataTables.bootstrap.js') }}"></script>
<script type="text/javascript" language="javascript" src="{{ asset('libs/assets/all/dataTables.rowGroup.min.js') }}"></script>
<script type="text/javascript" language="javascript" src="{{ asset('libs/assets/datatable/dataTables.fixedColumns.min.js') }}"></script>
<script src="{{ asset('libs/assets/all/jquery.validate.min.js') }}"></script>
<script src="{{ asset('libs/assets/all/sweetalert.min.js') }}"></script>

<script src="{{ asset('libs/jquery/bootstrap-datepicker/bootstrap-datepicker.js')}}" type="text/javascript"></script>
<script src="{{ asset('smsapp/js/jquery.textareaCounter.plugin.js') }}"></script>
<script src="{{ asset('smsapp/js/fileinput.min.js') }}"></script>
<script src="{{ asset('smsapp/js/moment.min.js') }}"></script>
<script src="{{ asset('smsapp/js/bootstrap-datetimepicker.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('libs/assets/all/loader.js') }}"></script>

<script src="{{ asset('libs/assets/select2/select2.min.js') }}"></script>




@yield('scripts')



<script>
  
  //-----------------------------modify by rrr
      
      $(document).ready(function() {
          $('.select2').select2();
      });
     
      
  //-----------------------------modify end

</script>

<script type="text/javascript">
      

      let profileindex = "{{Route::currentRouteName()}}";
      let userid = null;
      let $charurl = '';
      if (profileindex == "client-profile-index" || profileindex == "myprofile-index") {
        $charurl = '/client-profile-total-sms/'+userid;
      } else if(profileindex == "reseller-client-profile-index") {
        $charurl = '/reseller-client-profile-total-sms/'+userid;
      } else {
        $charurl = '/reseller-profile-total-sms/'+userid;
      }

      console.log('Chart Url', $charurl);
      if (profileindex == "client-profile-index" || profileindex == "myprofile-index" || profileindex == "reseller-profile-index" || profileindex == "reseller-client-profile-index") {
          google.charts.load('current', {'packages':['bar']});
          google.charts.setOnLoadCallback(drawChart);

          let totalsms = [];
          
          function drawChart() {
            fetch($charurl)
            .then(res => {
              res.json().then(data => {
                var data = google.visualization.arrayToDataTable([
                  ['Month', 'Sms Sent'],
                  ...data.totalsms
                ]);

                var options = {
                  chart: {
                    title: 'Client Sms Sent History',
                    subtitle: 'Monthly Sms Sent in current year',
                  },
                  bars: 'vertical' // Required for Material Bar Charts.
                };

                var chart = new google.charts.Bar(document.getElementById('barchart_material'));

                chart.draw(data, google.charts.Bar.convertOptions(options));
              })
            })
          }
      }
    </script>
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


      var staffActivity = function(){
      //$('.rootuser').DataTable().destroy();
      $('.activedeal').css({'background-color':'#3e8ef7','color':'#fff'});    
      $('.staffactivity').DataTable({
          "order": [[ 0, "desc" ]],
          "lengthMenu": [[5, 10, 100, -1], [5, 10, 100, "All"]],
          "processing": true,
          "language": {
            processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span> '
          },
 
          "serverSide": true,
          "ajax": { "url": "staff-activity","type": "get" },
          columnDefs: [
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
                targets: [ 6 ],
                visible: false,
                searchable: false
            },   
            {  targets: 3,
              render: function (data, type, full, meta) {
                    { return '<div>'+full[3]+'<br/><span style="font-size:12px;font-style:italic;font-weight:bold;">'+full[6]+'</span></div>'; }
              }   
            },
            {  targets: 5,
              render: function (data, type, full, meta) {
                                      { return '<a href="#" class="btn btn-sm btn-icon btn-pure btn-default rootuseredtfrm" data-original-title="edit" data-id="'+full[5]+'">'+full[0]+'</a>'; }
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
          ],
          oLanguage: {sProcessing: '<div class="loader vertical-align-middle loader-rotate-plane" style="background-color: #2e688e; "></div>'}

        });
      }
      staffActivity();



      var staffInvoiceActivity = function(){
      //$('.rootuser').DataTable().destroy();
      $('.activedeal').css({'background-color':'#3e8ef7','color':'#fff'});    
      $('.staffactivity_invoice').DataTable({
          "order": [[ 0, "desc" ]],
          "lengthMenu": [[5, 10, 100, -1], [5, 10, 100, "All"]],
          "processing": true,
          "language": {
            processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span> '
          },
 
          "serverSide": true,
          "ajax": { "url": "staff-invoice-activity","type": "get" },
          columnDefs: [
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
                targets: [ 7 ],
                visible: false,
                searchable: false
            },   
            {  targets: 3,
              render: function (data, type, full, meta) {
                    { return '<div>'+full[3]+'<br/><span style="font-size:12px;font-style:italic;font-weight:bold;">'+full[7]+'</span></div>'; }
              }   
            },
            {  targets: 5,
              render: function (data, type, full, meta) {
                                      { return '<a href="#" class="btn btn-sm btn-icon btn-pure btn-default rootuseredtfrm" data-original-title="edit" data-id="'+full[0]+'">'+full[5]+'</a>'; }
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
          ],
          oLanguage: {sProcessing: '<div class="loader vertical-align-middle loader-rotate-plane" style="background-color: #2e688e; "></div>'}

        });
      }
      staffInvoiceActivity();

   


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
                  return full[13]+' / '+full[14]+' / '+full[15];
                }
            },
            {  targets: -1,
              render: function (data, type, full, meta) {
                let id = full[0];
                let name = full[1];
                let mobile = full[4];
                $.ajax({
                  url: "{{route('client-assigned-senderids')}}",
                  type: "post",
                  data: {
                    clientid: id
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
                                      { return '<a href="client-profile/'+full[0]+'/index" class="btn btn-sm btn-icon btn-pure btn-default rootuseredtfrm" data-original-title="edit" data-id="'+full[0]+'"><i class="icon icon-pencil" aria-hidden="true"></i></a>\n\
                                        <a href="#" class="btn btn-sm btn-icon btn-pure btn-default viewclientassignedsenderid totalsenderid'+full[0]+'" data-toggle="modal" data-target="#resellerassignedsenders" data-original-title="edit" data-id="'+full[0]+'" data-name="'+full[1]+'" data-mobile="'+full[4]+'"></a>\n\
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
              url: "{{route('client.balance')}}",
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

        $('body').on('click','.save-invoice', function(){
            let storeItem = localStorage.getItem('saleItem');
            let productInCart = JSON.parse(storeItem);
            savprdarr = [];

            if ($('#invoice_vat').val() == "" && $('#paymentoption').val() == 'cash')
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

            /*if ($('#paymentoption').val() != 'cash')
            {
              document.querySelector('#paymentby').focus();
              document.querySelector('.paymentbyerr').style.display = 'block';
              return false;
            } else {
              document.querySelector('.paymentbyerr').style.display = 'none';
            }*/

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
                    rate: product.rate,               
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

      

      
      
      $('body').on('click','.viewclientassignedsenderid', function(e){
        e.preventDefault();
        let id = $(this).data('id');
        let name = $(this).data('name');
        let mobile = $(this).data('mobile');
        $.ajax({
          url: "{{route('client-assigned-senderids')}}",
          type: "post",
          data: {
            clientid: id
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
              "order": [[ 0, "desc" ]],
              "processing": true,
              "language": {
                processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span> '
              },
              "serverSide": true,
              "ajax": {
                "url": "{{route('root-clients-sms-send-data')}}",
                "type": "get",
                "data": {
                  fromdate: $('#from_date').val(),
                  todate: $('#to_date').val(),
                  userid: $('#userid').val()
                },
                "beforeSend": function (xhr) {
                  
                },
              },
              columnDefs:
              [ 
                {
                targets: [0],
                    visible: false,
                    searchable: false
                },
                {
                targets: [3],
                    visible: false,
                    searchable: false
                },
                {
                targets: [12],
                    visible: false,
                    searchable: false
                },
                {
                    targets: [5],
                    render: function(data, type, full, meta) {
                      return '<a href="#" class="viewsmsdetails" style="color:blue;" data-toggle="modal" data-target="#viewsmsdetails" data-submittedat="'+full[10]+'" data-contact="'+full[5]+'" data-remarks="'+full[1]+'" data-content="'+full[12]+'">'+full[5]+'</a>';
                    }
                },
                {
                    targets: [11],
                    render: function(data, type, full, meta) {
                        if (full[11] == 1)
                        {
                          return '<a href="#" class="btn btn-success btn-sm">Delivered</a>'; 
                        } else {
                          return '<a href="#" class="btn btn-danger btn-sm">Failed</a>';
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
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                //{ "sWidth":"60px","sClass": "actions" }
            ],
            oLanguage: {sProcessing: '<div class="loader vertical-align-middle loader-rotate-plane" style="background-color: #2e688e; "></div>'}
          } );
      }

      clientSmsSendReport();


      var clientCampaignSmsSendReport = function() {
          $.ajax({
            url: "{{route('root-client-total-sms-send')}}",
            type: "get",
            data: {
              fromdate: $('#from_date').val(),
              todate: $('#to_date').val(),
              userid: $('#userid').val()
            },
            success: function(res) {
              $('.totalsendsms').css({'margin-top':'20px'});
              $('.totalsendsms').text(`Total SMS sent: ${res}`);
              $('.totalsendsms').addClass('font-bold');
            }
          });

          $.ajax({
            url: "{{route('root-client-total-sms-campaign')}}",
            type: "get",
            data: {
              fromdate: $('#from_date').val(),
              todate: $('#to_date').val(),
              userid: $('#userid').val()
            },
            success: function(res) {
              $('.totalcampaign').css({'margin-right':'20px','margin-top':'20px'});
              $('.totalcampaign').text(`Total Campaign: ${res}`+'  ');
              $('.totalcampaign').addClass('font-bold');
            }
          });
          
      }

      clientCampaignSmsSendReport();



      var clientArchiveCampaignSmsSendReport = function() {
          $.ajax({
            url: "{{route('root-client-archive-total-sms-send')}}",
            type: "get",
            data: {
              fromdate: $('#from_date').val(),
              todate: $('#to_date').val(),
              userid: $('#userid').val()
            },
            success: function(res) {
              $('.totalsendsmsarchive').css({'margin-top':'20px'});
              $('.totalsendsmsarchive').text(`Total SMS sent: ${res}`);
              $('.totalsendsmsarchive').addClass('font-bold');
            }
          });

          $.ajax({
            url: "{{route('root-client-archive-total-sms-campaign')}}",
            type: "get",
            data: {
              fromdate: $('#from_date').val(),
              todate: $('#to_date').val(),
              userid: $('#userid').val()
            },
            success: function(res) {
              $('.totalcampaignarchive').css({'margin-right':'20px','margin-top':'20px'});
              $('.totalcampaignarchive').text(`Total Campaign: ${res}`+'  ');
              $('.totalcampaignarchive').addClass('font-bold');
            }
          });
         
      }

      clientArchiveCampaignSmsSendReport();

      /*$('body').on('click','.exportexcel', function(e){
          let remarks = $(this).data('remarks');

          $.ajax({
            url: "{{route('export-excel')}}",
            type: "get",
            data: {
              remarks: remarks
            },
            success: function(res) {
              console.log(res);
            },
            error: function(err){
              console.log(err);
            }
          });
      });*/

    $('.gatewayerrors').DataTable().destroy();
    $('.gatewayerrors').DataTable({
      "order": [[ 0, "desc" ]],
      "processing": true,
        "language": {
          processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span> '
        },

        "serverSide": true,
        "ajax": { "url": "{{route('render-gateway-error')}}","type": "get" },
        columnDefs: [
            
          //{ className: "actions", targets: 9  },  
          {
              targets: [ 0 ],
              visible: false,
              searchable: false
          }, 
      ],
      "aoColumns": [
            null,
            null,
            null,
            null,
            null,
        ],
        oLanguage: {sProcessing: '<div class="loader vertical-align-middle loader-rotate-plane" style="background-color: #2e688e; "></div>'}

    });

    $('body').on('click','.campaignstatus', function(e){
        let remarks = $(this).data('remarks');
          $('.campaignmobile').DataTable().destroy();
          $('.campaignmobile').DataTable( {
          "order": [[ 0, "desc" ]],
          "ajax": {
              "processing": true,
              "url": "{{route('root-client-campaign-mobile-list')}}",
              "dataType": 'json',
              "type": "post",
              "data": {
                  remarks: remarks
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
                
                {
                  targets: [7],
                  render: function (data, type, full, meta) {
                    if (full.status == '1') {
                          return '<a href="#" class="btn btn-success btn-sm">Delivered</a>'; 
                        } else {
                          return '<a href="#" class="btn btn-danger btn-sm">Failed</a>';
                        }
                  }
                },
              ],
              fixedColumns: true,
              "columns": [
                  { "data": "rowid" },
                  { "data": "senderid" },
                  { "data": "tonumber" },
                  { "data": "smscount" },
                  { "data": "smscontent" },
                  { "data": "submittedat" },
                  { "data": "sms_type" },
                  { "data": "status" },
              ]
          } );
     });

     $('body').on('click','.archivecampaignstatus', function(e){
        let remarks = $(this).data('remarks');
          $('.campaignmobile').DataTable().destroy();
          $('.campaignmobile').DataTable( {
          "order": [[ 0, "desc" ]],
          "ajax": {
              "processing": true,
              "url": "{{route('root-client-archive-campaign-mobile-list')}}",
              "dataType": 'json',
              "type": "post",
              "data": {
                  remarks: remarks
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
                
                {
                  targets: [7],
                  render: function (data, type, full, meta) {
                    if (full.status == '1') {
                          return '<a href="#" class="btn btn-success btn-sm">Delivered</a>'; 
                        } else {
                          return '<a href="#" class="btn btn-danger btn-sm">Failed</a>';
                        }
                  }
                },
              ],
              fixedColumns: true,
              "columns": [
                  { "data": "rowid" },
                  { "data": "senderid" },
                  { "data": "tonumber" },
                  { "data": "smscount" },
                  { "data": "smscontent" },
                  { "data": "submittedat" },
                  { "data": "sms_type" },
                  { "data": "status" },
              ]
          } );
     });


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

      // if ($('.dataTables_empty').text() == "Loading...") {
      //   $('.dataTables_empty').text('No Record Found');
      // }

      $('body').on('click','.getclietArchiveCampaignsmsreport', function(){
        
        clientArchiveCampaignSmsSendReport();
        /*if (document.querySelector('table tbody > tr > td').className == "dataTables_empty") {
          
          setTimeout(() => $('.dataTables_empty').text('No Record Found'), 5000);
        }
        */
      });

      $('body').on('click','.getclietCampaignsmsreport', function(){
        
        clientCampaignSmsSendReport();
        /*if (document.querySelector('table tbody > tr > td').className == "dataTables_empty") {
          
          setTimeout(() => $('.dataTables_empty').text('No Record Found'), 5000);
        }
        */
      });

      $('body').on('click','.getclietsmsreport', function(){
        
        clientSmsSendReport();
        /*if (document.querySelector('table tbody > tr > td').className == "dataTables_empty") {
          
          setTimeout(() => $('.dataTables_empty').text('No Record Found'), 5000);
        }
        */
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

      

      

      $('body').on('click','.operatoredtfrm', function(){
        let id = $(this).data('id');
        let operator_name = $(this).data('operatorname');
        let type = $(this).data('type');
        let operator_prefix = $(this).data('operatorprefix');
        let single_url = $(this).data('single_url');
        let multi_url = $(this).data('multi_url');
        let delivery_url = $(this).data('delivery_url');
        let status = $(this).data('status');

        $('#smsoperatoredit #operator_id').val(id);
        $('#smsoperatoredit #name').val(operator_name);

        $('input:radio[name="type"][value="'+type+'"]').prop('checked', true);
        $('#smsoperatoredit #prefix').val(operator_prefix);
        $('#smsoperatoredit #single_url').val(single_url);
        $('#smsoperatoredit #multi_url').val(multi_url);
        $('#smsoperatoredit #delivery_url').val(delivery_url);
        
        if (status == '1'){
          $('#smsoperatoredit #active').val(status);
          $('#smsoperatoredit #active').prop('checked',true);
        }

      });

      /* ----- Operator end ------*/

      /**-------Gateway Start------ */


      $('body').on('click','.operatorapifrm', function(){
        let gatewayid = $(this).data('id');
        let gatewayoperator = $(this).data('operator');
        let gatewayname = $(this).data('gatewayname');
        let gatewayuser = $(this).data('gatewayuser');
        let gatewaypassword = $(this).data('gatewaypassword');
        let gatewaystatus = $(this).data('gatewaystatus');

        $('#smsoperatorapi #gateway_id').val(gatewayid);
        $('#smsoperatorapi #operator_id').val(gatewayoperator).change();
        $('#smsoperatorapi #gateway_name').val(gatewayname);
        $('#smsoperatorapi #user').val(gatewayuser);
        $('#smsoperatorapi #password').val(gatewaypassword);
        if (gatewaystatus == '1'){
          $('#smsoperatorapi #active').val(gatewaystatus);
          $('#smsoperatorapi #active').prop('checked', true);
          
        } else {
          $('#smsoperatorapi #active').prop('checked', false);
        }

        
        
        
      });

      /**-------Gateway End------- */

      


      @if(Route::currentRouteName() == 'reseller-profile-senderid')
          var renderResellerSenderId = function() {
              $('#resellerassignedsmssender').DataTable().destroy();
              $('#resellerassignedsmssender').DataTable( {
              "order": [[ 0, "desc" ]],  
              "ajax": {
                  "processing": true,
                  "url": "{{route('rander-sms-senderid-for-reseller',[@$request->userid])}}",
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
                      { "data": "created_by" },
                  ]
              } );
          }

          renderResellerSenderId();

          //jquery keyup delay until stop tipping

          $('.tableprofilesenderid tbody').empty();
          let getsenderid = [];
          let searchsenderid = $('.searchsenderid').val();
          let current_profileid = document.querySelector('.searchsenderid')
          current_profileid = current_profileid.getAttribute('data-current_profileid');
          $.ajax({
            url: "{{route('search-unassigned-reseller-senderid')}}",
            type: "post",
            data: {
              search: searchsenderid,
              userid: current_profileid
            },
            success: function(res) {
              
              $.each(res.data, (index, senderid) => {
                  $('.tableprofilesenderid tbody').append(
                    '<tr>'+
                      '<td><input type="checkbox" name="sms_sender_id[]" value="'+senderid.id+'" /></td>'+
                      '<td>'+senderid.sender_name+'</td>'+
                    '</tr>'
                  );
              })
            },
            error: function(err) {
              console.log(err);
            }
          });

          var delay = (function(){
              var timer = 0;
              return function(callback, ms){
                  clearTimeout (timer);
                  timer = setTimeout(callback, ms);
              };
          })();
          $('body').on('keyup','.searchsenderid', function(){
            delay(() =>{
                $('.tableprofilesenderid tbody').empty();
                let getsenderid = [];
                let searchsenderid = $(this).val();
                let current_profileid = document.querySelector('.searchsenderid')
                current_profileid = current_profileid.getAttribute('data-current_profileid');
                $.ajax({
                  url: "{{route('search-unassigned-reseller-senderid')}}",
                  type: "post",
                  data: {
                    search: searchsenderid,
                    userid: current_profileid
                  },
                  success: function(res) {
                    
                    $.each(res.data, (index, senderid) => {
                        $('.tableprofilesenderid tbody').append(
                          '<tr>'+
                            '<td><input type="checkbox" name="sms_sender_id[]" value="'+senderid.id+'" /></td>'+
                            '<td>'+senderid.sender_name+'</td>'+
                          '</tr>'
                        );
                    })
                  },
                  error: function(err) {
                    console.log(err);
                  }
                });
            },1000);
            
          
          });

      @endif

      @if(Route::currentRouteName() == 'reseller-profile-invoice')

          /** reseller's Clients invoice **/

          var resellerMyInvoicesList = function(){
          $('.resellermyinvoicelist').DataTable({
              "order": [[ 0, "desc" ]],
              "processing": true,
              "language": {
                processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span> '
              },
              "serverSide": true,
              "ajax": { "url": "{{route('show-my-reseller-invoicelist',[@$request->userid])}}","type": "get" },
              columnDefs: [
                  
                //{ className: "actions", targets: 9  },  
                {
                    targets: [ 0 ],
                    visible: false,
                    searchable: false
                }, 
                {
                    targets: [ 1 ],
                    visible: false,
                    searchable: false
                }, 
                {
                    targets: [ 12 ],
                    visible: false,
                    searchable: false
                }, 
                {
                    targets: [ 13 ],
                    visible: false,
                    searchable: false
                }, 
                {  targets: 4,
                  render: function (data, type, full, meta) {
                                { return `<div>${full[4]}</div><div style="font-weight:900; font-style:italic; font-size: 11px;">Sms Type ${full[13]}</div>`; }
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
          resellerMyInvoicesList();
      @endif



      let rs = 1;
      $('body').on('click','.addrotation_associate_senderid', function(){
        
        $('#rotation_associate_senderid_parent').append(
              '<tr id="rotation_associate_senderid'+rs+'">'+
                                
              '<td><input type="text" name="associate_sender_id[]"  value="" id="associate_sender_id'+rs+'" class="form-control" placeholder="Enter associate sender id"></td>'+
              '<td>'+
               ' <select name="gateway[]" id="gateway'+rs+'" class="form-control">'+
               '   <option selected value="">Select gateway</option>'+
                  @if(!empty(@$gateways))
                    @foreach($gateways as $gateway)
                      '<option value="{{$gateway->id}}">{{$gateway->gateway_name}}</option>'+
                    @endforeach
                  @endif
                '</select>'+
              '</td>'+
              '<td>'+
                  '<select name="sender_status[]" id="sender_status'+rs+'" class="form-control">'+
                      '<option>Select</option>'+
                      '<option value="yes">Yes</option>'+
                      '<option value="no">No</option>'+
                  '</select>'+
              '</td>'+
              '<td>'+
                  '<a class="btn btn-primary btn-md mb-5 addrotation_associate_senderid" style="margin-bottom:10px; z-index: 99; position:relative;"><i class="fa fa-plus"></i> Add New</a> '+
                  '<a class="btn btn-primary btn-md mb-5 deleterotation_associate_senderid" data-recordid="rotation_associate_senderid'+rs+'" style="margin-bottom:10px; z-index: 99; position:relative;"><i class="fa fa-minus"></i> Delete</a>'+
              '</td>'+
            '</tr>'
        );
        rs++;
      });

      $('body').on('click', '.deleterotation_associate_senderid', function(){
        let recordid = $(this).data('recordid');
        $('#'+recordid).remove();
      });



      let rss = 1;
      $('body').on('click','.addrotation_associate_add_senderid', function(){
        
        $('#rotation_associate_edit_senderid_parent').append(
              '<tr id="rotation_associate_edit_senderid'+rss+'">'+
                                
              '<td><input type="text" name="associate_sender_id[]"  value="" id="associate_sender_id'+rss+'" class="form-control" placeholder="Enter associate sender id"></td>'+
              '<td>'+
               ' <select name="gateway[]" id="gateway'+rss+'" class="form-control">'+
               '   <option selected value="">Select gateway</option>'+
                  @if(!empty(@$gateways))
                    @foreach($gateways as $gateway)
                      '<option value="{{$gateway->id}}">{{$gateway->gateway_name}}</option>'+
                    @endforeach
                  @endif
                '</select>'+
              '</td>'+
              '<td>'+
                  '<select name="sender_status[]" id="sender_status'+rss+'" class="form-control">'+
                      '<option>Select</option>'+
                      '<option value="yes">Yes</option>'+
                      '<option value="no">No</option>'+
                  '</select>'+
              '</td>'+
              '<td>'+
                  '<a class="btn btn-primary btn-md mb-5 addrotation_associate_add_senderid" style="margin-bottom:10px; z-index: 99; position:relative;"><i class="fa fa-plus"></i> Add New</a> '+
                  '<a class="btn btn-primary btn-md mb-5 deleterotation_associate_add_senderid" data-recordid="rotation_associate_edit_senderid'+rss+'" style="margin-bottom:10px; z-index: 99; position:relative;"><i class="fa fa-minus"></i> Delete</a>'+
              '</td>'+
            '</tr>'
        );
        rss++;
      });

      $('body').on('click', '.deleterotation_associate_add_senderid', function(){
        let recordid = $(this).data('recordid');
        $('#'+recordid).remove();
      });


      

      let trs = 1;
      $('body').on('click','.addmultiple_rotation_associate_senderid', function(){
        
        $('#multiple_rotation_associate_senderid_parent').append(
              '<tr id="multiple_rotation_associate_senderid0'+trs+'">'+
                                
              '<td><input type="text" name="associate_sender_id[]"  value="" id="associate_sender_id'+trs+'" class="form-control" placeholder="Enter associate sender id"></td>'+
              '<td>'+
               ' <select name="template[]" id="template'+trs+'" class="form-control">'+
               '   <option selected value="">Select template</option>'+
                  @if(!empty(@$templates))
                    @foreach($templates as $template)
                      '<option value="{{$template->id}}">{{$template->template_title}}</option>'+
                    @endforeach
                  @endif
                '</select>'+
              '</td>'+
              '<td>'+
               ' <select name="gateway[]" id="gateway'+trs+'" class="form-control">'+
               '   <option selected value="">Select gateway</option>'+
                  @if(!empty(@$gateways))
                    @foreach($gateways as $gateway)
                      '<option value="{{$gateway->id}}">{{$gateway->gateway_name}}</option>'+
                    @endforeach
                  @endif
                '</select>'+
              '</td>'+
              '<td>'+
                  '<select name="sender_status[]" id="sender_status'+trs+'" class="form-control">'+
                      '<option>Select</option>'+
                      '<option value="yes">Yes</option>'+
                      '<option value="no">No</option>'+
                  '</select>'+
              '</td>'+
              '<td>'+
                  '<a class="btn btn-primary btn-md mb-5 addmultiple_rotation_associate_senderid" style="margin-bottom:10px; z-index: 99; position:relative;"><i class="fa fa-plus"></i> Add New</a> '+
                  '<a class="btn btn-primary btn-md mb-5 multiple_deleterotation_associate_senderid" data-recordid="multiple_rotation_associate_senderid0'+trs+'" style="margin-bottom:10px; z-index: 99; position:relative;"><i class="fa fa-minus"></i> Delete</a>'+
              '</td>'+
            '</tr>'
        );
        trs++;
      });

      $('body').on('click', '.multiple_deleterotation_associate_senderid', function(){
        let recordid = $(this).data('recordid');
        $('#'+recordid).remove();
      });



      let trss = 1;
      $('body').on('click','.multiple_addrotation_associate_add_senderid', function(){
        
        $('#multiple_rotation_associate_edit_senderid_parent').append(
              '<tr id="multiple_rotation_associate_edit_senderid0'+trss+'">'+
                                
              '<td><input type="text" name="associate_sender_id[]"  value="" id="associate_sender_id'+trss+'" class="form-control" placeholder="Enter associate sender id"></td>'+
              '<td>'+
               ' <select name="template[]" id="template'+trss+'" class="form-control">'+
               '   <option selected value="">Select gateway</option>'+
                  @if(!empty(@$templates))
                    @foreach($templates as $template)
                      '<option value="{{$template->id}}">{{$template->template_title}}</option>'+
                    @endforeach
                  @endif
                '</select>'+
              '</td>'+
              '<td>'+
               ' <select name="gateway[]" id="gateway'+trss+'" class="form-control">'+
               '   <option selected value="">Select gateway</option>'+
                  @if(!empty(@$gateways))
                    @foreach($gateways as $gateway)
                      '<option value="{{$gateway->id}}">{{$gateway->gateway_name}}</option>'+
                    @endforeach
                  @endif
                '</select>'+
              '</td>'+
              '<td>'+
                  '<select name="sender_status[]" id="sender_status'+trss+'" class="form-control">'+
                      '<option>Select</option>'+
                      '<option value="yes">Yes</option>'+
                      '<option value="no">No</option>'+
                  '</select>'+
              '</td>'+
              '<td>'+
                  '<a class="btn btn-primary btn-md mb-5 multiple_addrotation_associate_add_senderid" style="margin-bottom:10px; z-index: 99; position:relative;"><i class="fa fa-plus"></i> Add New</a> '+
                  '<a class="btn btn-primary btn-md mb-5 multiple_deleterotation_associate_add_senderid" data-recordid="multiple_rotation_associate_edit_senderid0'+trss+'" style="margin-bottom:10px; z-index: 99; position:relative;"><i class="fa fa-minus"></i> Delete</a>'+
              '</td>'+
            '</tr>'
        );
        trss++;
      });

      $('body').on('click', '.multiple_deleterotation_associate_add_senderid', function(){
        let recordid = $(this).data('recordid');
        $('#'+recordid).remove();
      });


      
      $('body').on('click','.multiplerotationsenderidedtfrm', function(){
        $('#multiple_rotation_associate_senderid_parent').empty();
        let id = $(this).data('id');
        let gatewayinfo = $(this).data('gatewayinfo');
        let sendername = $(this).data('sendername');
        let status = $(this).data('status');
        let recdefault = $(this).data('default');
        let operatorid = $(this).data('operatorid');
        let talitalkuser = $(this).data('user');
        let talitalkpassword = $(this).data('password');

        console.log("Sender ",gatewayinfo);
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
          let rs = 0;
          if (rs == 0) {
            if (gatewayinfo == null) {
                $('#multiple_rotation_associate_senderid_parent').append(
                  '<tr id="multiple_rotation_associate_senderid'+rs+'">'+
                                    
                  '<td><input type="text" name="associate_sender_id[]"  value="" id="associate_sender_id'+rs+'" class="form-control" placeholder="Enter associate sender id"></td>'+
                  '<td>'+
                  ' <select name="template[]" id="template'+rs+'" class="form-control">'+
                  '   <option selected value="">Select template</option>'+
                      @if(!empty(@$templates))
                        @foreach($templates as $template)
                          '<option value="{{$template->id}}">{{$template->template_title}}</option>'+
                        @endforeach
                      @endif
                    '</select>'+
                  '</td>'+
                  '<td>'+
                  ' <select name="gateway[]" id="gateway'+rs+'" class="form-control">'+
                  '   <option selected value="">Select gateway</option>'+
                      @if(!empty(@$gateways))
                        @foreach($gateways as $gateway)
                          '<option value="{{$gateway->id}}">{{$gateway->gateway_name}}</option>'+
                        @endforeach
                      @endif
                    '</select>'+
                  '</td>'+
                  '<td>'+
                      '<select name="sender_status[]" id="sender_status'+rs+'" class="form-control">'+
                          '<option>Select</option>'+
                          '<option value="yes">Yes</option>'+
                          '<option value="no">No</option>'+
                      '</select>'+
                  '</td>'+
                  '<td>'+
                      '<a class="btn btn-primary btn-md mb-5 addmultiple_rotation_associate_senderid" style="margin-bottom:10px; z-index: 99; position:relative;"><i class="fa fa-plus"></i> Add New</a> '+
                      '<a class="btn btn-primary btn-md mb-5 multiple_deleterotation_associate_senderid" data-recordid="multiple_rotation_associate_senderid'+rs+'" style="margin-bottom:10px; z-index: 99; position:relative;"><i class="fa fa-minus"></i> Delete</a>'+
                  '</td>'+
                '</tr>'
                );
            }
          }
          if (gatewayinfo != "null") {
              gatewayinfo.forEach(function(sender){
                hasnumber = parseInt(sender.associate_sender_id);

                if (sender.associate_sender_id != null)
                {
                  $('#multiple_rotation_associate_senderid_parent').append(
                        '<tr id="multiple_rotation_associate_senderid'+rs+'">'+
                                          
                        '<td><input type="text" name="associate_sender_id[]"  value="" id="associate_sender_id'+rs+'" class="form-control" placeholder="Enter associate sender id"></td>'+
                        '<td>'+
                        ' <select name="template[]" id="template'+rs+'" class="form-control">'+
                        '   <option selected value="">Select template</option>'+
                            @if(!empty(@$templates))
                              @foreach($templates as $template)
                                '<option value="{{$template->id}}">{{$template->template_title}}</option>'+
                              @endforeach
                            @endif
                          '</select>'+
                        '</td>'+
                        '<td>'+
                        ' <select name="gateway[]" id="gateway'+rs+'" class="form-control">'+
                        '   <option selected value="">Select gateway</option>'+
                            @if(!empty(@$gateways))
                              @foreach($gateways as $gateway)
                                '<option value="{{$gateway->id}}">{{$gateway->gateway_name}}</option>'+
                              @endforeach
                            @endif
                          '</select>'+
                        '</td>'+
                        '<td>'+
                            '<select name="sender_status[]" id="sender_status'+rs+'" class="form-control">'+
                                '<option>Select</option>'+
                                '<option value="yes">Yes</option>'+
                                '<option value="no">No</option>'+
                            '</select>'+
                        '</td>'+
                        '<td>'+
                            '<a class="btn btn-primary btn-md mb-5 addmultiple_rotation_associate_senderid" style="margin-bottom:10px; z-index: 99; position:relative;"><i class="fa fa-plus"></i> Add New</a> '+
                            '<a class="btn btn-primary btn-md mb-5 multiple_deleterotation_associate_senderid" data-recordid="multiple_rotation_associate_senderid'+rs+'" style="margin-bottom:10px; z-index: 99; position:relative;"><i class="fa fa-minus"></i> Delete</a>'+
                        '</td>'+
                      '</tr>'
                  );
                  
                    
                    
                    
                    
                    if (sender.associate_sender_id.indexOf('\"') >= 0) {
                      $('#associate_sender_id'+rs).val(sender.associate_sender_id.replace(/\\\"+/g, ' '));
                      $('#gateway'+rs).val(sender.associate_gateway);
                      $('#template'+rs).val(sender.template);
                      $('#sender_status'+rs).val(sender.status);
                    } else {
                      $('#associate_sender_id'+rs).val(sender.associate_sender_id);
                      $('#gateway'+rs).val(sender.associate_gateway);
                      $('#template'+rs).val(sender.template);
                      $('#sender_status'+rs).val(sender.status);
                    }
                }

                rs++;
              });
          }

          
          if (status == 1) {
            $('#multiple_template_senderid_status_yes').prop("checked", true);
            
          } else {
            $('#multiple_template_senderid_status_no').prop("checked", true);
          }
          
          if (recdefault == 1) {
            $('#multiple_template_senderid_default_yes').prop("checked", true);
          } else {
            $('#multiple_template_senderid_default_no').prop("checked", true);
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


      /**End Multiple rotation senderid */


      

      


      /**Template */
      var rootTemplate = function(){
      $('.managetemplate').DataTable().destroy();
      $('.activedeal').css({'background-color':'#3e8ef7','color':'#fff'});    
      $('.managetemplate').DataTable({
          "order": [[ 0, "desc" ]],
          "processing": true,
          "language": {
            processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span> '
          },
 
          "serverSide": true,
          "ajax": { "url": "rander-root-template","type": "get" },
          columnDefs: [
                  {
                      targets: [8],
                      visible: false,
                      searchable: false
                  },
                  {
                      targets: [9],
                      visible: false,
                      searchable: false
                  },
                  {
                      targets: [2],
                      render: function (data, type, full, meta) {
                        {
                          return `<div>
                              ${full[2]}
                              <div style="font-style: italic; font-weight: 900; font-size:10px;">BTRC File: <a href="https://login.smsinbd.com/storage/app/public/templateContent/${full[8]}">${full[8]}</a></div>
                              <div style="font-style: italic; font-weight: 900; font-size:10px;">BTRC File Status: <a href="/approve-btrc-file/${full[0]}">${full[9] == 1 ? "true" : "false" }</a></div>
                          </div>`
                        }
                      }
                  },
                  {  targets: -1,
                    render: function (data, type, full, meta) {
                          {
                            return '<a href="#" class="btn btn-sm btn-icon btn-pure btn-default templateeditform"  data-toggle="modal" data-target="#smsRootTemplate" data-original-title="edit" data-id="'+full[0]+'" data-templatetitle="'+full[1]+'" data-templatedesc="'+full[2]+'" data-templateowner="'+full[3]+'" data-usertype="'+full[4]+'" data-status="'+full[5]+'"><i class="icon icon-pencil" aria-hidden="true"></i></a>';

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
                    null,
                    null,
                    null,
                    null,
                    { "sWidth":"60px","sClass": "actions" }
                ],
          oLanguage: {sProcessing: '<div class="loader vertical-align-middle loader-rotate-plane" style="background-color: #2e688e; "></div>'}

        });
      }
      rootTemplate();

      $('body').on('click','.templateeditform', function(){
        let id = $(this).data('id');
        let templatetitle = $(this).data('templatetitle');
        let templatedesc = $(this).data('templatedesc');
        let templateowner = $(this).data('templateowner');
        let usertype = $(this).data('usertype');
        let status = $(this).data('status');
        $('#exampleModalLabel').text('Edit User Template');
        $('#template_title').val(templatetitle);
        $('#id').val(id);
        $('#frmmode').val('edt');
        $('#template_desc').val(templatedesc)
        if (status == 'Active') {
          $('#senderid_status_yes').prop('checked',true);
          $('#senderid_status_no').prop('checked',false);
        }

        if (status == 'Inactive') {
          $('#senderid_status_no').prop('checked',true);
          $('#senderid_status_yes').prop('checked',false);
          
        }
      });


      $('#smsRootTemplate').on('hidden.bs.modal', function(){
        $('#exampleModalLabel').text('Add User Template');
        $('#frmmode').val('ins');
        $('#id').val('');
        $('#template_title').val('');
        $('#template_desc').val('');
        $('#senderid_status_no').prop('checked',false);
        $('#senderid_status_yes').prop('checked',false);
      });


      

      /** End Template */




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

      //$('#smsSenderIdAddForm').modal('refresh');

      // $('#smsSenderIdAddForm').on('hidden.bs.modal', function(){
      //   $(".modal-body").html("");
      // });
      $('body').on('click','.rotationsenderidedtfrm', function(){
        $('#rotation_associate_senderid_parent').empty();
        let id = $(this).data('id');
        let gatewayinfo = $(this).data('gatewayinfo');
        let sendername = $(this).data('sendername');
        let status = $(this).data('status');
        let recdefault = $(this).data('default');
        let operatorid = $(this).data('operatorid');
        let talitalkuser = $(this).data('user');
        let talitalkpassword = $(this).data('password');

        console.log("Sender ",gatewayinfo);
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
          let rs = 0;
          if (rs == 0) {
            if (gatewayinfo == null) {
                $('#rotation_associate_senderid_parent').append(
                  '<tr id="rotation_associate_senderid'+rs+'">'+
                                    
                  '<td><input type="text" name="associate_sender_id[]"  value="" id="associate_sender_id'+rs+'" class="form-control" placeholder="Enter associate sender id"></td>'+
                  '<td>'+
                  ' <select name="gateway[]" id="gateway'+rs+'" class="form-control">'+
                  '   <option selected value="">Select gateway</option>'+
                      @if(!empty(@$gateways))
                        @foreach($gateways as $gateway)
                          '<option value="{{$gateway->id}}">{{$gateway->gateway_name}}</option>'+
                        @endforeach
                      @endif
                    '</select>'+
                  '</td>'+
                  '<td>'+
                      '<select name="sender_status[]" id="sender_status'+rs+'" class="form-control">'+
                          '<option>Select</option>'+
                          '<option value="yes">Yes</option>'+
                          '<option value="no">No</option>'+
                      '</select>'+
                  '</td>'+
                  '<td>'+
                      '<a class="btn btn-primary btn-md mb-5 addrotation_associate_senderid" style="margin-bottom:10px; z-index: 99; position:relative;"><i class="fa fa-plus"></i> Add New</a> '+
                      '<a class="btn btn-primary btn-md mb-5 deleterotation_associate_senderid" data-recordid="rotation_associate_senderid'+rs+'" style="margin-bottom:10px; z-index: 99; position:relative;"><i class="fa fa-minus"></i> Delete</a>'+
                  '</td>'+
                '</tr>'
                );
            }
          }
          if (gatewayinfo != "null") {
              gatewayinfo.forEach(function(sender){
                hasnumber = parseInt(sender.associate_sender_id);

                if (sender.associate_sender_id != null)
                {
                  $('#rotation_associate_senderid_parent').append(
                        '<tr id="rotation_associate_senderid'+rs+'">'+
                                          
                        '<td><input type="text" name="associate_sender_id[]"  value="" id="associate_sender_id'+rs+'" class="form-control" placeholder="Enter associate sender id"></td>'+
                        '<td>'+
                        ' <select name="gateway[]" id="gateway'+rs+'" class="form-control">'+
                        '   <option selected value="">Select gateway</option>'+
                            @if(!empty(@$gateways))
                              @foreach($gateways as $gateway)
                                '<option value="{{$gateway->id}}">{{$gateway->gateway_name}}</option>'+
                              @endforeach
                            @endif
                          '</select>'+
                        '</td>'+
                        '<td>'+
                            '<select name="sender_status[]" id="sender_status'+rs+'" class="form-control">'+
                                '<option>Select</option>'+
                                '<option value="yes">Yes</option>'+
                                '<option value="no">No</option>'+
                            '</select>'+
                        '</td>'+
                        '<td>'+
                            '<a class="btn btn-primary btn-md mb-5 addrotation_associate_senderid" style="margin-bottom:10px; z-index: 99; position:relative;"><i class="fa fa-plus"></i> Add New</a> '+
                            '<a class="btn btn-primary btn-md mb-5 deleterotation_associate_senderid" data-recordid="rotation_associate_senderid'+rs+'" style="margin-bottom:10px; z-index: 99; position:relative;"><i class="fa fa-minus"></i> Delete</a>'+
                        '</td>'+
                      '</tr>'
                  );
                  
                    
                    
                    
                    
                    if (sender.associate_sender_id.indexOf('\"') >= 0) {
                      $('#associate_sender_id'+rs).val(sender.associate_sender_id.replace(/\\\"+/g, ' '));
                      $('#gateway'+rs).val(sender.associate_gateway);
                      $('#sender_status'+rs).val(sender.status);
                    } else {
                      $('#associate_sender_id'+rs).val(sender.associate_sender_id);
                      $('#gateway'+rs).val(sender.associate_gateway);
                      $('#sender_status'+rs).val(sender.status);
                    }
                }

                rs++;
              });
          }

          
          if (status == 1) {
            $('#rotation_senderid_status_yes').prop("checked", true);
            
          } else {
            $('#rotation_senderid_status_no').prop("checked", true);
          }
          
          if (recdefault == 1) {
            $('#rotation_senderid_default_yes').prop("checked", true);
          } else {
            $('#rotation_senderid_default_no').prop("checked", true);
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

      

      @if(isset($request->userid))

      $('.doverify').on('click', function(){
      
        let verified = $(this).data('verified');


        //verified = $('.doverify').attr('verified', !verified);

        $.ajax({
          url: "{{route('root-client-decument-verify',['userid' => $request->userid])}}",
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
          url: "{{route('root-client-status',['userid' => $request->userid])}}",
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


      $('.doresellerverify').on('click', function(){
      
      let verified = $(this).data('verified');


      //verified = $('.doverify').attr('verified', !verified);

      $.ajax({
        url: "{{route('root-reseller-decument-verify',['userid' => $request->userid])}}",
        method: 'post',

        success: function(r) {
          
          
          
          if (r.msg == 0) {
              $('.doresellerverify').text('Verified: No');
              $('.doresellerverify').addClass('text-danger');
              $('.doresellerverify').removeClass('text-success');
              $('.doresellerverify').attr('data-verified', 1);
          }

          if (r.msg == 1) {
              $('.doresellerverify').text('Verified: Yes');
              $('.doresellerverify').addClass('text-success');
              $('.doresellerverify').removeClass('text-danger');
              $('.doresellerverify').attr('data-verified', 0);
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


      $('.doresellerstatus').on('click', function(){
        let status = $(this).data('status');


        //verified = $('.doverify').attr('verified', !verified);

        $.ajax({
          url: "{{route('root-reseller-status',['userid' => $request->userid])}}",
          method: 'post',

          success: function(r) {
            
            
            
            if (r.msg == 'n') {
                $('.doresellerstatus').text('Status: No');
                $('.doresellerstatus').addClass('text-danger');
                $('.doresellerstatus').removeClass('text-success');
                $('.doresellerstatus').attr('data-status', 'n');
            }

            if (r.msg == 'y') {
                $('.doresellerstatus').text('Status: Yes');
                $('.doresellerstatus').addClass('text-success');
                $('.doresellerstatus').removeClass('text-danger');
                $('.doresellerstatus').attr('data-status', 'y');
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
                      '<td>'+product.rate+'</td>'+
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
        if ( $('#paymentoption').val() == 'debit') {
          alert('Vat is not applicable with debit voucher');
          $(this).val(0);
          $('#paymentoption').focus();
          return false;
        }

        if ( $('#paymentoption').val() == '') {
          alert('Please select voucher type first');
          $(this).val(0);
          $('#paymentoption').focus();
          return false;
        }

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
                rate: product.rate,        
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

      $('body').on('keyup','#price',function(){


        /*if ($('#smsqty').val() == "") {
          $('#smsqty').focus();
        }*/

        //let invoicePrice = parseInt($('#smsqty').val())*parseFloat($('#rate').val());
        let invoiceQty = Math.round(parseFloat($('#price').val())/parseFloat($('#rate').val()));
        if ($('#rate').val() == '')
        {
          $('#smsqty').val(0);
          $('.smsqty').text(0);
        } else {
          $('#smsqty').val(Math.round(invoiceQty));
          $('.smsqty').text(Math.round(invoiceQty));
        }

        let storeProduct = localStorage.getItem('saleItem');
        let productInCart = JSON.parse(storeProduct);
        let productarr = [];
        if(localStorage.getItem('saleItem'))
        {
          productInCart.forEach(async (product,index) => {
              if (product.sms_type == $('#sms_type').val()) {
                await productarr.push({
                  client: product.client,
                  invoice_date: product.invoice_date,
                  sms_type: product.sms_type,
                  user_type: product.user_type,
                  smsqty: Math.round(parseFloat($('#price').val())/parseFloat($('#rate').val())),//product.smsqty, 
                  rate: parseFloat($('#rate').val()),              
                  price: parseFloat(('#price').val()),//invoicePrice,
                  validity_date: product.validity_date,
                  invoice_vat: parseFloat(product.invoice_vat),
                  paymentoption: $('#paymentoption').val(),
                  paymentby: $('#paymentby').val(),
                  remarks: $('#remarks').val()
                });
                await localStorage.setItem('saleItem', JSON.stringify(productarr));
              }
          });
        }

      });


      $('body').on('change','#paymentoption',function(){

        if ($('#invoice_vat').val() == "" && $('#paymentoption').val() == 'cash') {
          $('#invoice_vat').focus();
        }

        if ($('#paymentoption').val() == '' || $('#paymentoption').val() == 'debit') {
          invoiceVat = 0;
          $('.invoicevat').text(invoiceVat);
          $('#invoice_vat').val(0);
          let grandtotal = addCommas(invoiceTotal-invoiceVat);
          $('.grandtotal').text(grandtotal);
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
                rate: product.rate,       
                price: product.price,
                validity_date: product.validity_date,
                invoice_vat: $('#invoice_vat').val(),
                paymentoption: $('#paymentoption').val(),
                paymentby: $('#paymentby').val(),
                remarks: $('#remarks').val()
              });
              await localStorage.setItem('saleItem', JSON.stringify(productarr));
          });
        }

      });

      $('body').on('change','#paymentby',function(){

          if ($('#invoice_vat').val() == "" && $('#paymentoption').val() == 'cash') {
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
                  rate: product.rate,             
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
          if ($('#invoice_vat').val() == "" && $('#paymentoption').val() == 'cash') {
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
                  rate: product.rate,               
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
        let rootuser = '{{@request()->user()->id}}'

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

        /*if ($('#smsqty').val() == "")
        {
          document.querySelector('#smsqty').focus();
          document.querySelector('.smsqtyerr').style.display = 'block';
          //$('.invoicedateerr').css({'display':'block'});
          return false;
        } else {
          document.querySelector('.smsqtyerr').style.display = 'none';
        }

        if ($('#smsqty').val() == 0)
        {
          document.querySelector('#smsqty').focus();
          document.querySelector('.smsqtyerr').style.display = 'block';
          //$('.invoicedateerr').css({'display':'block'});
          return false;
        } else {
          document.querySelector('.smsqtyerr').style.display = 'none';
        }
        */

        if ($('#rate').val() == "")
        {
          document.querySelector('#rate').focus();
          document.querySelector('.rateerr').style.display = 'block';
          //$('.invoicedateerr').css({'display':'block'});
          return false;
        } else {
          document.querySelector('.rateerr').style.display = 'none';
        }

        if ($('#price').val() == "" && !NaN(('#price').val()))
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
              let smsqty = parseFloat(product.smsqty);
              let price = parseFloat(product.price);
              smsqty += parseFloat($('#smsqty').val());

              price += parseFloat($('#price').val());//parseFloat($('#smsqty').val())*parseFloat($('#rate').val());

              product.smsqty = parseFloat(smsqty);
              product.price = parseFloat(price);

              f = 1;
            }

          });

          if (f == 0) {
            productInCart.push({
              client: $('#invoice_client').val(),
              invoice_date: $('#invoice_date').val(),
              sms_type: $('#sms_type').val(),
              user_type: $('#user_type').val(),
              smsqty: Math.round(parseFloat($('#price').val())/parseFloat($('#rate').val())), 
              rate: parseFloat($('#rate').val()),              
              price: parseFloat($('#price').val()),
              validity_date: $('#validity_date').val(),
              invoice_vat: 0,
              paymentoption: $('#paymentoption').val(),
              paymentby: $('#paymentby').val(),
              remarks: $('#remarks').val()
            });

          }
          localStorage.setItem('saleItem', JSON.stringify(productInCart));


          $('.carttable tbody').empty();
          let i = 1;
          let invoiceTotal = 0;
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
                        '<td>'+Math.round(product.smsqty)+'</td>'+
                        '<td>'+product.rate+'</td>'+
                        '<td>'+Math.round(product.price)+'</td>'+
                        '<td class="text-center">'+
                            //'<a href="#" class="icon icon-pencil btn edt-currency" data-transid="" data-smstype="'+product.sms_type+'" data-smsqty="'+product.smsqty+'" data-price="'+product.price+'" data-validity_date="'+product.validity_date+'"></a> '+
                            '<a href="#" data-recindex="'+index+'" class="icon icon-trash btn dlt-prdb" data-id=""></a>'+
                        '</td>'+
                    '</tr>');

                    i++;
          });
          
        } else {

          salearr.push({
            client: $('#invoice_client').val(),
            invoice_date: $('#invoice_date').val(),
            sms_type: $('#sms_type').val(),
            user_type: $('#user_type').val(),
            smsqty: Math.round(parseFloat($('#price').val())/parseFloat($('#rate').val())), //parseFloat($('#smsqty').val()),               
            rate: parseFloat($('#rate').val()),              
            price: parseFloat($('#price').val()),//parseFloat($('#smsqty').val())*parseFloat($('#rate').val()),
            validity_date: $('#validity_date').val(),
            invoice_vat: 0,
            paymentoption: $('#paymentoption').val(),
            paymentby: $('#paymentby').val(),
            remarks: $('#remarks').val()
          });

          localStorage.setItem('saleItem', JSON.stringify(salearr));

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
                        '<td>'+Math.round(product.smsqty)+'</td>'+
                        '<td>'+product.rate+'</td>'+
                        '<td>'+Math.round(product.price)+'</td>'+
                        '<td class="text-center">'+
                            //'<a href="#" class="icon icon-pencil btn edt-currency" data-transid="" data-smstype="'+product.sms_type+'" data-smsqty="'+product.smsqty+'" data-price="'+product.price+'" data-validity_date="'+product.validity_date+'"></a> '+
                            '<a href="#" data-recindex="'+index+'" class="icon icon-trash btn dlt-prdb" data-id=""></a>'+
                        '</td>'+
                    '</tr>');

                    i++;
          });
          
        }

        $('#smsqty').val('');
        $('#rate').val('');
        $('#price').val('');
        $('#validity_date').val('');
        $('#paymentoption').val('');
        $('#remarks').val('');
        $('#sms_type').focus();
      });


      $('body').on('click','.clearinvoice', function(){
        swal({
          title: "Are you sure?",
          text: "Once reset, you will not be able to recover values!",
          icon: "warning",
          buttons: true,
        })
        .then((willDelete) => {
          console.log(willDelete);
          if (willDelete) {
            localStorage.removeItem('saleItem');
            swal('Form reset successfully', {
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
      
      //sms-saleto-reseller
      

      /** Root Clients invoice **/

      var rootInvoices = function(){
      $('.rootinvoicelist').DataTable({
          "order": [[ 0, "desc" ]],
          "processing": true,
          "language": {
            processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span> '
          },
          "serverSide": true,
          "ajax": { "url": "show-root-client-invoices","type": "get" },
          columnDefs: [
              
            //{ className: "actions", targets: 9  },  
            {
                targets: [ 0 ],
                visible: false,
                searchable: false
            }, 
            {
                targets: [ 1 ],
                visible: false,
                searchable: false
            }, 
            {
                targets: [ 12 ],
                visible: false,
                searchable: false
            }, 
            {
                targets: [ 13 ],
                visible: false,
                searchable: false
            }, 
            {  targets: 4,
              render: function (data, type, full, meta) {
                            { return `<div>${full[4]}</div><div style="font-weight:900; font-style:italic; font-size: 11px;">Sms Type ${full[13]}</div>`; }
              }   
            },
            {  targets: -1,
              render: function (data, type, full, meta) {
                                      { return '<a href="#" class="btn btn-sm btn-icon btn-pure btn-default rootuserdtl" data-toggle="tooltip" data-original-title="Remove" data-id="'+full[0]+'"><i class="icon icon-trash" aria-hidden="true"></i></a>'; }
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
              null,
              { "sWidth":"60px","sClass": "actions" }
          ],
          oLanguage: {sProcessing: '<div class="loader vertical-align-middle loader-rotate-plane" style="background-color: #2e688e; "></div>'}

        });
      }
      rootInvoices();


      /**Clients invoices **/

      var clientProfileInvoices = function(){
      $('.clientprofileinvoicelist').DataTable({
          "order": [[ 0, "desc" ]],
          "processing": true,
          "language": {
            processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span> '
          },
          "serverSide": true,
          "ajax": { "url": "{{route('show-my-invoicelist',[@$request->userid])}}","type": "get" },
          columnDefs: [
              
            //{ className: "actions", targets: 9  },  
            {
                targets: [ 0 ],
                visible: false,
                searchable: false
            }, 
            {
                targets: [ 1 ],
                visible: false,
                searchable: false
            }, 
            {
                targets: [ 12 ],
                visible: false,
                searchable: false
            }, 
            {
                targets: [ 13 ],
                visible: false,
                searchable: false
            }, 
            {  targets: 4,
              render: function (data, type, full, meta) {
                            { return `<div>${full[4]}</div><div style="font-weight:900; font-style:italic; font-size: 11px;">Sms Type ${full[13]}</div>`; }
              }   
            }
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
      clientProfileInvoices();


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
            {
                targets: [ 1 ],
                visible: false,
                searchable: false
            }, 
            {
                targets: [ 12 ],
                visible: false,
                searchable: false
            }, 
            {
                targets: [ 13 ],
                visible: false,
                searchable: false
            }, 
            {  targets: 4,
              render: function (data, type, full, meta) {
                            { return `<div>${full[4]}</div><div style="font-weight:900; font-style:italic; font-size: 11px;">Sms Type ${full[13]}</div>`; }
              }   
            },
            {  targets: -1,
              render: function (data, type, full, meta) {
                                      console.log(full);
                                      { return '<a href="#" class="btn btn-sm btn-icon btn-pure btn-default rootuserdtl" data-toggle="tooltip" data-original-title="Remove" data-id="'+full[0]+'"><i class="icon icon-trash" aria-hidden="true"></i></a>'; }
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
              null,
              { "sWidth":"60px","sClass": "actions" }
          ],
          oLanguage: {sProcessing: '<div class="loader vertical-align-middle loader-rotate-plane" style="background-color: #2e688e; "></div>'}

        });
      }
      rootResellerInvoices();

      @if(Route::currentRouteName() == 'client-profile-senderid')

      var clientAssignedSenderid = function() {
          const clientid = document.querySelector('.profile-senderid');
          const getid = clientid.getAttribute('data-current_profileid');

          let url = "{{route('client-senderid-list',':id')}}";

          url = url.replace(':id', getid);
          $('.clientassignsenderid').DataTable().destroy();
          $('.clientassignsenderid').DataTable( {
              "ajax": {
                "processing": true,
                "url": url,
                "dataType": 'json',
                "type": "get",
                "beforeSend": function (xhr) {
                    }
              },
              "pageLength": 5,
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
                          return '<a href="#" data-userid="'+ full.user_id + '" data-default="'+ full.default +'" data-smssenderid="'+ full.sms_sender_id +'" style="line-height: 27px;">'+full.default+'</a>'; 
                      }
                  },
                  { "data": "created_at" },
              ],
          } );
      }

      clientAssignedSenderid();
      

      //jquery keyup delay until stop tipping

      $('.tableprofilesenderid tbody').empty();
      let getsenderid = [];
      let searchsenderid = $('.searchsenderid').val();
      let current_profileid = document.querySelector('.searchsenderid')
      current_profileid = current_profileid.getAttribute('data-current_profileid');
      $.ajax({
        url: "{{route('search-unassigned-senderid')}}",
        type: "post",
        data: {
          search: searchsenderid,
          userid: current_profileid
        },
        success: function(res) {
          
          $.each(res.data, (index, senderid) => {
              $('.tableprofilesenderid tbody').append(
                '<tr>'+
                  '<td><input type="checkbox" name="sms_sender_id[]" value="'+senderid.id+'" /></td>'+
                  '<td>'+senderid.sender_name+'</td>'+
                '</tr>'
              );
          })
        },
        error: function(err) {
          console.log(err);
        }
      });

      var delay = (function(){
          var timer = 0;
          return function(callback, ms){
              clearTimeout (timer);
              timer = setTimeout(callback, ms);
          };
      })();
      $('body').on('keyup','.searchsenderid', function(){
        delay(() =>{
            $('.tableprofilesenderid tbody').empty();
            let getsenderid = [];
            let searchsenderid = $(this).val();
            let current_profileid = document.querySelector('.searchsenderid')
            current_profileid = current_profileid.getAttribute('data-current_profileid');
            $.ajax({
              url: "{{route('search-unassigned-senderid')}}",
              type: "post",
              data: {
                search: searchsenderid,
                userid: current_profileid
              },
              success: function(res) {
                
                $.each(res.data, (index, senderid) => {
                    $('.tableprofilesenderid tbody').append(
                      '<tr>'+
                        '<td><input type="checkbox" name="sms_sender_id[]" value="'+senderid.id+'" /></td>'+
                        '<td>'+senderid.sender_name+'</td>'+
                      '</tr>'
                    );
                })
              },
              error: function(err) {
                console.log(err);
              }
            });
        },1000);
        
      
      });

      @endif

      var clientTemplate = function(){
      $('.clienttemplate').DataTable().destroy();
      $('.activedeal').css({'background-color':'#3e8ef7','color':'#fff'});    
      $('.clienttemplate').DataTable({
          "order": [[ 0, "desc" ]],
          "processing": true,
          "language": {
            processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span> '
          },

          "serverSide": true,
          "ajax": { "url": "{{route('rander-client-template',[@$request->userid])}}","type": "get" },
          columnDefs: [
            {
                targets: [8],
                visible: false,
                searchable: false
            },
            {
                targets: [9],
                visible: false,
                searchable: false
            },
            {
                targets: [2],
                render: function (data, type, full, meta) {
                  {
                    return `<div>
                        ${full[2]}
                        <div style="font-style: italic; font-weight: 900; font-size:10px;">BTRC File: <a href="https://login.smsinbd.com/storage/app/public/templateContent/${full[8]}">${full[8]}</a></div>
                        <div style="font-style: italic; font-weight: 900; font-size:10px;">BTRC File Status: ${full[9] == 1 ? "true" : "false" }</div>
                    </div>`
                  }
                }
            },
            {  targets: -1,
              render: function (data, type, full, meta) {
                    {
                      return '<a href="#" class="btn btn-sm btn-icon btn-pure btn-default templateeditform"  data-toggle="modal" data-target="#smsRootTemplate" data-original-title="edit" data-id="'+full[0]+'" data-templatetitle="'+full[1]+'" data-templatedesc="'+full[2]+'" data-templateowner="'+full[3]+'" data-usertype="'+full[4]+'" data-status="'+full[5]+'"><i class="icon icon-pencil" aria-hidden="true"></i></a>';

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
              null,
              null,
              null,
              null,
              { "sWidth":"60px","sClass": "actions" }
          ],
          oLanguage: {sProcessing: '<div class="loader vertical-align-middle loader-rotate-plane" style="background-color: #2e688e; "></div>'}

        });
      }
      clientTemplate();


    })(document, window, jQuery);
  </script>

  @elseif(Auth::guard('manager')->check())

  <script type="text/javascript">
    (function(document, window, $){

      @if(Route::currentRouteName() == 'reseller-profile-senderid')
          var renderResellerSenderId = function() {
              $('#resellerassignedsmssender').DataTable().destroy();
              $('#resellerassignedsmssender').DataTable( {
              "order": [[ 0, "desc" ]],  
              "ajax": {
                  "processing": true,
                  "url": "{{route('rander-sms-senderid-for-reseller',[@$request->userid])}}",
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
                      { "data": "created_by" },
                  ]
              } );
          }

          renderResellerSenderId();

          //jquery keyup delay until stop tipping

          $('.tableprofilesenderid tbody').empty();
          let getsenderid = [];
          let searchsenderid = $('.searchsenderid').val();
          let current_profileid = document.querySelector('.searchsenderid')
          current_profileid = current_profileid.getAttribute('data-current_profileid');
          $.ajax({
            url: "{{route('search-unassigned-reseller-senderid')}}",
            type: "post",
            data: {
              search: searchsenderid,
              userid: current_profileid
            },
            success: function(res) {
              
              $.each(res.data, (index, senderid) => {
                  $('.tableprofilesenderid tbody').append(
                    '<tr>'+
                      '<td><input type="checkbox" name="sms_sender_id[]" value="'+senderid.id+'" /></td>'+
                      '<td>'+senderid.sender_name+'</td>'+
                    '</tr>'
                  );
              })
            },
            error: function(err) {
              console.log(err);
            }
          });

          var delay = (function(){
              var timer = 0;
              return function(callback, ms){
                  clearTimeout (timer);
                  timer = setTimeout(callback, ms);
              };
          })();
          $('body').on('keyup','.searchsenderid', function(){
            delay(() =>{
                $('.tableprofilesenderid tbody').empty();
                let getsenderid = [];
                let searchsenderid = $(this).val();
                let current_profileid = document.querySelector('.searchsenderid')
                current_profileid = current_profileid.getAttribute('data-current_profileid');
                $.ajax({
                  url: "{{route('search-unassigned-reseller-senderid')}}",
                  type: "post",
                  data: {
                    search: searchsenderid,
                    userid: current_profileid
                  },
                  success: function(res) {
                    
                    $.each(res.data, (index, senderid) => {
                        $('.tableprofilesenderid tbody').append(
                          '<tr>'+
                            '<td><input type="checkbox" name="sms_sender_id[]" value="'+senderid.id+'" /></td>'+
                            '<td>'+senderid.sender_name+'</td>'+
                          '</tr>'
                        );
                    })
                  },
                  error: function(err) {
                    console.log(err);
                  }
                });
            },1000);
            
          
          });

      @endif

      @if(Route::currentRouteName() == 'reseller-profile-invoice')

          /** reseller's Clients invoice **/

          var resellerMyInvoicesList = function(){
          $('.resellermyinvoicelist').DataTable({
              "order": [[ 0, "desc" ]],
              "processing": true,
              "language": {
                processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span> '
              },
              "serverSide": true,
              "ajax": { "url": "{{route('show-my-reseller-invoicelist',[@$request->userid])}}","type": "get" },
              columnDefs: [
                  
                //{ className: "actions", targets: 9  },  
                {
                    targets: [ 0 ],
                    visible: false,
                    searchable: false
                }, 
                {
                    targets: [ 1 ],
                    visible: false,
                    searchable: false
                }, 
                {
                    targets: [ 12 ],
                    visible: false,
                    searchable: false
                }, 
                {
                    targets: [ 13 ],
                    visible: false,
                    searchable: false
                }, 
                {  targets: 4,
                  render: function (data, type, full, meta) {
                                { return `<div>${full[4]}</div><div style="font-weight:900; font-style:italic; font-size: 11px;">Sms Type ${full[13]}</div>`; }
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
          resellerMyInvoicesList();
      @endif
      /**Clients invoices **/

      var clientProfileInvoices = function(){
      $('.clientprofileinvoicelist').DataTable({
          "order": [[ 0, "desc" ]],
          "processing": true,
          "language": {
            processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span> '
          },
          "serverSide": true,
          "ajax": { "url": "{{route('show-my-invoicelist',[@$request->userid])}}","type": "get" },
          columnDefs: [
              
            //{ className: "actions", targets: 9  },  
            {
                targets: [ 0 ],
                visible: false,
                searchable: false
            }, 
            {
                targets: [ 1 ],
                visible: false,
                searchable: false
            }, 
            {
                targets: [ 12 ],
                visible: false,
                searchable: false
            }, 
            {
                targets: [ 13 ],
                visible: false,
                searchable: false
            }, 
            {  targets: 4,
              render: function (data, type, full, meta) {
                            { return `<div>${full[4]}</div><div style="font-weight:900; font-style:italic; font-size: 11px;">Sms Type ${full[13]}</div>`; }
              }   
            }
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
      clientProfileInvoices();
      @if(Route::currentRouteName() == 'client-profile-senderid')

        var clientAssignedSenderid = function() {
            const clientid = document.querySelector('.profile-senderid');
            const getid = clientid.getAttribute('data-current_profileid');

            let url = "{{route('client-senderid-list',':id')}}";

            url = url.replace(':id', getid);
            $('.clientassignsenderid').DataTable().destroy();
            $('.clientassignsenderid').DataTable( {
                "ajax": {
                  "processing": true,
                  "url": url,
                  "dataType": 'json',
                  "type": "get",
                  "beforeSend": function (xhr) {
                      }
                },
                "pageLength": 5,
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
                            return '<a href="#" data-userid="'+ full.user_id + '" data-default="'+ full.default +'" data-smssenderid="'+ full.sms_sender_id +'" style="line-height: 27px;">'+full.default+'</a>'; 
                        }
                    },
                    { "data": "created_at" },
                ],
            } );
        }

        clientAssignedSenderid();


        //jquery keyup delay until stop tipping

        $('.tableprofilesenderid tbody').empty();
        let getsenderid = [];
        let searchsenderid = $('.searchsenderid').val();
        let current_profileid = document.querySelector('.searchsenderid')
        current_profileid = current_profileid.getAttribute('data-current_profileid');
        $.ajax({
          url: "{{route('search-unassigned-senderid')}}",
          type: "post",
          data: {
            search: searchsenderid,
            userid: current_profileid
          },
          success: function(res) {
            
            $.each(res.data, (index, senderid) => {
                $('.tableprofilesenderid tbody').append(
                  '<tr>'+
                    '<td><input type="checkbox" name="sms_sender_id[]" value="'+senderid.id+'" /></td>'+
                    '<td>'+senderid.sender_name+'</td>'+
                  '</tr>'
                );
            })
          },
          error: function(err) {
            console.log(err);
          }
        });

        var delay = (function(){
            var timer = 0;
            return function(callback, ms){
                clearTimeout (timer);
                timer = setTimeout(callback, ms);
            };
        })();
        $('body').on('keyup','.searchsenderid', function(){
          delay(() =>{
              $('.tableprofilesenderid tbody').empty();
              let getsenderid = [];
              let searchsenderid = $(this).val();
              let current_profileid = document.querySelector('.searchsenderid')
              current_profileid = current_profileid.getAttribute('data-current_profileid');
              $.ajax({
                url: "{{route('search-unassigned-senderid')}}",
                type: "post",
                data: {
                  search: searchsenderid,
                  userid: current_profileid
                },
                success: function(res) {
                  
                  $.each(res.data, (index, senderid) => {
                      $('.tableprofilesenderid tbody').append(
                        '<tr>'+
                          '<td><input type="checkbox" name="sms_sender_id[]" value="'+senderid.id+'" /></td>'+
                          '<td>'+senderid.sender_name+'</td>'+
                        '</tr>'
                      );
                  })
                },
                error: function(err) {
                  console.log(err);
                }
              });
          },1000);
          

        });

      @endif

      var clientTemplate = function(){
      $('.clienttemplate').DataTable().destroy();
      $('.activedeal').css({'background-color':'#3e8ef7','color':'#fff'});    
      $('.clienttemplate').DataTable({
          "order": [[ 0, "desc" ]],
          "processing": true,
          "language": {
            processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span> '
          },

          "serverSide": true,
          "ajax": { "url": "{{route('rander-client-template',[@$request->userid])}}","type": "get" },
          columnDefs: [
            {
                targets: [8],
                visible: false,
                searchable: false
            },
            {
                targets: [9],
                visible: false,
                searchable: false
            },
            {
                targets: [2],
                render: function (data, type, full, meta) {
                  {
                    return `<div>
                        ${full[2]}
                        <div style="font-style: italic; font-weight: 900; font-size:10px;">BTRC File: <a href="https://login.smsinbd.com/storage/app/public/templateContent/${full[8]}">${full[8]}</a></div>
                        <div style="font-style: italic; font-weight: 900; font-size:10px;">BTRC File Status: ${full[9] == 1 ? "true" : "false" }</div>
                    </div>`
                  }
                }
            },
            {  targets: -1,
              render: function (data, type, full, meta) {
                    {
                      return '<a href="#" class="btn btn-sm btn-icon btn-pure btn-default templateeditform"  data-toggle="modal" data-target="#smsRootTemplate" data-original-title="edit" data-id="'+full[0]+'" data-templatetitle="'+full[1]+'" data-templatedesc="'+full[2]+'" data-templateowner="'+full[3]+'" data-usertype="'+full[4]+'" data-status="'+full[5]+'"><i class="icon icon-pencil" aria-hidden="true"></i></a>';

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
              null,
              null,
              null,
              null,
              { "sWidth":"60px","sClass": "actions" }
          ],
          oLanguage: {sProcessing: '<div class="loader vertical-align-middle loader-rotate-plane" style="background-color: #2e688e; "></div>'}

        });
      }
      clientTemplate();

      $('body').on('click','.templateeditform', function(){
        let id = $(this).data('id');
        let templatetitle = $(this).data('templatetitle');
        let templatedesc = $(this).data('templatedesc');
        let templateowner = $(this).data('templateowner');
        let usertype = $(this).data('usertype');
        let status = $(this).data('status');
        $('#exampleModalLabel').text('Edit User Template');
        $('#template_title').val(templatetitle);
        $('#id').val(id);
        $('#frmmode').val('edt');
        $('#template_desc').val(templatedesc)
        if (status == 'Active') {
          $('#senderid_status_yes').prop('checked',true);
          $('#senderid_status_no').prop('checked',false);
        }

        if (status == 'Inactive') {
          $('#senderid_status_no').prop('checked',true);
          $('#senderid_status_yes').prop('checked',false);
          
        }
      });


      $('#smsRootTemplate').on('hidden.bs.modal', function(){
        $('#exampleModalLabel').text('Add User Template');
        $('#frmmode').val('ins');
        $('#id').val('');
        $('#template_title').val('');
        $('#template_desc').val('');
        $('#senderid_status_no').prop('checked',false);
        $('#senderid_status_yes').prop('checked',false);
      });


      

      /** End Template */

      @if(isset($request->userid))

      $('.doverify').on('click', function(){

        let verified = $(this).data('verified');


        //verified = $('.doverify').attr('verified', !verified);

        $.ajax({
          url: "{{route('root-client-decument-verify',['userid' => $request->userid])}}",
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
          url: "{{route('root-client-status',['userid' => $request->userid])}}",
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


      $('.doresellerverify').on('click', function(){

      let verified = $(this).data('verified');


      //verified = $('.doverify').attr('verified', !verified);

      $.ajax({
        url: "{{route('root-reseller-decument-verify',['userid' => $request->userid])}}",
        method: 'post',

        success: function(r) {
          
          
          
          if (r.msg == 0) {
              $('.doresellerverify').text('Verified: No');
              $('.doresellerverify').addClass('text-danger');
              $('.doresellerverify').removeClass('text-success');
              $('.doresellerverify').attr('data-verified', 1);
          }

          if (r.msg == 1) {
              $('.doresellerverify').text('Verified: Yes');
              $('.doresellerverify').addClass('text-success');
              $('.doresellerverify').removeClass('text-danger');
              $('.doresellerverify').attr('data-verified', 0);
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


      $('.doresellerstatus').on('click', function(){
        let status = $(this).data('status');


        //verified = $('.doverify').attr('verified', !verified);

        $.ajax({
          url: "{{route('root-reseller-status',['userid' => $request->userid])}}",
          method: 'post',

          success: function(r) {
            
            
            
            if (r.msg == 'n') {
                $('.doresellerstatus').text('Status: No');
                $('.doresellerstatus').addClass('text-danger');
                $('.doresellerstatus').removeClass('text-success');
                $('.doresellerstatus').attr('data-status', 'n');
            }

            if (r.msg == 'y') {
                $('.doresellerstatus').text('Status: Yes');
                $('.doresellerstatus').addClass('text-success');
                $('.doresellerstatus').removeClass('text-danger');
                $('.doresellerstatus').attr('data-status', 'y');
            }
            console.log(r.msg);
          },

          error: function(e) {
            console.log(e);
            
          }

        });
      });

      @endif
      
         /** manager Clients **/
   
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
                          return "aaaaa";
                        } else {
                          return "bbbbb";
                        }
                      }
                  },
                  {  targets: -1,
                    render: function (data, type, full, meta) {
                      let id = full[0];
                      let name = full[1];
                      let mobile = full[4];
                      $.ajax({
                        url: "{{route('client-assigned-senderids')}}",
                        type: "post",
                        data: {
                          clientid: id
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
                                            { return '<a href="client-profile/'+full[0]+'/index" class="btn btn-sm btn-icon btn-pure btn-default rootuseredtfrm" data-original-title="edit" data-id="'+full[0]+'"><i class="icon icon-pencil" aria-hidden="true"></i></a>\n\
                                              <a href="#" class="btn btn-sm btn-icon btn-pure btn-default viewclientassignedsenderid totalsenderid'+full[0]+'" data-toggle="modal" data-target="#resellerassignedsenders" data-original-title="edit" data-id="'+full[0]+'" data-name="'+full[1]+'" data-mobile="'+full[4]+'"></a>\n\
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

      /** manager Reseller **/

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

      $('body').on('click','.viewresellerbalance', function(e){
        e.preventDefault();
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


            $('body').on('click','.viewbalance', function(e){
              e.preventDefault();
              let id = $(this).data('id');
              let name = $(this).data('name');
              let mobile = $(this).data('mobile');
              $.ajax({
                url: "{{route('client.balance')}}",
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

            $('body').on('click','.viewclientassignedsenderid', function(e){
                e.preventDefault();
                let id = $(this).data('id');
                let name = $(this).data('name');
                let mobile = $(this).data('mobile');
                $.ajax({
                  url: "{{route('client-assigned-senderids')}}",
                  type: "post",
                  data: {
                    clientid: id
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
                      '<td>'+product.rate+'</td>'+
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
        if ( $('#paymentoption').val() == 'debit') {
          alert('Vat is not applicable with debit voucher');
          $(this).val(0);
          $('#paymentoption').focus();
          return false;
        }

        if ( $('#paymentoption').val() == '') {
          alert('Please select voucher type first');
          $(this).val(0);
          $('#paymentoption').focus();
          return false;
        }

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
                rate: product.rate,        
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

      $('body').on('keyup','#price',function(){

        /*if ($('#smsqty').val() == "") {
          $('#smsqty').focus();
        }*/

        //let invoicePrice = parseInt($('#smsqty').val())*parseFloat($('#rate').val());
        let invoiceQty = Math.round(parseFloat($('#price').val())/parseFloat($('#rate').val()));
        if ($('#rate').val() == '')
        {
          $('#smsqty').val(0);
          $('.smsqty').text(0);
        } else {
          $('#smsqty').val(Math.round(invoiceQty));
          $('.smsqty').text(Math.round(invoiceQty));
        }

        let storeProduct = localStorage.getItem('saleItem');
        let productInCart = JSON.parse(storeProduct);
        let productarr = [];
        if(localStorage.getItem('saleItem'))
        {
          productInCart.forEach(async (product,index) => {
              if (product.sms_type == $('#sms_type').val()) {
                await productarr.push({
                  client: product.client,
                  invoice_date: product.invoice_date,
                  sms_type: product.sms_type,
                  user_type: product.user_type,
                  smsqty: Math.round(parseFloat($('#price').val())/parseFloat($('#rate').val())),//product.smsqty, 
                  rate: parseFloat($('#rate').val()),              
                  price: parseFloat(('#price').val()),//invoicePrice,
                  validity_date: product.validity_date,
                  invoice_vat: parseFloat(product.invoice_vat),
                  paymentoption: $('#paymentoption').val(),
                  paymentby: $('#paymentby').val(),
                  remarks: $('#remarks').val()
                });
                await localStorage.setItem('saleItem', JSON.stringify(productarr));
              }
          });
        }

      });


      $('body').on('change','#paymentoption',function(){

        if ($('#invoice_vat').val() == "" && $('#paymentoption').val() == 'cash') {
          $('#invoice_vat').focus();
        }

        if ($('#paymentoption').val() == '' || $('#paymentoption').val() == 'debit') {
          invoiceVat = 0;
          $('.invoicevat').text(invoiceVat);
          $('#invoice_vat').val(0);
          let grandtotal = addCommas(invoiceTotal-invoiceVat);
          $('.grandtotal').text(grandtotal);
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
                rate: product.rate,       
                price: product.price,
                validity_date: product.validity_date,
                invoice_vat: $('#invoice_vat').val(),
                paymentoption: $('#paymentoption').val(),
                paymentby: $('#paymentby').val(),
                remarks: $('#remarks').val()
              });
              await localStorage.setItem('saleItem', JSON.stringify(productarr));
          });
        }

      });

      $('body').on('change','#paymentby',function(){

          if ($('#invoice_vat').val() == "" && $('#paymentoption').val() == 'cash') {
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
                  rate: product.rate,             
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
          if ($('#invoice_vat').val() == "" && $('#paymentoption').val() == 'cash') {
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
                  rate: product.rate,               
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

        /*if ($('#smsqty').val() == "")
        {
          document.querySelector('#smsqty').focus();
          document.querySelector('.smsqtyerr').style.display = 'block';
          //$('.invoicedateerr').css({'display':'block'});
          return false;
        } else {
          document.querySelector('.smsqtyerr').style.display = 'none';
        }

        if ($('#smsqty').val() == 0)
        {
          document.querySelector('#smsqty').focus();
          document.querySelector('.smsqtyerr').style.display = 'block';
          //$('.invoicedateerr').css({'display':'block'});
          return false;
        } else {
          document.querySelector('.smsqtyerr').style.display = 'none';
        }
        */

        if ($('#rate').val() == "")
        {
          document.querySelector('#rate').focus();
          document.querySelector('.rateerr').style.display = 'block';
          //$('.invoicedateerr').css({'display':'block'});
          return false;
        } else {
          document.querySelector('.rateerr').style.display = 'none';
        }

        if ($('#price').val() == "" && !NaN(('#price').val()))
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
              let smsqty = parseFloat(product.smsqty);
              let price = parseFloat(product.price);
              smsqty += parseFloat($('#smsqty').val());

              price += parseFloat($('#price').val());//parseFloat($('#smsqty').val())*parseFloat($('#rate').val());

              product.smsqty = parseFloat(smsqty);
              product.price = parseFloat(price);

              f = 1;
            }

          });

          if (f == 0) {
            productInCart.push({
              client: $('#invoice_client').val(),
              invoice_date: $('#invoice_date').val(),
              sms_type: $('#sms_type').val(),
              user_type: $('#user_type').val(),
              smsqty: Math.round(parseFloat($('#price').val())/parseFloat($('#rate').val())), 
              rate: parseFloat($('#rate').val()),              
              price: parseFloat($('#price').val()),
              validity_date: $('#validity_date').val(),
              invoice_vat: 0,
              paymentoption: $('#paymentoption').val(),
              paymentby: $('#paymentby').val(),
              remarks: $('#remarks').val()
            });

          }
          localStorage.setItem('saleItem', JSON.stringify(productInCart));


          $('.carttable tbody').empty();
          let i = 1;
          let invoiceTotal = 0;
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
                        '<td>'+Math.round(product.smsqty)+'</td>'+
                        '<td>'+product.rate+'</td>'+
                        '<td>'+Math.round(product.price)+'</td>'+
                        '<td class="text-center">'+
                            //'<a href="#" class="icon icon-pencil btn edt-currency" data-transid="" data-smstype="'+product.sms_type+'" data-smsqty="'+product.smsqty+'" data-price="'+product.price+'" data-validity_date="'+product.validity_date+'"></a> '+
                            '<a href="#" data-recindex="'+index+'" class="icon icon-trash btn dlt-prdb" data-id=""></a>'+
                        '</td>'+
                    '</tr>');

                    i++;
          });
          
        } else {

          salearr.push({
            client: $('#invoice_client').val(),
            invoice_date: $('#invoice_date').val(),
            sms_type: $('#sms_type').val(),
            user_type: $('#user_type').val(),
            smsqty: Math.round(parseFloat($('#price').val())/parseFloat($('#rate').val())), //parseFloat($('#smsqty').val()),               
            rate: parseFloat($('#rate').val()),              
            price: parseFloat($('#price').val()),//parseFloat($('#smsqty').val())*parseFloat($('#rate').val()),
            validity_date: $('#validity_date').val(),
            invoice_vat: 0,
            paymentoption: $('#paymentoption').val(),
            paymentby: $('#paymentby').val(),
            remarks: $('#remarks').val()
          });

          localStorage.setItem('saleItem', JSON.stringify(salearr));

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
                        '<td>'+Math.round(product.smsqty)+'</td>'+
                        '<td>'+product.rate+'</td>'+
                        '<td>'+Math.round(product.price)+'</td>'+
                        '<td class="text-center">'+
                            //'<a href="#" class="icon icon-pencil btn edt-currency" data-transid="" data-smstype="'+product.sms_type+'" data-smsqty="'+product.smsqty+'" data-price="'+product.price+'" data-validity_date="'+product.validity_date+'"></a> '+
                            '<a href="#" data-recindex="'+index+'" class="icon icon-trash btn dlt-prdb" data-id=""></a>'+
                        '</td>'+
                    '</tr>');

                    i++;
          });
          
        }

        $('#smsqty').val('');
        $('#rate').val('');
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

        if ($('#invoice_vat').val() == "" && $('#paymentoption').val() == 'cash')
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

        /*if ($('#paymentoption').val() != 'cash')
        {
          document.querySelector('#paymentby').focus();
          document.querySelector('.paymentbyerr').style.display = 'block';
          return false;
        } else {
          document.querySelector('.paymentbyerr').style.display = 'none';
        }*/

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
                rate: product.rate,               
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

      /** manager Clients invoice **/

      var rootInvoices = function(){
      $('.rootinvoicelist').DataTable({
          "order": [[ 0, "desc" ]],
          "processing": true,
          "language": {
            processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span> '
          },
          "serverSide": true,
          "ajax": { "url": "show-root-client-invoices","type": "get" },
          columnDefs: [
              
            //{ className: "actions", targets: 9  },  
            {
                targets: [ 0 ],
                visible: false,
                searchable: false
            }, 
            {
                targets: [ 1 ],
                visible: false,
                searchable: false
            }, 
            {
                targets: [ 12 ],
                visible: false,
                searchable: false
            }, 
            {
                targets: [ 13 ],
                visible: false,
                searchable: false
            }, 
            {  targets: 4,
              render: function (data, type, full, meta) {
                            { return `<div>${full[4]}</div><div style="font-weight:900; font-style:italic; font-size: 11px;">Sms Type ${full[13]}</div>`; }
              }   
            },
            {  targets: -1,
              render: function (data, type, full, meta) {
                                      { return '<a href="#" class="btn btn-sm btn-icon btn-pure btn-default rootuserdtl" data-toggle="tooltip" data-original-title="Remove" data-id="'+full[0]+'"><i class="icon icon-trash" aria-hidden="true"></i></a>'; }
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
            {
                targets: [ 1 ],
                visible: false,
                searchable: false
            }, 
            {
                targets: [ 12 ],
                visible: false,
                searchable: false
            }, 
            {
                targets: [ 13 ],
                visible: false,
                searchable: false
            }, 
            {  targets: 4,
              render: function (data, type, full, meta) {
                            { return `<div>${full[4]}</div><div style="font-weight:900; font-style:italic; font-size: 11px;">Sms Type ${full[13]}</div>`; }
              }   
            },
            {  targets: -1,
              render: function (data, type, full, meta) {
                                      console.log(full);
                                      { return '<a href="#" class="btn btn-sm btn-icon btn-pure btn-default rootuserdtl" data-toggle="tooltip" data-original-title="Remove" data-id="'+full[0]+'"><i class="icon icon-trash" aria-hidden="true"></i></a>'; }
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
              null,
              { "sWidth":"60px","sClass": "actions" }
          ],
          oLanguage: {sProcessing: '<div class="loader vertical-align-middle loader-rotate-plane" style="background-color: #2e688e; "></div>'}

        });
      }
      rootResellerInvoices();

      /**-------Gateway End------- */

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
              "order": [[ 0, "desc" ]],
              "processing": true,
              "language": {
                processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span> '
              },
              "serverSide": true,
              "ajax": {
                "url": "{{route('root-clients-sms-send-data')}}",
                "type": "get",
                "data": {
                  fromdate: $('#from_date').val(),
                  todate: $('#to_date').val(),
                  userid: $('#userid').val()
                },
                "beforeSend": function (xhr) {
                  
                },
              },
              columnDefs:
              [ 
                {
                targets: [0],
                    visible: false,
                    searchable: false
                },
                {
                targets: [3],
                    visible: false,
                    searchable: false
                },
                {
                targets: [12],
                    visible: false,
                    searchable: false
                },
                {
                    targets: [5],
                    render: function(data, type, full, meta) {
                      return '<a href="#" class="viewsmsdetails" style="color:blue;" data-toggle="modal" data-target="#viewsmsdetails" data-submittedat="'+full[10]+'" data-contact="'+full[5]+'" data-remarks="'+full[1]+'" data-content="'+full[12]+'">'+full[5]+'</a>';
                    }
                },
                {
                    targets: [11],
                    render: function(data, type, full, meta) {
                        if (full[11] == 1)
                        {
                          return '<a href="#" class="btn btn-success btn-sm">Delivered</a>'; 
                        } else {
                          return '<a href="#" class="btn btn-danger btn-sm">Failed</a>';
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
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                //{ "sWidth":"60px","sClass": "actions" }
            ],
            oLanguage: {sProcessing: '<div class="loader vertical-align-middle loader-rotate-plane" style="background-color: #2e688e; "></div>'}
          } );
      }

      clientSmsSendReport();


      var clientCampaignSmsSendReport = function() {
          $.ajax({
            url: "{{route('root-client-total-sms-send')}}",
            type: "get",
            data: {
              fromdate: $('#from_date').val(),
              todate: $('#to_date').val(),
              userid: $('#userid').val()
            },
            success: function(res) {
              $('.totalsendsms').css({'margin-top':'20px'});
              $('.totalsendsms').text(`Total SMS sent: ${res}`);
              $('.totalsendsms').addClass('font-bold');
            }
          });

          $.ajax({
            url: "{{route('root-client-total-sms-campaign')}}",
            type: "get",
            data: {
              fromdate: $('#from_date').val(),
              todate: $('#to_date').val(),
              userid: $('#userid').val()
            },
            success: function(res) {
              $('.totalcampaign').css({'margin-right':'20px','margin-top':'20px'});
              $('.totalcampaign').text(`Total Campaign: ${res}`+'  ');
              $('.totalcampaign').addClass('font-bold');
            }
          });
          $('.clientcampaignsmsreport').DataTable().destroy();
          
          $('.clientcampaignsmsreport').DataTable( {
              "order": [[ 0, "desc" ]],
              "processing": true,
              "language": {
                processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span> '
              },
              "serverSide": true,
              "ajax": {
                "url": "{{route('root-client-campaign-send-data')}}",
                "type": "get",
                "data": {
                  fromdate: $('#from_date').val(),
                  todate: $('#to_date').val(),
                  userid: $('#userid').val()
                },
                "beforeSend": function (xhr) {
                  
                },
              },
              columnDefs:
              [ 
                
                {
                targets: [9],
                    visible: false,
                    searchable: false
                },
                {
                    targets: [0],
                    render: function(data, type, full, meta) {
                      return '<a href="#" class="viewsmsdetails" style="color:blue;" data-toggle="modal" data-target="#viewsmsdetails" data-submittedat="'+full[7]+'" data-remarks="'+full[0]+'" data-content="'+full[9]+'">'+full[0]+'</a>';
                    }
                },
                {
                    targets: [8],
                    render: function(data, type, full, meta) {
                      return `<a href="#" class="btn btn-success btn-sm campaignstatus" data-toggle="modal" data-target="#viewcampaignmobile" data-remarks="${full[0]}"><i class="fa fa-eye"></i></a>
                      <a href="/export-excel?remarks=${full[0]}" target="_self" class="btn btn-success btn-sm exportexcel" data-remarks="${full[0]}"><i class="fa fa-file-excel-o"></i></a>`; 
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
                //{ "sWidth":"60px","sClass": "actions" }
            ],
            oLanguage: {sProcessing: '<div class="loader vertical-align-middle loader-rotate-plane" style="background-color: #2e688e; "></div>'}
          } );
      }

      clientCampaignSmsSendReport();

      /*$('body').on('click','.exportexcel', function(e){
          let remarks = $(this).data('remarks');

          $.ajax({
            url: "{{route('export-excel')}}",
            type: "get",
            data: {
              remarks: remarks
            },
            success: function(res) {
              console.log(res);
            },
            error: function(err){
              console.log(err);
            }
          });
      });*/

      $('body').on('click','.campaignstatus', function(e){
        let remarks = $(this).data('remarks');
          $('.campaignmobile').DataTable().destroy();
          $('.campaignmobile').DataTable( {
          "order": [[ 0, "desc" ]],
          "ajax": {
              "processing": true,
              "url": "{{route('root-client-campaign-mobile-list')}}",
              "dataType": 'json',
              "type": "post",
              "data": {
                  remarks: remarks
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
                
                {
                  targets: [7],
                  render: function (data, type, full, meta) {
                    if (full.status == '1') {
                          return '<a href="#" class="btn btn-success btn-sm">Delivered</a>'; 
                        } else {
                          return '<a href="#" class="btn btn-danger btn-sm">Failed</a>';
                        }
                  }
                },
              ],
              fixedColumns: true,
              "columns": [
                  { "data": "rowid" },
                  { "data": "senderid" },
                  { "data": "tonumber" },
                  { "data": "smscount" },
                  { "data": "smscontent" },
                  { "data": "submittedat" },
                  { "data": "sms_type" },
                  { "data": "status" },
              ]
          } );
    });


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

      // if ($('.dataTables_empty').text() == "Loading...") {
      //   $('.dataTables_empty').text('No Record Found');
      // }

      $('body').on('click','.getclietCampaignsmsreport', function(){
        
        clientCampaignSmsSendReport();
        /*if (document.querySelector('table tbody > tr > td').className == "dataTables_empty") {
          
          setTimeout(() => $('.dataTables_empty').text('No Record Found'), 5000);
        }
        */
      });

      $('body').on('click','.getclietsmsreport', function(){
        
        clientSmsSendReport();
        /*if (document.querySelector('table tbody > tr > td').className == "dataTables_empty") {
          
          setTimeout(() => $('.dataTables_empty').text('No Record Found'), 5000);
        }
        */
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
                let id = full[0];
                let name = full[1];
                let mobile = full[4];
                $.ajax({
                  url: "{{route('reseller-client-assigned-senderids')}}",
                  type: "post",
                  data: {
                    clientid: id
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
                                      { return '<a href="reseller-client-profile/'+full[0]+'/index" class="btn btn-sm btn-icon btn-pure btn-default rootuseredtfrm" data-original-title="edit" data-id="'+full[0]+'"><i class="icon icon-pencil" aria-hidden="true"></i></a>\n\
                                        <a href="#" class="btn btn-sm btn-icon btn-pure btn-default viewclientassignedsenderid totalsenderid'+full[0]+'" data-toggle="modal" data-target="#resellerassignedsenders" data-original-title="edit" data-id="'+full[0]+'" data-name="'+full[1]+'" data-mobile="'+full[4]+'"></a>\n\
                                        <a href="#" class="btn btn-sm btn-icon btn-pure btn-default viewbalance" data-toggle="modal" data-target="#clientviewbalance" data-original-title="edit" data-id="'+full[0]+'" data-name="'+full[1]+'" data-mobile="'+full[4]+'"><i class="fa fa-money" aria-hidden="true"></i></a>\n\
                                        <a href="#" class="btn btn-sm btn-icon btn-pure btn-default userloginfromreseller" data-original-title="edit" data-email="'+full[2]+'"><i class="fa fa-sign-in" aria-hidden="true"></i></a>\n\
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

       $('body').on('click','.viewclientassignedsenderid', function(e){
        e.preventDefault();
        let id = $(this).data('id');
        let name = $(this).data('name');
        let mobile = $(this).data('mobile');
        $.ajax({
          url: "{{route('reseller-client-assigned-senderids')}}",
          type: "post",
          data: {
            clientid: id
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

      $('body').on('click','.viewbalance', function(e){
        e.preventDefault();
        let id = $(this).data('id');
        let name = $(this).data('name');
        let mobile = $(this).data('mobile');
        $.ajax({
          url: "{{route('client.balance')}}",
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


      //  var renderSenderId = function() {
      //     $('#smssender').DataTable().destroy();
      //     $('#smssender').DataTable( {
      //     "order": [[ 0, "desc" ]],  
      //     "ajax": {
      //         "processing": true,
      //         "url": "{{route('rander-sms-senderid-for-reseller')}}",
      //         "dataType": 'json',
      //         "type": "get",
      //         "beforeSend": function (xhr) {
      //             }
      //         },
      //         "columnDefs":
      //         [
      //           {
      //             "visible": false,
      //             "searchable": false
      //           },
      //           {
      //             targets: [0],
      //             visible: false,
      //             searchable: false
      //           },
      //           {
      //             targets: [2],
      //             render: function (data, type, full, meta) {
      //               if (full.operator_name != null) {
      //                 {return full.operator_name.operator_name};
      //               } else {
      //                 {return 'General'};
      //               }
      //             }
      //           },
      //           {
      //             targets: [3],
      //             render: function (data, type, full, meta) {
      //               if (full.status == '1') {
      //                 {return 'Published'};
      //               } else {
      //                 {return 'Unpublished'};
      //               }
      //             }
      //           },
      //           {
      //             targets: [4],
      //             render: function (data, type, full, meta) {
      //               if (full.default == '1') {
      //                 {return 'Yes'};
      //               } else {
      //                 {return 'No'};
      //               }
      //             }
      //           },
      //         ],
      //         "columns": [
      //             { "data": "id" },
      //             { "data": "sender_name" },
      //             { "data": "operator_name" },
      //             { "data": "status" },
      //             { "data": "default" },
      //             { "data": "user" },
      //             { "data": "password" },
      //             { "data": "created_by" },
      //             { "data": "updated_by" },
      //             {
      //                 "render": function (data, type, full, meta)
      //                 { 
      //                   if (full.status == 1) {
      //                     return '<a href="#" class="btn btn-sm btn-icon btn-pure btn-default assignsenderid"  data-toggle="modal" data-target="#assignsenderid" data-original-title="edit" data-id="'+full.id+'" data-sendername="'+full.sender_name+'"><i class="fa fa-users" aria-hidden="true"></i></a>'; 
      //                   } else {
      //                     return '<span style="background-color:red; padding:5px; color:#fff;">Inactive</span>'; 
      //                   }
      //                 }
      //             },
      //         ]
      //     } );
      // }

      // renderSenderId();


      /**Clients invoices **/

      var clientProfileInvoices = function(){
      $('.clientprofileinvoicelist').DataTable({
          "order": [[ 0, "desc" ]],
          "processing": true,
          "language": {
            processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span> '
          },
          "serverSide": true,
          "ajax": { "url": "{{route('show-my-invoicelist',[@$request->userid])}}","type": "get" },
          columnDefs: [
              
            //{ className: "actions", targets: 9  },  
            {
                targets: [ 0 ],
                visible: false,
                searchable: false
            }, 
            {
                targets: [ 1 ],
                visible: false,
                searchable: false
            }, 
            {
                targets: [ 12 ],
                visible: false,
                searchable: false
            }, 
            {
                targets: [ 13 ],
                visible: false,
                searchable: false
            }, 
            {  targets: 4,
              render: function (data, type, full, meta) {
                            { return `<div>${full[4]}</div><div style="font-weight:900; font-style:italic; font-size: 11px;">Sms Type ${full[13]}</div>`; }
              }   
            }
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
      clientProfileInvoices();


      @if(Route::currentRouteName() == 'reseller-client-profile-senderid')

      var clientAssignedSenderid = function() {
          const clientid = document.querySelector('.profile-senderid');
          const getid = clientid.getAttribute('data-current_profileid');

          let url = "{{route('client-senderid-list',':id')}}";

          url = url.replace(':id', getid);
          $('.clientassignsenderid').DataTable().destroy();
          $('.clientassignsenderid').DataTable( {
              "ajax": {
                "processing": true,
                "url": url,
                "dataType": 'json',
                "type": "get",
                "beforeSend": function (xhr) {
                    }
              },
              "pageLength": 5,
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
                          return '<a href="#" data-userid="'+ full.user_id + '" data-default="'+ full.default +'" data-smssenderid="'+ full.sms_sender_id +'" style="line-height: 27px;">'+full.default+'</a>'; 
                      }
                  },
                  { "data": "created_at" },
              ],
          } );
      }

      clientAssignedSenderid();
      

      //jquery keyup delay until stop tipping

      $('.tableprofilesenderid tbody').empty();
      let getsenderid = [];
      let searchsenderid = $('.searchsenderid').val();
      let current_profileid = document.querySelector('.searchsenderid')
      current_profileid = current_profileid.getAttribute('data-current_profileid');
      $.ajax({
        url: "{{route('reseller-client-search-unassigned-senderid')}}",
        type: "post",
        data: {
          search: searchsenderid,
          userid: current_profileid
        },
        success: function(res) {
          
          $.each(res.data, (index, senderid) => {
              $('.tableprofilesenderid tbody').append(
                '<tr>'+
                  '<td><input type="checkbox" name="sms_sender_id[]" value="'+senderid.id+'" /></td>'+
                  '<td>'+senderid.sender_name+'</td>'+
                '</tr>'
              );
          })
        },
        error: function(err) {
          console.log(err);
        }
      });

      var delay = (function(){
          var timer = 0;
          return function(callback, ms){
              clearTimeout (timer);
              timer = setTimeout(callback, ms);
          };
      })();
      $('body').on('keyup','.searchsenderid', function(){
        delay(() =>{
            $('.tableprofilesenderid tbody').empty();
            let getsenderid = [];
            let searchsenderid = $(this).val();
            let current_profileid = document.querySelector('.searchsenderid')
            current_profileid = current_profileid.getAttribute('data-current_profileid');
            $.ajax({
              url: "{{route('reseller-client-search-unassigned-senderid')}}",
              type: "post",
              data: {
                search: searchsenderid,
                userid: current_profileid
              },
              success: function(res) {
                
                $.each(res.data, (index, senderid) => {
                    $('.tableprofilesenderid tbody').append(
                      '<tr>'+
                        '<td><input type="checkbox" name="sms_sender_id[]" value="'+senderid.id+'" /></td>'+
                        '<td>'+senderid.sender_name+'</td>'+
                      '</tr>'
                    );
                })
              },
              error: function(err) {
                console.log(err);
              }
            });
        },1000);
        
      
      });

      @endif

      var clientTemplate = function(){
      $('.clienttemplate').DataTable().destroy();
      $('.activedeal').css({'background-color':'#3e8ef7','color':'#fff'});    
      $('.clienttemplate').DataTable({
          "order": [[ 0, "desc" ]],
          "processing": true,
          "language": {
            processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span> '
          },

          "serverSide": true,
          "ajax": { "url": "{{route('rander-client-template',[@$request->userid])}}","type": "get" },
          columnDefs: [
            {
                targets: [8],
                visible: false,
                searchable: false
            },
            {
                targets: [9],
                visible: false,
                searchable: false
            },
            {
                targets: [2],
                render: function (data, type, full, meta) {
                  {
                    return `<div>
                        ${full[2]}
                        <div style="font-style: italic; font-weight: 900; font-size:10px;">BTRC File: <a href="https://login.smsinbd.com/storage/app/public/templateContent/${full[8]}">${full[8]}</a></div>
                        <div style="font-style: italic; font-weight: 900; font-size:10px;">BTRC File Status: ${full[9] == 1 ? "true" : "false" }</div>
                    </div>`
                  }
                }
            },
            {  targets: -1,
              render: function (data, type, full, meta) {
                    {
                      return '<a href="#" class="btn btn-sm btn-icon btn-pure btn-default templateeditform"  data-toggle="modal" data-target="#smsRootTemplate" data-original-title="edit" data-id="'+full[0]+'" data-templatetitle="'+full[1]+'" data-templatedesc="'+full[2]+'" data-templateowner="'+full[3]+'" data-usertype="'+full[4]+'" data-status="'+full[5]+'"><i class="icon icon-pencil" aria-hidden="true"></i></a>';

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
              null,
              null,
              null,
              null,
              { "sWidth":"60px","sClass": "actions" }
          ],
          oLanguage: {sProcessing: '<div class="loader vertical-align-middle loader-rotate-plane" style="background-color: #2e688e; "></div>'}

        });
      }
      clientTemplate();



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
              "order": [[ 0, "desc" ]],
              "processing": true,
              "language": {
                processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span> '
              },
              "serverSide": true,
              "ajax": {
                "url": "{{route('reseller-clients-sms-send-data')}}",
                "type": "get",
                "data": {
                  fromdate: $('#from_date').val(),
                  todate: $('#to_date').val(),
                  userid: $('#userid').val()
                },
                "beforeSend": function (xhr) {
                  
                },
              },
              columnDefs:
              [ 
                {
                targets: [0],
                    visible: false,
                    searchable: false
                },
                {
                targets: [3],
                    visible: false,
                    searchable: false
                },
                {
                targets: [12],
                    visible: false,
                    searchable: false
                },
                {
                    targets: [5],
                    render: function(data, type, full, meta) {
                      return '<a href="#" class="viewsmsdetails" style="color:blue;" data-toggle="modal" data-target="#viewsmsdetails" data-submittedat="'+full[10]+'" data-contact="'+full[5]+'" data-remarks="'+full[1]+'" data-content="'+full[12]+'">'+full[5]+'</a>';
                    }
                },
                {
                    targets: [11],
                    render: function(data, type, full, meta) {
                        if (full[11] == 1)
                        {
                          return '<a href="#" class="btn btn-success btn-sm">Delivered</a>'; 
                        } else {
                          return '<a href="#" class="btn btn-danger btn-sm">Failed</a>';
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
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                //{ "sWidth":"60px","sClass": "actions" }
            ],
            oLanguage: {sProcessing: '<div class="loader vertical-align-middle loader-rotate-plane" style="background-color: #2e688e; "></div>'}
          } );


      }

      clientSmsSendReport();


      var clientCampaignSmsSendReport = function() {
          $.ajax({
            url: "{{route('reseller-client-total-sms-send')}}",
            type: "get",
            data: {
              fromdate: $('#from_date').val(),
              todate: $('#to_date').val(),
              userid: $('#userid').val()
            },
            success: function(res) {
              $('.totalsendsms').css({'margin-top':'20px'});
              $('.totalsendsms').text(`Total SMS sent: ${res}`);
              $('.totalsendsms').addClass('font-bold');
            }
          });

          $.ajax({
            url: "{{route('reseller-client-total-sms-campaign')}}",
            type: "get",
            data: {
              fromdate: $('#from_date').val(),
              todate: $('#to_date').val(),
              userid: $('#userid').val()
            },
            success: function(res) {
              $('.totalcampaign').css({'margin-right':'20px','margin-top':'20px'});
              $('.totalcampaign').text(`Total Campaign: ${res}`+'  ');
              $('.totalcampaign').addClass('font-bold');
            }
          });
          $('.clientcampaignsmsreport').DataTable().destroy();
          
          $('.clientcampaignsmsreport').DataTable( {
              "order": [[ 0, "desc" ]],
              "processing": true,
              "language": {
                processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span> '
              },
              "serverSide": true,
              "ajax": {
                "url": "{{route('reseller-client-campaign-send-data')}}",
                "type": "get",
                "data": {
                  fromdate: $('#from_date').val(),
                  todate: $('#to_date').val(),
                  userid: $('#userid').val()
                },
                "beforeSend": function (xhr) {
                  
                },
              },
              columnDefs:
              [ 
                
                {
                targets: [9],
                    visible: false,
                    searchable: false
                },
                {
                    targets: [0],
                    render: function(data, type, full, meta) {
                      return '<a href="#" class="viewsmsdetails" style="color:blue;" data-toggle="modal" data-target="#viewsmsdetails" data-submittedat="'+full[7]+'" data-remarks="'+full[0]+'" data-content="'+full[9]+'">'+full[0]+'</a>';
                    }
                },
                {
                    targets: [8],
                    render: function(data, type, full, meta) {
                      return `<a href="#" class="btn btn-success btn-sm campaignstatus" data-toggle="modal" data-target="#viewcampaignmobile" data-remarks="${full[0]}"><i class="fa fa-eye"></i></a>
                      <a href="/export-excel?remarks=${full[0]}" target="_self" class="btn btn-success btn-sm exportexcel" data-remarks="${full[0]}"><i class="fa fa-file-excel-o"></i></a>`; 
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
                //{ "sWidth":"60px","sClass": "actions" }
            ],
            oLanguage: {sProcessing: '<div class="loader vertical-align-middle loader-rotate-plane" style="background-color: #2e688e; "></div>'}
          } );
      }

      clientCampaignSmsSendReport();


      var clientArchiveCampaignSmsSendReport = function() {
          $.ajax({
            url: "{{route('reseller-client-archive-total-sms-send')}}",
            type: "get",
            data: {
              fromdate: $('#from_date').val(),
              todate: $('#to_date').val(),
              userid: $('#userid').val()
            },
            success: function(res) {
              $('.totalsendsmsarchive').css({'margin-top':'20px'});
              $('.totalsendsmsarchive').text(`Total SMS sent: ${res}`);
              $('.totalsendsmsarchive').addClass('font-bold');
            }
          });

          $.ajax({
            url: "{{route('reseller-client-archive-total-sms-campaign')}}",
            type: "get",
            data: {
              fromdate: $('#from_date').val(),
              todate: $('#to_date').val(),
              userid: $('#userid').val()
            },
            success: function(res) {
              $('.totalcampaignarchive').css({'margin-right':'20px','margin-top':'20px'});
              $('.totalcampaignarchive').text(`Total Campaign: ${res}`+'  ');
              $('.totalcampaignarchive').addClass('font-bold');
            }
          });
          $('.clientarchivecampaignsmsreport').DataTable().destroy();
          
          $('.clientarchivecampaignsmsreport').DataTable( {
              "order": [[ 0, "desc" ]],
              "processing": true,
              "language": {
                processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span> '
              },
              "serverSide": true,
              "ajax": {
                "url": "{{route('reseller-client-archive-campaign-send-data')}}",
                "type": "get",
                "data": {
                  fromdate: $('#from_date').val(),
                  todate: $('#to_date').val(),
                  userid: $('#userid').val()
                },
                "beforeSend": function (xhr) {
                  
                },
              },
              columnDefs:
              [ 
                
                {
                targets: [9],
                    visible: false,
                    searchable: false
                },
                {
                targets: [7],
                    visible: false,
                    searchable: false
                },
                {
                targets: [2],
                    visible: false,
                    searchable: false
                },
                {
                    targets: [0],
                    render: function(data, type, full, meta) {
                      return `<a href="#" class="viewsmsdetails" style="color:blue;" data-toggle="modal" data-target="#viewsmsdetails" data-submittedat="${full[7]}" data-remarks="${full[0]}" data-content="${full[9]}">${full[0]}</a>
                      <div>
                        <span style="font-weight:600; font-size:11px; font-style:italic;">Date: ${full[7]}</span>
                      </div>
                      <div>
                      <span style="font-weight:600; font-size:11px; font-style:italic;">Sms Type: ${full[2]}</span>
                      </div>`;
                    }
                },
                {
                    targets: [8],
                    render: function(data, type, full, meta) {
                      return `<a href="#" class="btn btn-success btn-sm campaignstatus" data-toggle="modal" data-target="#viewcampaignmobile" data-remarks="${full[0]}"><i class="fa fa-eye"></i></a>
                      <a href="/export-archive-sms-excel?remarks=${full[0]}" target="_self" class="btn btn-success btn-sm exportexcel" data-remarks="${full[0]}"><i class="fa fa-file-excel-o"></i></a>`; 
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
                //{ "sWidth":"60px","sClass": "actions" }
            ],
            oLanguage: {sProcessing: '<div class="loader vertical-align-middle loader-rotate-plane" style="background-color: #2e688e; "></div>'}
          } );
      }

      clientArchiveCampaignSmsSendReport();


      $('body').on('click','.campaignstatus', function(e){
        let remarks = $(this).data('remarks');
          $('.campaignmobile').DataTable().destroy();
          $('.campaignmobile').DataTable( {
          "order": [[ 0, "desc" ]],
          "ajax": {
              "processing": true,
              "url": "{{route('root-client-campaign-mobile-list')}}",
              "dataType": 'json',
              "type": "post",
              "data": {
                  remarks: remarks
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
                
                {
                  targets: [7],
                  render: function (data, type, full, meta) {
                    if (full.status == '1') {
                          return '<a href="#" class="btn btn-success btn-sm">Delivered</a>'; 
                        } else {
                          return '<a href="#" class="btn btn-danger btn-sm">Failed</a>';
                        }
                  }
                },
              ],
              fixedColumns: true,
              "columns": [
                  { "data": "rowid" },
                  { "data": "senderid" },
                  { "data": "tonumber" },
                  { "data": "smscount" },
                  { "data": "smscontent" },
                  { "data": "submittedat" },
                  { "data": "sms_type" },
                  { "data": "status" },
              ]
          } );
    });


    $('body').on('click','.archivecampaignstatus', function(e){
        let remarks = $(this).data('remarks');
          $('.campaignmobile').DataTable().destroy();
          $('.campaignmobile').DataTable( {
          "order": [[ 0, "desc" ]],
          "ajax": {
              "processing": true,
              "url": "{{route('root-client-archive-campaign-mobile-list')}}",
              "dataType": 'json',
              "type": "post",
              "data": {
                  remarks: remarks
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
                
                {
                  targets: [7],
                  render: function (data, type, full, meta) {
                    if (full.status == '1') {
                          return '<a href="#" class="btn btn-success btn-sm">Delivered</a>'; 
                        } else {
                          return '<a href="#" class="btn btn-danger btn-sm">Failed</a>';
                        }
                  }
                },
              ],
              fixedColumns: true,
              "columns": [
                  { "data": "rowid" },
                  { "data": "senderid" },
                  { "data": "tonumber" },
                  { "data": "smscount" },
                  { "data": "smscontent" },
                  { "data": "submittedat" },
                  { "data": "sms_type" },
                  { "data": "status" },
              ]
          } );
    });

      // if ($('.dataTables_empty').text() == "Loading...") {
      //   $('.dataTables_empty').text('No Record Found');
      // }

      $('body').on('click','.getclietArchiveCampaignsmsreport', function(){
        
        clientArchiveCampaignSmsSendReport();
        /*if (document.querySelector('table tbody > tr > td').className == "dataTables_empty") {
          
          setTimeout(() => $('.dataTables_empty').text('No Record Found'), 5000);
        }
        */
      });
      

      $('body').on('click','.getclietCampaignsmsreport', function(){
        
        clientCampaignSmsSendReport();
        /*if (document.querySelector('table tbody > tr > td').className == "dataTables_empty") {
          
          setTimeout(() => $('.dataTables_empty').text('No Record Found'), 5000);
        }
        */
      });

      $('body').on('click','.getresellerclietsmsreport', function(){
        
        clientSmsSendReport();
        // if (document.querySelector('table tbody > tr > td').className == "dataTables_empty") {
          
        //   setTimeout(() => $('.dataTables_empty').text('No Record Found'), 5000);
        // }
      });

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
         alert();
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
              url: `/delete-reseller-client-senderid/${assign_user_senderid}/${sms_sender_id}`,
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
                      '<td>'+product.rate+'</td>'+
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
        if ( $('#paymentoption').val() == 'debit') {
          alert('Vat is not applicable with debit voucher');
          $(this).val(0);
          $('#paymentoption').focus();
          return false;
        }

        if ( $('#paymentoption').val() == '') {
          alert('Please select voucher type first');
          $(this).val(0);
          $('#paymentoption').focus();
          return false;
        }

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
                rate: product.rate,        
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

      $('body').on('keyup','#price',function(){

        /*if ($('#smsqty').val() == "") {
          $('#smsqty').focus();
        }*/

        //let invoicePrice = parseInt($('#smsqty').val())*parseFloat($('#rate').val());
        let invoiceQty = Math.round((parseFloat($('#price').val())/parseFloat($('#rate').val())));
        if ($('#rate').val() == '')
        {
          $('#smsqty').val(0);
          $('.smsqty').text(0);
        } else {
          $('#smsqty').val(Math.round(invoiceQty));
          $('.smsqty').text(Math.round(invoiceQty));
        }

        let storeProduct = localStorage.getItem('saleItem');
        let productInCart = JSON.parse(storeProduct);
        let productarr = [];
        if(localStorage.getItem('saleItem'))
        {
          productInCart.forEach(async (product,index) => {
              if (product.sms_type == $('#sms_type').val()) {
                await productarr.push({
                  client: product.client,
                  invoice_date: product.invoice_date,
                  sms_type: product.sms_type,
                  user_type: product.user_type,
                  smsqty: Math.round(parseFloat($('#price').val())/parseFloat($('#rate').val())),//product.smsqty, 
                  rate: parseFloat($('#rate').val()),              
                  price: parseFloat(('#price').val()),//invoicePrice,
                  validity_date: product.validity_date,
                  invoice_vat: parseFloat(product.invoice_vat),
                  paymentoption: $('#paymentoption').val(),
                  paymentby: $('#paymentby').val(),
                  remarks: $('#remarks').val()
                });
                await localStorage.setItem('saleItem', JSON.stringify(productarr));
              }
          });
        }

      });


      $('body').on('change','#paymentoption',function(){

        if ($('#invoice_vat').val() == "" && $('#paymentoption').val() == 'cash') {
          $('#invoice_vat').focus();
        }

        if ($('#paymentoption').val() == '' || $('#paymentoption').val() == 'debit') {
          invoiceVat = 0;
          $('.invoicevat').text(invoiceVat);
          $('#invoice_vat').val(0);
          let grandtotal = addCommas(invoiceTotal-invoiceVat);
          $('.grandtotal').text(grandtotal);
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
                rate: product.rate,       
                price: product.price,
                validity_date: product.validity_date,
                invoice_vat: $('#invoice_vat').val(),
                paymentoption: $('#paymentoption').val(),
                paymentby: $('#paymentby').val(),
                remarks: $('#remarks').val()
              });
              await localStorage.setItem('saleItem', JSON.stringify(productarr));
          });
        }

      });

      $('body').on('change','#paymentby',function(){

          if ($('#invoice_vat').val() == "" && $('#paymentoption').val() == 'cash') {
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
                  rate: product.rate,             
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
          if ($('#invoice_vat').val() == "" && $('#paymentoption').val() == 'cash') {
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
                  rate: product.rate,               
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

            /*if ($('#smsqty').val() == "")
            {
              document.querySelector('#smsqty').focus();
              document.querySelector('.smsqtyerr').style.display = 'block';
              //$('.invoicedateerr').css({'display':'block'});
              return false;
            } else {
              document.querySelector('.smsqtyerr').style.display = 'none';
            }

            if ($('#smsqty').val() == 0)
            {
              document.querySelector('#smsqty').focus();
              document.querySelector('.smsqtyerr').style.display = 'block';
              //$('.invoicedateerr').css({'display':'block'});
              return false;
            } else {
              document.querySelector('.smsqtyerr').style.display = 'none';
            }
            */

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

            /*if ($('#smsqty').val() == "")
            {
              document.querySelector('#smsqty').focus();
              document.querySelector('.smsqtyerr').style.display = 'block';
              //$('.invoicedateerr').css({'display':'block'});
              return false;
            } else {
              document.querySelector('.smsqtyerr').style.display = 'none';
            }*/

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
                    let smsqty = parseFloat(product.smsqty);
                    let price = parseFloat(product.price);
                    smsqty += parseFloat($('#smsqty').val());

                    price += parseFloat($('#price').val());//parseFloat($('#smsqty').val())*parseFloat($('#rate').val());

                    product.smsqty = parseFloat(smsqty);
                    product.price = parseFloat(price);

                    f = 1;
                  }

              });

              if (f == 0) {
                productInCart.push({
                  client: $('#invoice_client').val(),
                  invoice_date: $('#invoice_date').val(),
                  sms_type: $('#sms_type').val(),
                  user_type: $('#user_type').val(),
                  smsqty: Math.round(parseFloat($('#price').val())/parseFloat($('#rate').val())), 
                  rate: parseFloat($('#rate').val()),              
                  price: parseFloat($('#price').val()),
                  validity_date: $('#validity_date').val(),
                  invoice_vat: 0,
                  paymentoption: $('#paymentoption').val(),
                  paymentby: $('#paymentby').val(),
                  remarks: $('#remarks').val()
                });

              }
              localStorage.setItem('saleItem', JSON.stringify(productInCart));

              $('.carttable tbody').empty();
              let i = 1;
              let invoiceTotal = 0;
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
                            '<td>'+Math.round(product.smsqty)+'</td>'+
                            '<td>'+product.rate+'</td>'+
                            '<td>'+Math.round(product.price)+'</td>'+
                            '<td class="text-center">'+
                                //'<a href="#" class="icon icon-pencil btn edt-currency" data-transid="" data-smstype="'+product.sms_type+'" data-smsqty="'+product.smsqty+'" data-price="'+product.price+'" data-validity_date="'+product.validity_date+'"></a> '+
                                '<a href="#" data-recindex="'+index+'" class="icon icon-trash btn dlt-prdb" data-id=""></a>'+
                            '</td>'+
                        '</tr>');

                        i++;
              });
              
            } else {

                salearr.push({
                  client: $('#invoice_client').val(),
                  invoice_date: $('#invoice_date').val(),
                  sms_type: $('#sms_type').val(),
                  user_type: $('#user_type').val(),
                  smsqty: Math.round(parseFloat($('#price').val())/parseFloat($('#rate').val())), //parseFloat($('#smsqty').val()),               
                  rate: parseFloat($('#rate').val()),              
                  price: parseFloat($('#price').val()),//parseFloat($('#smsqty').val())*parseFloat($('#rate').val()),
                  validity_date: $('#validity_date').val(),
                  invoice_vat: 0,
                  paymentoption: $('#paymentoption').val(),
                  paymentby: $('#paymentby').val(),
                  remarks: $('#remarks').val()
                });

                localStorage.setItem('saleItem', JSON.stringify(salearr));

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
                              '<td>'+Math.round(product.smsqty)+'</td>'+
                              '<td>'+product.rate+'</td>'+
                              '<td>'+Math.round(product.price)+'</td>'+
                              '<td class="text-center">'+
                                  //'<a href="#" class="icon icon-pencil btn edt-currency" data-transid="" data-smstype="'+product.sms_type+'" data-smsqty="'+product.smsqty+'" data-price="'+product.price+'" data-validity_date="'+product.validity_date+'"></a> '+
                                  '<a href="#" data-recindex="'+index+'" class="icon icon-trash btn dlt-prdb" data-id=""></a>'+
                              '</td>'+
                          '</tr>');

                          i++;
                });
              
            }

            $('#smsqty').val('');
            $('#rate').val('');
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

      // reseller-sms-saleto-client
      $('body').on('click','.save-invoice', function(){
        let storeItem = localStorage.getItem('saleItem');
        let productInCart = JSON.parse(storeItem);
        savprdarr = [];

        if ($('#invoice_vat').val() == "" && $('#paymentoption').val() == 'cash')
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

        /*if ($('#paymentoption').val() != 'cash')
        {
          document.querySelector('#paymentby').focus();
          document.querySelector('.paymentbyerr').style.display = 'block';
          return false;
        } else {
          document.querySelector('.paymentbyerr').style.display = 'none';
        }*/

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
                rate: product.rate,               
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

      /** reseller's Clients invoice **/

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
            {
                targets: [ 1 ],
                visible: false,
                searchable: false
            }, 
            {
                targets: [ 12 ],
                visible: false,
                searchable: false
            }, 
            {
                targets: [ 13 ],
                visible: false,
                searchable: false
            }, 
            {  targets: 4,
              render: function (data, type, full, meta) {
                            { return `<div>${full[4]}</div><div style="font-weight:900; font-style:italic; font-size: 11px;">Sms Type ${full[13]}</div>`; }
              }   
            },
            {  targets: -1,
              render: function (data, type, full, meta) {
                                      { return '<a href="#" class="btn btn-sm btn-icon btn-pure btn-default rootuserdtl" data-toggle="tooltip" data-original-title="Remove" data-id="'+full[0]+'"><i class="icon icon-trash" aria-hidden="true"></i></a>'; }
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
              null,
              { "sWidth":"60px","sClass": "actions" }
          ],
          oLanguage: {sProcessing: '<div class="loader vertical-align-middle loader-rotate-plane" style="background-color: #2e688e; "></div>'}

        });
      }
      resellerClientInvoices();

      /** reseller's Clients invoice **/

      var resellerMyInvoicesList = function(){
      $('.resellermyinvoicelist').DataTable({
          "order": [[ 0, "desc" ]],
          "processing": true,
          "language": {
            processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span> '
          },
          "serverSide": true,
          "ajax": { "url": "show-my-reseller-invoicelist","type": "get" },
          columnDefs: [
              
            //{ className: "actions", targets: 9  },  
            {
                targets: [ 0 ],
                visible: false,
                searchable: false
            }, 
            {
                targets: [ 1 ],
                visible: false,
                searchable: false
            }, 
            {
                targets: [ 12 ],
                visible: false,
                searchable: false
            }, 
            {
                targets: [ 13 ],
                visible: false,
                searchable: false
            }, 
            {  targets: 4,
              render: function (data, type, full, meta) {
                            { return `<div>${full[4]}</div><div style="font-weight:900; font-style:italic; font-size: 11px;">Sms Type ${full[13]}</div>`; }
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
      resellerMyInvoicesList();



     })(document, window, jQuery);
   </script>

@elseif(Auth::guard('web')->check())

<script type="text/javascript">
  (function(document, window, $){

    const currentrout = document.querySelector('.currentrout');

    if (currentrout.getAttribute('data-current_route') === "client") {
      return false;
    }

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
              "order": [[ 0, "desc" ]],
              "processing": true,
              "language": {
                processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span> '
              },
              "serverSide": true,
              "ajax": {
                "url": "{{route('client-sms-send-data')}}",
                "type": "get",
                "data": {
                  fromdate: $('#from_date').val(),
                  todate: $('#to_date').val(),
                  userid: $('#userid').val()
                },
                "beforeSend": function (xhr) {
                  
                },
              },
              columnDefs:
              [ 
                {
                targets: [0],
                    visible: false,
                    searchable: false
                },
                {
                targets: [3],
                    visible: false,
                    searchable: false
                },
                {
                targets: [12],
                    visible: false,
                    searchable: false
                },
                {
                    targets: [5],
                    render: function(data, type, full, meta) {
                      return '<a href="#" class="viewsmsdetails" style="color:blue;" data-toggle="modal" data-target="#viewsmsdetails" data-submittedat="'+full[10]+'" data-contact="'+full[5]+'" data-remarks="'+full[1]+'" data-content="'+full[12]+'">'+full[5]+'</a>';
                    }
                },
                {
                    targets: [11],
                    render: function(data, type, full, meta) {
                        if (full[11] == 1)
                        {
                          return '<a href="#" class="btn btn-success btn-sm">Delivered</a>'; 
                        } else {
                          return '<a href="#" class="btn btn-danger btn-sm">Failed</a>';
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
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                //{ "sWidth":"60px","sClass": "actions" }
            ],
            oLanguage: {sProcessing: '<div class="loader vertical-align-middle loader-rotate-plane" style="background-color: #2e688e; "></div>'}
          } );
      }

      clientSmsSendReport();


      var userCampaignSmsSendReport = function() {
          $.ajax({
            url: "{{route('client-total-sms-send')}}",
            type: "get",
            data: {
              fromdate: $('#from_date').val(),
              todate: $('#to_date').val(),
              userid: $('#userid').val()
            },
            success: function(res) {
              $('.totalsendsms').css({'margin-top':'20px'});
              $('.totalsendsms').text(`Total SMS sent: ${res}`);
              $('.totalsendsms').addClass('font-bold');
            }
          });

          $.ajax({
            url: "{{route('client-total-sms-campaign')}}",
            type: "get",
            data: {
              fromdate: $('#from_date').val(),
              todate: $('#to_date').val(),
              userid: $('#userid').val()
            },
            success: function(res) {
              $('.totalcampaign').css({'margin-right':'20px','margin-top':'20px'});
              $('.totalcampaign').text(`Total Campaign: ${res}`+'  ');
              $('.totalcampaign').addClass('font-bold');
            }
          });
          
      }

      userCampaignSmsSendReport();

      $('body').on('click','.resendsms', function(){
        const remarks = $(this).data('remarks');
        const content = $(this).data('content');
        const sender = $(this).data('sender');

        $('#resendcontent').val(content);
        $('#remarks').val(remarks);
        $('#sender').val(sender);
      });

      var clientArchiveCampaignSmsSendReport = function() {
          $.ajax({
            url: "{{route('client-archive-total-sms-send')}}",
            type: "get",
            data: {
              fromdate: $('#from_date').val(),
              todate: $('#to_date').val(),
              userid: $('#userid').val()
            },
            success: function(res) {
              $('.totalsendsmsarchive').css({'margin-top':'20px'});
              $('.totalsendsmsarchive').text(`Total SMS sent: ${res}`);
              $('.totalsendsmsarchive').addClass('font-bold');
            }
          });

          $.ajax({
            url: "{{route('client-archive-total-sms-campaign')}}",
            type: "get",
            data: {
              fromdate: $('#from_date').val(),
              todate: $('#to_date').val(),
              userid: $('#userid').val()
            },
            success: function(res) {
              $('.totalcampaignarchive').css({'margin-right':'20px','margin-top':'20px'});
              $('.totalcampaignarchive').text(`Total Campaign: ${res}`+'  ');
              $('.totalcampaignarchive').addClass('font-bold');
            }
          });
          $('.clientcampaignsmsreport').DataTable().destroy();
          
          $('.clientcampaignsmsreport').DataTable( {
              "order": [[ 0, "desc" ]],
              "processing": true,
              "language": {
                processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span> '
              },
              "serverSide": true,
              "ajax": {
                "url": "{{route('campaign-archive-send-data')}}",
                "type": "get",
                "data": {
                  fromdate: $('#from_date').val(),
                  todate: $('#to_date').val(),
                  userid: $('#userid').val()
                },
                "beforeSend": function (xhr) {
                  
                },
              },
              columnDefs:
              [ 
                
                {
                targets: [9],
                    visible: false,
                    searchable: false
                },
                {
                targets: [7],
                    visible: false,
                    searchable: false
                },
                {
                targets: [2],
                    visible: false,
                    searchable: false
                },
                {
                    targets: [0],
                    render: function(data, type, full, meta) {
                      return `<a href="#" class="viewsmsdetails" style="color:blue;" data-toggle="modal" data-target="#viewsmsdetails" data-submittedat="${full[7]}" data-remarks="${full[0]}" data-content="${full[9]}">${full[0]}</a>
                      <div>
                        <span style="font-weight:600; font-size:11px; font-style:italic;">Date: ${full[7]}</span>
                      </div>
                      <div>
                      <span style="font-weight:600; font-size:11px; font-style:italic;">Sms Type: ${full[2]}</span>
                      </div>`;
                    }
                },
                {
                    targets: [8],
                    render: function(data, type, full, meta) {
                      return `<a href="#" class="btn btn-success btn-sm archivecampaignstatus" data-toggle="modal" data-target="#viewcampaignmobile" data-remarks="${full[0]}"><i class="fa fa-eye"></i></a>
                      <a href="/export-archive-sms-excel?remarks=${full[0]}" target="_self" class="btn btn-success btn-sm exportexcel" data-remarks="${full[0]}"><i class="fa fa-file-excel-o"></i></a>`; 
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
                //{ "sWidth":"60px","sClass": "actions" }
            ],
            oLanguage: {sProcessing: '<div class="loader vertical-align-middle loader-rotate-plane" style="background-color: #2e688e; "></div>'}
          } );
      }

      clientArchiveCampaignSmsSendReport();


      $('body').on('click','.campaignstatus', function(e){
        let remarks = $(this).data('remarks');
          $('.campaignmobile').DataTable().destroy();
          $('.campaignmobile').DataTable( {
          "order": [[ 0, "desc" ]],
          "ajax": {
              "processing": true,
              "url": "{{route('root-client-campaign-mobile-list')}}",
              "dataType": 'json',
              "type": "post",
              "data": {
                  remarks: remarks
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
                
                {
                  targets: [7],
                  render: function (data, type, full, meta) {
                    if (full.status == '1') {
                          return '<a href="#" class="btn btn-success btn-sm">Delivered</a>'; 
                        } else {
                          return '<a href="#" class="btn btn-danger btn-sm">Failed</a>';
                        }
                  }
                },
              ],
              fixedColumns: true,
              "columns": [
                  { "data": "rowid" },
                  { "data": "senderid" },
                  { "data": "tonumber" },
                  { "data": "smscount" },
                  { "data": "smscontent" },
                  { "data": "submittedat" },
                  { "data": "sms_type" },
                  { "data": "status" },
              ]
          } );
    });


    $('body').on('click','.archivecampaignstatus', function(e){
        let remarks = $(this).data('remarks');
          $('.campaignmobile').DataTable().destroy();
          $('.campaignmobile').DataTable( {
          "order": [[ 0, "desc" ]],
          "ajax": {
              "processing": true,
              "url": "{{route('root-client-archive-campaign-mobile-list')}}",
              "dataType": 'json',
              "type": "post",
              "data": {
                  remarks: remarks
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
                
                {
                  targets: [7],
                  render: function (data, type, full, meta) {
                    if (full.status == '1') {
                          return '<a href="#" class="btn btn-success btn-sm">Delivered</a>'; 
                        } else {
                          return '<a href="#" class="btn btn-danger btn-sm">Failed</a>';
                        }
                  }
                },
              ],
              fixedColumns: true,
              "columns": [
                  { "data": "rowid" },
                  { "data": "senderid" },
                  { "data": "tonumber" },
                  { "data": "smscount" },
                  { "data": "smscontent" },
                  { "data": "submittedat" },
                  { "data": "sms_type" },
                  { "data": "status" },
              ]
          } );
    });


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

      // if ($('.dataTables_empty').text() == "Loading...") {
      //   $('.dataTables_empty').text('No Record Found');
      // }

      $('body').on('click','.getclietArchiveCampaignsmsreport', function(){
        
        clientArchiveCampaignSmsSendReport();
        /*if (document.querySelector('table tbody > tr > td').className == "dataTables_empty") {
          
          setTimeout(() => $('.dataTables_empty').text('No Record Found'), 5000);
        }
        */
      });

      $('body').on('click','.getclietCampaignsmsreport', function(){
        
        userCampaignSmsSendReport();
        /*if (document.querySelector('table tbody > tr > td').className == "dataTables_empty") {
          
          setTimeout(() => $('.dataTables_empty').text('No Record Found'), 5000);
        }
        */
      });

      $('body').on('click','.getclietsmsreport', function(){
        
        clientSmsSendReport();
        /*if (document.querySelector('table tbody > tr > td').className == "dataTables_empty") {
          
          setTimeout(() => $('.dataTables_empty').text('No Record Found'), 5000);
        }
        */
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
          
          $('.clientsmscountreport').DataTable( {
          "ajax": {
                "processing": true,
                "url": "{{route('clients-send-sms-consulate-rpt')}}",
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

        




            

            $("#file").fileinput({ 

                uploadUrl: '#', // you must set a valid URL here else you will get an error

                allowedFileExtensions : ['txt','xls','xlsx','csv','pdf'],



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

            var clientTemplate = function(){
            $('.clienttemplate').DataTable().destroy();
            $('.activedeal').css({'background-color':'#3e8ef7','color':'#fff'});    
            $('.clienttemplate').DataTable({
                "order": [[ 0, "desc" ]],
                "processing": true,
                "language": {
                  processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span> '
                },
      
                "serverSide": true,
                "ajax": { "url": "{{route('rander-client-template',[@$request->userid])}}","type": "get" },
                columnDefs: [
                  {
                      targets: [8],
                      visible: false,
                      searchable: false
                  },
                  {
                      targets: [9],
                      visible: false,
                      searchable: false
                  },
                  {
                      targets: [2],
                      render: function (data, type, full, meta) {
                        {
                          return `<div>
                              ${full[2]}
                              <div style="font-style: italic; font-weight: 900; font-size:10px;">BTRC File: <a href="https://login.smsinbd.com/storage/app/public/templateContent/${full[8]}">${full[8]}</a></div>
                              <div style="font-style: italic; font-weight: 900; font-size:10px;">BTRC File Status: ${full[9] == 1 ? "true" : "false" }</div>
                          </div>`
                        }
                      }
                  },
                  {  targets: -1,
                    render: function (data, type, full, meta) {
                          { 
                            return '<a href="#" class="btn btn-sm btn-icon btn-pure btn-default templateclienteditform"  data-toggle="modal" data-target="#smsClientTemplate" data-original-title="edit" data-id="'+full[0]+'" data-templatetitle="'+full[1]+'" data-templatedesc="'+full[2]+'" data-templateowner="'+full[3]+'" data-usertype="'+full[4]+'" data-status="'+full[5]+'" data-btrc_file_status="'+full[9]+'"><i class="icon icon-pencil" aria-hidden="true"></i></a>';

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
                    null,
                    null,
                    null,
                    null,
                    { "sWidth":"60px","sClass": "actions" }
                ],
                oLanguage: {sProcessing: '<div class="loader vertical-align-middle loader-rotate-plane" style="background-color: #2e688e; "></div>'}

              });
            }
            clientTemplate();

            $('body').on('click','.templateclienteditform', function(){
              let id = $(this).data('id');
              let templatetitle = $(this).data('templatetitle');
              let templatedesc = $(this).data('templatedesc');
              let templateowner = $(this).data('templateowner');
              let usertype = $(this).data('usertype');
              let status = $(this).data('status');
              let btrc_file_status = $(this).data('btrc_file_status');

              if (btrc_file_status == true) {
                $('.btrcfile').css({'display':'none'});
              } else {
                $('.btrcfile').removeAttr('style');
              }

              $('#exampleModalLabel').text('Edit User Template');

              $('#id').val(id);
              $('#frmmode').val('edt');
              $('#template_desc').val(templatedesc)
              if (status == 'Active') {
                $('#senderid_status_yes').text(status);
                $('#template_title').val(templatetitle);
                $('#template_title').attr('disabled',"disabled");
                $('#template_desc').val(templatedesc)
                $('#template_desc').attr('disabled',"disabled");
              } else {
                $('#senderid_status_yes').text(status);
                $('#template_title').val(templatetitle);
                $('#template_title').attr('disabled',false);
                $('#template_desc').val(templatedesc)
                $('#template_desc').attr('disabled',false);
              } 
            });

            $('#smsClientTemplate').on('hidden.bs.modal', function(){
              $('#exampleModalLabel').text('Add User Template');
              $('#frmmode').val('ins');
              $('#id').val('');
              $('#template_title').val('');
              $('#template_desc').val('');
              $('#template_title').attr('disabled',false);
              $('#template_desc').attr('disabled',false);
              $('#senderid_status_yes').text('Inactive');
            });


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


            

            if ($('#numbertypeupload').prop('checked') != true) {
              
              $('#template').attr('disabled','disabled');

            }


            

            $('body').on('change','#template', function(){
              const tempid = $(this).val();

              const routeurl = '{{route("template-approved-content",":tempid")}}';

              const url = routeurl.replace(':tempid',tempid);

              $.ajax({
                url: url,
                type: "get",
                success: function(res) {
                  $('#msgcontent').val(res.template_desc);
                },
                error: function(err) {
                  $('#msgcontent').val('');
                }
              });
            });


            //smpp dipping

            $('body').on('click', '.resendsmsbtn', function(){
              

              let url ="{{route('resend-failed-sms-messages')}}";
              $.ajax({
                url: url,
                type: "post",
                data: {
                  remarks: $('#remarks').val(),
                  message: $('#resendcontent').val(),
                  sender: $('#sender').val()
                },
                beforeSend: function()
                {
                  $('.failedsmsstatus').text('Processing....');

                },
                success: function(res)
                {
                  $('.setupsms').css({'color':'green'}).text(res.msg);
                  setTimeout(function(){
                    $('.failedsmsstatus').css({'display':'none'});
                    $('#contact_number').focus();
                  },2000);
                },
                error: function(err)
                {
                  $('.failedsmsstatus').css({'color':'red','text-align':'center'}).text(err.responseJSON.errmsg);
                  setTimeout(function(){
                    $('.failedsmsstatus').css({'color':'green','text-align':'center'}).text('');
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


      /**Clients invoices **/

      var clientInvoices = function(){
      $('.rootinvoicelist').DataTable({
          "order": [[ 0, "desc" ]],
          "processing": true,
          "language": {
            processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span> '
          },
          "serverSide": true,
          "ajax": { "url": "{{route('show-my-invoicelist',[@$request->userid])}}","type": "get" },
          columnDefs: [
              
            //{ className: "actions", targets: 9  },  
            {
                targets: [ 0 ],
                visible: false,
                searchable: false
            }, 
            {
                targets: [ 1 ],
                visible: false,
                searchable: false
            }, 
            {
                targets: [ 12 ],
                visible: false,
                searchable: false
            }, 
            {
                targets: [ 13 ],
                visible: false,
                searchable: false
            }, 
            {  targets: 4,
              render: function (data, type, full, meta) {
                            { return `<div>${full[4]}</div><div style="font-weight:900; font-style:italic; font-size: 11px;">Sms Type ${full[13]}</div>`; }
              }   
            }
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
      clientInvoices();

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


@yield('dashboardScripts')

</body>
</html>
