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
                <h1 class="m-n font-thin h3 text-black font-bold">Super Admin Settings</h1>
                <small class="text-muted"></small>
            </div>
            <div class="col-sm-6 text-right hidden-xs">
                
            </div>
            </div>
        </div>
        <!-- / main header -->
        <div class="wrapper-md" ng-controller="FlotChartDemoCtrl">
            <!-- stats -->

            <div class="row">
              
            <div class="col-md-12">
              @if(session()->has('msg'))
                <div class="alert alert-success font-weight-bold clientsuccess" role="alert">
                  {{session()->get('msg')}}
                </div>
              @endif  

              @if(count($errors) > 0)
                <div class="alert alert-danger font-weight-bold clientsuccess text-left" role="alert">
                <uL class="list-group">
                @foreach($errors->all() as $error)
                  <li class="list-group-item">{{$error}}</li>
                @endforeach
                </ul>
                </div>

              @endif
              {{-- <a class="btn btn-primary btn-addon btn-md pull-right mb-5" style="margin-bottom:10px; z-index: 9999; position:relative;" href="{{route('root-users')}}"><i class="fa fa-angle-left"></i> Back to List</a> --}}
              <div class="tab-container">
                    <ul class="nav nav-tabs">
                      <li class="active font-bold"><a href data-toggle="tab" data-target="#tab_1">SITE INFORMATION </a></li>
                      <li class="font-bold"><a href data-toggle="tab" data-target="#tab_2">THIRD PARTY SETTINGS</a></li>
                      <li class="font-bold"><a href data-toggle="tab" data-target="#tab_3">APPLICATION PARAMETERS  </a></li>
                    </ul>
                    <form role="form" action="{{route('smsadmin-settings-update')}}" method="post">
                      @csrf
                    <div class="tab-content">
                      <div class="tab-pane active" id="tab_1">
                          
                            <div class="panel-body">
                                <div class="panel panel-default">
                                                          
                                <div class="panel-heading font-bold">ADD/EDIT SETTINGS </div>
                                      <div class="panel-body">
                                          <div class="form-group">
                                            <div class="row">
                                              <div class="col-md-6">
                                                <label class="font-bold" for="site_name">Site Name <span class="text-danger">*</span></label>
                                                <input type="text" name="site_name" id="site_name" value="{{$config['site_information']['site_name']}}" class="form-control {{$errors->has('name') ? 'border-danger': ''}}"  placeholder="Enter site name">
                                              </div>
                                              <div class="col-md-6">
                                                <label class="font-bold" for="site_slogan">Site Slogan: <span class="text-danger">*</span></label>
                                                <input type="text" name="site_slogan" id="site_slogan" value="{{$config['site_information']['site_slogan']}}" class="form-control {{$errors->has('name') ? 'border-danger': ''}}"  placeholder="Enter site slogan">
                                              </div>
                                            </div>
                                          </div>
                                          
                      
                                          <div class="form-group">
                                            <div class="row">
                                              <div class="col-md-12">
                                                <label class="font-bold" for="address">Address</label>
                                                <textarea name="address" id="address" class="form-control" placeholder="Enter your address">{{$config['site_information']['address']}}</textarea>
                                              </div>
                                              
                                            </div>
                                          </div>
                                          
                                          <div class="form-group">
                                            <div class="row">
                                              <div class="col-md-3">
                                                <label class="font-bold" for="email">Email <span class="text-danger">*</span></label>
                                              <input type="email" name="email" id="email" value="{{$config['site_information']['email']}}" class="form-control {{$errors->has('email')?'border-danger':''}}" placeholder="Enter email address">
                                              </div>
                                              <div class="col-md-3">
                                                <label class="font-bold" for="order_email">Order Email <span class="text-danger">*</span></label>
                                                <input type="email" name="order_email" id="order_email" value="{{$config['site_information']['order_email']}}" class="form-control {{$errors->has('order_email')?'border-danger':''}}" placeholder="Enter order email address">
                                              </div>
                                              <div class="col-md-3">
                                                <label class="font-bold" for="email_from">Mail From <span class="text-danger">*</span></label>
                                                <input type="email" name="email_from" id="email_from" value="{{$config['site_information']['email_from']}}" class="form-control" placeholder="Enter mail from email address"/>
                                              </div>
                                              <div class="col-md-3">
                                                <input type="hidden" name="id"/>
                                                <label class="font-bold" for="contact_phon">Contact Number  <span class="text-danger">*</span></label>
                                              <input type="text" name="contact_phone" id="contact_phone" class="form-control" value="{{$config['site_information']['contact_phone']}}" required placeholder="Enter contact number">
                                              </div>
                                              
                                            </div>
                                          </div>
                                          
                                          <input type="hidden" name="usertype" id="usertype" value="client"/>
                                          <input type="hidden" name="paneltype" value="admin"/>
                                          <button type="submit" class="btn btn-primary btn-addon btn-md pull-right"><i class="fa fa-plus"></i> UPDATE</button>
                                        
                                      </div>
                                    
                                    </div>
                            </div>
                      </div>
                      <div class="tab-pane" id="tab_2">
                                 
                                        <div class="panel panel-default">
                                          
                                        <div class="panel-heading font-bold">ADD/EDIT THIRD PARTY SETTINGS </div>
                                              <div class="panel-body">
                                                  <div class="form-group">
                                                    <div class="row">
                                                      <div class="col-md-4">
                                                        <label class="font-bold" for="fb_link">Face Book</label>
                                                        <div class="input-group">
                                                          <div class="input-group-addon">
                                                            <i class="fa fa-facebook"></i>
                                                          </div>
                                                          <input type="text" name="fb_link" id="fb_link" value="{{$config['third_party_settings']['fb_link']}}" class="form-control" required placeholder="Enter face book link">
                                                        </div>
                                                      </div>
                                                      <div class="col-md-4">
                                                        <label class="font-bold" for="twitter_link">Twitter</label>
                                                        <div class="input-group">
                                                          <div class="input-group-addon">
                                                            <i class="fa fa-twitter"></i>
                                                          </div>
                                                          <input type="text" name="twitter_link" id="twitter_link" value="{{$config['third_party_settings']['twitter_link']}}" class="form-control" placeholder="Enter twitter link">
                                                        </div>
                                                      </div>
                                                      <div class="col-md-4">
                                                        <label class="font-bold" for="linkedin">Linkedin </label>
                                                        <div class="input-group">
                                                          <div class="input-group-addon">
                                                            <i class="fa fa-linkedin"></i>
                                                          </div>
                                                          <input type="text" name="linkedin_link" id="linkedin_link"  value="{{$config['third_party_settings']['linkedin_link']}}" class="form-control" placeholder="Enter linkedin link">
                                                        </div>
                                                      </div>
                                                    </div>
                                                  </div>
                                                  
                              
                                                  <div class="form-group">
                                                    <div class="row">
                                                      <div class="col-md-4">
                                                        <label for="recaptcha_site_key" class="font-bold">reCAPTCHA Site key</label>
                                                        <div class="input-group">
                                                          <div class="input-group-addon">
                                                            <i class="fa fa-key"></i>
                                                          </div>
                                                          <input type="text" name="recaptcha_site_key" id="recaptcha_site_key" value="{{$config['third_party_settings']['recaptcha_site_key']}}" placeholder="Enter recaptcha key" class="form-control"/>
                                                        </div>
                                                      </div>
                                                      
                                                      <div class="col-md-8">
                                                        <label for="about_site" class="font-bold">About Site</label>
                                                        <div class="input-group">
                                                          <div class="input-group-addon">
                                                            <i class="fa fa-file"></i>
                                                          </div>
                                                          <input type="text" name="about_site" id="about_site" value="{{$config['third_party_settings']['about_site']}}" class="form-control" placeholder="Enter about site text"/>
                                                        </div>
                                                      </div>
                                                      
                                                    </div>
                                                  </div>
                                                  
                                                  
                                                 
                                                  <input type="hidden" name="usertype" id="usertype" value="client"/>
                                                  <input type="hidden" name="paneltype" value="admin"/>
                                                  <button type="submit" class="btn btn-primary btn-addon btn-md pull-right"><i class="fa fa-plus"></i> UPDATE</button>
                                              
                                              </div>
                                            
                                            </div>
                          </div>
                      <div class="tab-pane" id="tab_3">
                                    <div class="panel-body">
                                        <div class="panel panel-default">
                                          
                                        <div class="panel-heading font-bold">ADD/EDIT SITE PARAMETERS </div>
                                              <div class="panel-body">
                                                  <div class="form-group">
                                                    <div class="row">
                                                      <div class="col-md-4">
                                                        <div class="checkbox">
                                                          <label class="i-checks">
                                                            <input type="checkbox" name="under_maintenence" @if($config['site_parameters']['under_maintenence'] == 'y') checked value="{{$config['site_parameters']['under_maintenence']}}" @else value="n" @endif>
                                                            <i></i>
                                                            Under Maintenance
                                                          </label>
                                                        </div>
                                                        
                                                          
                                                      </div>
                                                      <div class="col-md-8">
                                                        <label class="font-bold" for="maintenence_messsage">Message <span class="text-danger">*</span></label>
                                                        <div class="input-group">
                                                          <div class="input-group-addon">
                                                            <i class="fa fa-file-text-o"></i>
                                                          </div>
                                                          <input type="text" name="maintenence_messsage" id="maintenence_messsage" value="{{$config['site_parameters']['maintenence_messsage']}}" class="form-control" placeholder="Enter maintenence messsage">
                                                        </div>
                                                      </div>
                                                      
                                                    </div>
                                                  </div>
                                                  
                              
                                                  <div class="form-group">
                                                    <div class="row">
                                                      <div class="col-md-3">
                                                        <label class="font-bold" for="max_audio_file_size">Max Audio File Size</label>
                                                        <div class="input-group">
                                                          <div class="input-group-addon">
                                                            <i class="fa fa-file-text-o"></i>
                                                          </div>
                                                          <input type="text" name="max_audio_file_size" id="max_audio_file_size" value="{{$config['site_parameters']['max_audio_file_size']}}" placeholder="Enter max audio file size" class="form-control"/>
                                                        </div>
                                                      </div>
                                                      
                                                      <div class="col-md-3">
                                                        <label class="font-bold" for="max_tex_sms_limit">Max Text Sms Limit</label>
                                                        <div class="input-group">
                                                          <div class="input-group-addon">
                                                            <i class="fa fa-envelope-o"></i>
                                                          </div>
                                                          <input type="text" name="max_text_sms_limit" id="max_text_sms_limit" value="{{$config['site_parameters']['max_text_sms_limit']}}" class="form-control" placeholder="Enter max text sms limit"/>
                                                        </div>
                                                      </div>
                                                      
                                                      <div class="col-md-3">
                                                        <label class="font-bold" for="max_voice_sms_limit">Max Voice Sms Limit</label>
                                                        <div class="input-group">
                                                          <div class="input-group-addon">
                                                            <i class="fa fa-envelope-o"></i>
                                                          </div>
                                                          <input type="text" name="max_voice_sms_limit" id="max_voice_sms_limit" value="{{$config['site_parameters']['max_voice_sms_limit']}}"  class="form-control" placeholder="Enter max voice sms limit"/>
                                                        </div>
                                                      </div>
                                                      
                                                      <div class="col-md-3">
                                                        <label for="tex_limit_campaing" class="font-bold">Campaing Text Limit</label>
                                                        <div class="input-group">
                                                          <div class="input-group-addon"><i class="fa fa-envelope-o"></i></div>
                                                          <input type="text" class="form-control" name="text_limit_campaing" id="text_limit_campaing" value="{{$config['site_parameters']['text_limit_campaing']}}" placeholder="Enter campaing text limit">
                                                        </div>
                                                      </div>
                                                      
                                                    </div>
                                                  </div>

                                                  <div class="form-group">
                                                    <div class="row">
                                                      
                                                      <div class="col-md-4">
                                                        <label for="voice_limit_campaing" class="font-bold">Campaing Voice Limit</label>
                                                        <div class="input-group">
                                                          <div class="input-group-addon"><i class="fa fa-envelope-o"></i></div>
                                                          <input type="text" class="form-control" name="voice_limit_campaing" id="voice_limit_campaing" value="{{$config['site_parameters']['voice_limit_campaing']}}" placeholder="Enter voice campaing limit">
                                                        </div>
                                                      </div>
                                                      <div class="col-md-4">
                                                        <label for="ssl_comm_user" class="font-bold">SSL Commerze User</label>
                                                        <div class="input-group">
                                                          <div class="input-group-addon"><i class="fa fa-user"></i></div>
                                                          <input type="text" class="form-control" name="ssl_comm_user" id="ssl_comm_user" value="{{$config['site_parameters']['ssl_comm_user']}}" placeholder="Enter ssl commerze user">
                                                        </div>
                                                      </div>
                                                      <div class="col-md-4">
                                                        <label for="ssl_comm_password" class="font-bold">SSL Commerze Password</label>
                                                        <div class="input-group">
                                                          <div class="input-group-addon"><i class="fa fa-key"></i></div>
                                                          <input type="text" class="form-control" name="ssl_comm_password" id="ssl_comm_password" value="{{$config['site_parameters']['ssl_comm_password']}}" placeholder="Enter ssl commerze password">
                                                        </div>
                                                      </div>
                                                    </div>
                                                  </div>
                                                  
                                                  
                                                  
                                                  <input type="hidden" name="usertype" id="usertype" value="client"/>
                                                  <input type="hidden" name="paneltype" value="admin"/>
                                                  <button type="submit" class="btn btn-primary btn-addon btn-md pull-right"><i class="fa fa-plus"></i> UPDATE</button>
                                                
                                              </div>
                                            
                                            </div>
                                    </div>
                      </div>
                    </div>
                  </form>
                  </tabset>
                </div>
                
            </div>
            </div>
            <!-- / stats -->

        </div>
        </div>
        <!-- / main -->
        </div>



        </div>
        </div>
        <!-- /content -->
@endsection