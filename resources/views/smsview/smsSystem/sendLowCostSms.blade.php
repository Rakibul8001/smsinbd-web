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


        <div class="modal bd-example-modal-md fade" id="statusModal" tabindex="-1" role="dialog" aria-hidden="true">
          <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
              <div class="modal-header">
                
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="margin-top: -22px;">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>

              <div class="modal-body">
                <h3 id="smsStatus"></h3>
                <p class="text-success" id="successCount"></p>
                <p class="text-danger" id="failedCount"></p>
              </div>
              <div class="modal-footer">
                
                
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
              </div>
            </div>
          </div>
        </div>
       
        <!-- / main header -->
          <div class="row">
            <!-- <div class="col-md-3 col-lg-3 col-sm-12 col-3 pull-right" style="margin:25px 25px 0 0;">
            <a class="btn btn-primary btn-addon btn-md pull-right mb-5 addsenderid" style="margin-bottom:10px; z-index: 99; position:relative;" data-toggle="modal" data-target="#addContactInGroup"><i class="fa fa-plus"></i> Create New Contact</a>
            </div> -->
          </div>
          <div class="row m-t-md" style="margin:10px;">
            <div class="col-md-10">
              @if(session()->has('msg'))
                <div class="alert alert-success font-weight-bold clientsuccess" role="alert">
                  {{session()->get('msg')}}
                </div>
              @endif
              <div style="position: absolute; width:100%; height: 100%; background-color:#fff; opacity:.8; z-index:99;display: none;" class="setupsmsroot">
                <span style="top:40%; position:absolute; width:100%; text-align:center;" class="font-bold setupsms">Processing.... It may take some time. Please be patient.</span>
              </div>
              
              
              <div class="panel panel-default">
              <div class="panel-heading font-bold setupsmsform">Low Cost SMS <span class="pull-right" id="lowcostBalance">Current Low Cost SMS Balance: {{ $client->lowcost_balance }}</span></div>
                

                <?php //print_r(session('senderr')) ."<br/>"; ?>
                <?php //print_r(session('sendsuccess')); ?>
                <div class="panel-body messagesendfrm">
                <form role="form" action="#" id="smssendform" method="post" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="api_token" value="{{ $api_token }}">
                    <div class="form-group">
                      <div class="row">
                        <div class="col-md-6">
                          <label class="font-bold">Campaing  Name</label>
                        <input type="text" name="cam_name" id="cam_name" value="" class="form-control" placeholder="Enter campaing Name">
                        </div>
                      </div>
                    </div>
                    <div class="form-group">
                      <div class="row" style="border: 1px solid #ddd; margin: 5px 1px;">
                        <div class="col-md-3 col-md-offset-3">
                          <div class="radio">
                            <label class="i-checks font-bold">
                              <input type="radio" name="numbertype" id="numbertypesingle" value="single" checked>
                              <i></i>
                              Type Number
                            </label>
                          </div>
                        </div>
                        <div class="col-md-3">
                          <div class="radio">
                            <label class="i-checks font-bold">
                              <input type="radio" name="numbertype" id="numbertypegroup" value="contgroup">
                              <i></i>
                              Contact Group
                            </label>
                          </div>
                        </div>
                        <div class="col-md-3">
                          <div class="radio">
                            <label class="i-checks font-bold">
                              <input type="radio" name="numbertype" id="numbertypeupload" value="uploadfile">
                              <i></i>
                              Upload File
                            </label>
                          </div>
                        </div>
                      </div>
                    </div>

                    <div class="form-group">
                      <div class="row">
                        <div class="col-md-12 contact_number">
                          <label class="font-bold">Number <span class="text-danger">*</span> (Separated by comma or space or new line)</label>
                          <textarea name="contact_number" id="contact_number" class="form-control" placeholder="01800000000 01700000000 01900000000 01600000000 01500000000" rows="4"></textarea>
                        </div>

                        <div class="col-md-12 contactgroup" style="display: none;">
                          
                          <label for="contactgroup">Contact Group <span class="text-danger">*</span></label>
                          <select ui-jq="chosen"  id="smssent_contactgroup" name="contactgroup[]" multiple class="form-control w-md">
                              @foreach($groups as $group)  
                                  <option value="{{$group->id}}">{{$group->group_name}}</option>
                              @endforeach
                          </select>
                        </div>

                        <div class="col-md-12 contactgroup_file" style="display: none;">
                          <label class="col-sm-3 control-label font-bold">Select File [xls, xlsx]</label>
                          <div class="col-sm-7">
                            <input type="file" name="file" id="file12">
                          </div>
                        </div>
                      </div>
                    </div>
                    
                    <div class="form-group">
                      <div class="row">
                        <div class="col-md-8" id="recipient">
                          <label class="font-bold">Message Content</label>
                          <textarea name="message" id="msgcontent" rows="5" class="count_me form-control" placeholder="Enter message content"></textarea>
                          <div class="col-md-4" style="padding:0;">  Analyze SMS Count </div>
                          <div style="float: right; padding-right: 5px;">
                              <span class="parts-count">|</span>
                            </div>
                        </div>
                        <label class="font-bold" style="margin-left:14px;">SMS Type</label>
                        <div class="col-md-4">
                          <div class="row">
                        <div class="col-md-6">
                        
                          <div class="radio">
                            <label class="i-checks">
                              <input type="radio" name="sms_type" id="recipientsmsRadiosText" value="text" checked>
                              <i></i>
                              Text
                            </label>
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="radio">
                            <label class="i-checks">
                              <input type="radio" name="sms_type" id="recipientsmsRadiosUnicode" value="unicode">
                              <i></i>
                              Unicode
                            </label>
                          </div>
                        </div>
                          </div>
                        </div>
                        
                      </div>
                    </div>

                    <button type="button" class="btn btn-primary btn-addon btn-md pull-right sendLowCostSms"><i class="fa fa-save"></i> Submit</button>
                    <!-- submit button action handled by --- views/layouts/smsapp.blade.php - >setsmssend -->

                  </form>
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
  var parts = 1; 
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
      parts = 1;
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

      $('#totalsms').val(parts);
      $('#recipient .parts-count').text('| ' + parts + ' SMS ('+charPerSMS+' Char./SMS)');

      smscount = parts;

  });

  function isDoubleByte(str) {
      for (var i = 0, n = str.length; i < n; i++) {
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

  $('body').on('click', '.sendLowCostSms', function(){

    if ($('#numbertypesingle').prop('checked') == true)
    {
      if($('#contact_number').val() == "")
      {
        alert("Contact number can't left empty");
        $('#contact_number').focus();
        return false;
      } else {


        var numbers = $("#contact_number").val();

        numbers = numbers.replace(/\n/g, " ");
        numbers = numbers.replace(/,/g, " ")
        numbers = numbers.replace(/  +/g, " ");
        var count = numbers.trim().split(' ');


        var txt;
        var r = confirm("Are you sure? It will consume "+ parts*count.length +" sms* .");
        if (r == true) {
          sendLcSms();
        }

        // $("#smscount").html(parts*count.length);
        // $("confirmation").show();
      }
    } else if ($('#numbertypeupload').prop('checked') == true)
    {
      if($('#file12').val() == null)
      {
        alert("Contact file can't left empty");
        $('#file12').focus();
        return false;
      } else {
        sendLcSms();
      }
    } else if ($('#numbertypegroup').prop('checked') == true)
    {
      if($('#smssent_contactgroup').val() == null)
      {
        alert("Contact group can't left empty");
        $('#smssent_contactgroup').focus();
        return false;
      } else {
        sendLcSms();
      }
    }

  });


    

  //process sms sending form
  function sendLcSms(){

    let form = $('#smssendfrm')[0];
    let form_data = new FormData(form);
    let numbertype = '';
    
    if ($('#numbertypesingle').prop('checked') == true)
    {
      numbertype = $('#numbertypesingle').val();
    }

    if ($('#numbertypeupload').prop('checked') == true)
    {
      numbertype = $('#numbertypeupload').val();
    }
    if ($('#numbertypegroup').prop('checked') == true)
    {
      numbertype = $('#numbertypegroup').val();
    }

    form_data.append('campaign_name', $('#cam_name').val());
    form_data.append('numbertype',numbertype);
    form_data.append('contact_number',$('#contact_number').val());
    form_data.append('contactgroup',$('#smssent_contactgroup').val());
    form_data.append('message',$('#msgcontent').val());
    form_data.append('file',$('input[type=file]')[0].files[0]);
    form_data.append('source','web');



    url ="{{ config('apiconfig.api_url') }}/sms-api/send-lcsms";
    form_data.append('api_token','{{ $api_token }}');
    
    $.ajax({
      cache: false,
      url: url,
      type: 'post',
      data: form_data,
      dataType: 'json',
      mimeType: 'multipart/form-data',
      crossDomain: true,
      contentType: false,
      cache: false,
      processData: false,
      secure: true,
      beforeSend: function()
      {
        
        if($('#senderid').val() == "")
        {
          alert("Sender ID can't left empty");
          $('#senderid').focus();
          return false;
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
          $('.setupsms').text('Processing.... It may take some time. Please be patient.');
        }

      },
      success: function(res)
      {

        if (res.status=='success') {
          $('#smsStatus').css({'color':'green'}).text(res.message);          
          $('#statusModal').modal('show');
        }

        if (res.status=='error') {
          $('#smsStatus').css({'color':'red'}).text(res.message);
          $('#statusModal').modal('show');
        }

        if (numbertype == 'single')
        {
          $('#contact_number').val('');
          $('#msgcontent').val('');
          $('#numbertypesingle').prop('checked',true);
          $('#numbertypeupload').prop('checked',false);
          $('#numbertypegroup').prop('checked',false);
          document.querySelector('.contact_number').style.display = 'block';
          document.querySelector('.contactgroup').style.display = 'none';
        }

        if (numbertype == 'contgroup')
        {
          $('#numbertypesingle').prop('checked',false);
          $('#numbertypeupload').prop('checked',false);
          $('#numbertypegroup').prop('checked',true);
          $('#msgcontent').val('');
          document.querySelector('.contact_number').style.display = 'none';
          document.querySelector('.contactgroup').style.display = 'block';
          document.querySelector('.contactgroup_file').style.display = 'none';
        }

        if (numbertype == 'uploadfile')
        {
          $('#numbertypesingle').prop('checked',false);
          $('#numbertypegroup').prop('checked',false);
          $('#numbertypeupload').prop('checked',true);
          $('#msgcontent').val('');
          document.querySelector('.contact_number').style.display = 'none';
          document.querySelector('.contactgroup_file').style.display = 'block';
          document.querySelector('.contactgroup').style.display = 'none';
        }

        $('#numbertypesingle').prop('checked',true);
        $('#numbertypeupload').prop('checked',false);
        $('#msgcontent').val('');
        $('#cam_name').val('');
        
        document.querySelector('.contact_number').style.display = 'block';
        document.querySelector('.contactgroup_file').style.display = 'none';
        document.querySelector('.contactgroup').style.display = 'none';

        form_data.append('numbertype',numbertype);
        form_data.append('contact_number',$('#contact_number').val(''));
        form_data.append('message',$('#msgcontent').val(''));
        form_data.append('file',$('input[type=file]')[0].files[0]);

        setTimeout(function(){
          $('.setupsmsroot').css({'display':'none'});
          $('#contact_number').focus();
          window.location.reload();
        },2000);
      },
      error: function(err)
      {
        if (err.responseJSON.errmsg == "Insufficient nonmask sms balance" ||
            err.responseJSON.errmsg == "Insufficient mask sms balance" ||
            err.responseJSON.errmsg == "Insufficient voice sms balance") {

              $('.setupsms').css({'color':'red','text-align':'center'}).text(err.responseJSON.errmsg);

              setTimeout(function(){
                $('.setupsmsroot').css({'display':'none'});
                $('#contact_number').focus();
                $('.setupsms').css({'color':'green','text-align':'center'}).text('');
              },2000);

              form_data.append('numbertype',numbertype);
              form_data.append('contact_number',$('#contact_number').val(''));
              form_data.append('message',$('#msgcontent').val(''));
              form_data.append('template',$('#template').val(''));
              form_data.append('file',$('input[type=file]')[0].files[0]);

              return true;

        }
        
      }
    });
  };
// $('#myText').on('keyup',function(){
//   var numbers = $("#myText").val();
//   numbers = numbers.replace(/\n/g, " ");
//   numbers = numbers.replace(/,/g, " ")
//   numbers = numbers.replace(/  +/g, " ");
//   var count = numbers.trim().split(' ');
//   $('#wordCount').text(count.length);
// });


$('body').on('click','input[name="numbertype"]', function(){
  if ($(this).prop('checked',true))
  {
    switch($(this).val())
    {
      case 'single':
        document.querySelector('.contact_number').style.display = 'block';
        document.querySelector('.contactgroup_file').style.display = 'none';
        document.querySelector('.contactgroup').style.display = 'none';
        $('#template').attr('disabled','disabled');
        break;
      case 'uploadfile':
        document.querySelector('.contactgroup_file').style.display = 'block';
        document.querySelector('.contact_number').style.display = 'none';
        document.querySelector('.contactgroup').style.display = 'none';
        $('#template').removeAttr('disabled');
        break;
      case 'contgroup':
        document.querySelector('.contactgroup').style.display = 'block';
        document.querySelector('.contactgroup_file').style.display = 'none';
        document.querySelector('.contact_number').style.display = 'none';
        $('#template').removeAttr('disabled');
        break;
    }
  }
});

</script>


@endsection
