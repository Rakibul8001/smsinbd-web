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
                <h1 class="m-n font-thin h3 text-black font-bold">Client Edit</h1>
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
              <div class="alert alert-danger font-weight-bold clientunsuccess" style="display:none;" role="alert"></div>
              <a class="btn btn-primary btn-addon btn-md pull-right mb-5" style="margin-bottom:10px; z-index: 9999; position:relative;" href="{{route('root-clients')}}"><i class="fa fa-angle-left"></i> Back to List</a>
              <div class="tab-container">
                    <ul class="nav nav-tabs">
                      <li class="active font-bold"><a href data-toggle="tab" data-target="#tab_1">GENERAL INFORMATION </a></li>
                      <li class="font-bold"><a href data-toggle="tab" data-target="#tab_2">DOCUMENTS UPLOAD</a></li>
                    </ul>
                    <div class="tab-content">
                      <div class="tab-pane active" id="tab_1">
                        <div class="panel panel-default">
                          <form role="form" action="{{route('reseller-client-update')}}" method="post">
                           <div class="panel-heading font-bold">General Information <span  data-verified="{{@$user->documents[0]->isVerified}}" class="pull-right  @if(@$user->documents[0]->isVerified == false) text-danger @elseif(@$user->documents[0]->isVerified == true) text-success @endif">Verified :@if(@$user->documents[0]->isVerified == true) Yes @elseif(@$user->documents[0]->isVerified == false) No @endif</span></div>
                            <div class="panel-body">
                                <div class="panel panel-default">
                                  
                                <div class="panel-heading font-bold">EDIT USER <span class="text-success" style="position: relative; left: 40%;">@if(session()->has('msg')) <span class="text-left">{{session()->get('msg')}}</span> @endif</span><span data-status="{{$user->status}}" class="pull-right dostatus @if($user->status == '') text-danger @elseif($user->status == 'n') text-danger @elseif($user->status == 'y') text-success @endif">Status :@if($user->status == '') No @elseif($user->status == 'n') No @elseif($user->status == 'y') Yes @endif</span></div>
                                      <div class="panel-body">
                                      
                                          @csrf
                                          <div class="form-group">
                                            <div class="row">
                                              <div class="col-md-6">
                                                <label class="font-bold">Contact Name <span class="text-danger">*</span></label>
                                                @if(@$user->documents[0]->isVerified == false)
                                                <input type="text" name="name" id="name" value="{{$user['name']}}" class="form-control {{$errors->has('name') ? 'border-danger': ''}}" required placeholder="Enter Contact Name">
                                                @else
                                                <div class="form-control">{{$user['name']}}</div>
                                                @endif
                                              </div>
                                              <div class="col-md-6">
                                                <label class="font-bold">Email <span class="text-danger">*</span></label>
                                                @if(@$user->documents[0]->isVerified == false)
                                                <input type="text" name="email" id="email" value="{{$user['email']}}" class="form-control {{$errors->has('email') ? 'border-danger': ''}}" required placeholder="Enter Email">
                                                @else
                                                <div class="form-control">{{$user['email']}}</div>
                                                @endif
                                              </div>
                                            </div>
                                          </div>
                                          <div class="form-group">
                                            <div class="row">
                                              <div class="col-md-6">
                                                <label class="font-bold">Company Name <span class="text-danger">*</span></label>
                                                @if(@$user->documents[0]->isVerified == false)
                                                <input type="text" name="company" id="company" value="{{$user['company']}}" class="form-control {{$errors->has('company')?'border-danger':''}}" placeholder="Enter Company Name">
                                                @else
                                                <div class="form-control">{{$user['company']}}</div>
                                                @endif
                                              </div>
                                              <div class="col-md-6">
                                                <label class="font-bold">Contact Phone <span class="text-danger">*</span></label>
                                                @if(@$user->documents[0]->isVerified == false)
                                                <input type="text" name="phone" id="phone" value="{{$user['phone']}}" class="form-control {{$errors->has('phone') ? 'border-danger': ''}}" required placeholder="Enter Phone">
                                                @else
                                                <div class="form-control">{{$user['phone']}}</div>
                                                @endif
                                              </div>
                                            </div>
                                          </div>
                      
                                          <div class="form-group">
                                            <div class="row">
                                              <div class="col-md-6">
                                                <label class="font-bold">Address</label>
                                                @if(@$user->documents[0]->isVerified == false)
                                                <textarea name="address" id="address" class="form-control" placeholder="Enter your address">{{$user['address']}}</textarea>
                                                @else
                                                <div class="form-control">{{$user['address']}}</div>
                                                @endif
                                              </div>
                                              <div class="col-md-3">
                                                <label class="font-bold">Password <span class="text-danger">*</span></label>
                                                <input type="password" name="password" id="password" class="form-control {{$errors->has('password')?'border-danger':''}}" placeholder="Enter Password">
                                              </div>
                                              <div class="col-md-3">
                                                <label class="font-bold">Confirm Password <span class="text-danger">*</span></label>
                                              <input type="password" name="password_confirmation" id="password_confirmation" class="form-control {{$errors->has('password_confirmation')?'border-danger':''}}" placeholder="Enter Password">
                                              </div>
                                            </div>
                                          </div>
                                          
                                          <div class="form-group">
                                            <div class="row">
                                              <div class="col-md-3">
                                                <label class="font-bold">Country <span class="text-danger">*</span></label>
                                                @if(@$user->documents[0]->isVerified == false)
                                                <select class="form-control w-full clientregistration {{$errors->has('country')?'border-danger':''}}" name="country" id="country">
                                                  
                                                  
                                                      <option selected value="{{$user->country}}">{{$user->country}}</option>
                                                      @foreach ($countries as $country)
                      
                                                        <option value="{{$country->country_name}}">{{$country->country_name}}</option>
                                                          
                                                      @endforeach
                                                  
                                                </select>
                                                @else
                                                <div class="form-control">{{$user->country}}</div>
                                                @endif
                                              </div>
                                              <div class="col-md-3">
                                                <input type="hidden" name="id" value="{{$user->id}}"/>
                                                <label class="font-bold">City </label>
                                                @if(@$user->documents[0]->isVerified == false)
                                                <input type="text" name="city" id="city" class="form-control" value="{{$user->city}}" required placeholder="Enter your city">
                                                @else
                                                <div class="form-control">{{$user->city}}</div>
                                                @endif
                                              </div>
                                              <div class="col-md-3">
                                                <label class="font-bold">State</label>
                                                @if(@$user->documents[0]->isVerified == false)
                                                <input type="text" name="state" id="state" class="form-control" value="{{$user->state}}" required placeholder="Enter your state">
                                                @else
                                                <div class="form-control">{{$user->state}}</div>
                                                @endif
                                              </div>
                                              <div class="col-md-3">
                                                <label class="font-bold">Status</label>
                                                @if(@$user->documents[0]->isVerified == false)
                                                <select class="form-control" name="status" id="status">
                                                    <option value="{{$user->status}}">@if($user->status == '') No @elseif($user->status == 'n') No @elseif($user->status == 'y') Yes @endif</option>
                                                    <option value="y">Yes</option>
                                                    <option value="n">No</option>
                                                </select>
                                                @else
                                                <div class="form-control">{{$user->status}}</div>
                                                @endif
                                              </div>
                                            </div>
                                          </div>
                                          
                                          <input type="hidden" name="usertype" id="usertype" value="client"/>
                                          <input type="hidden" name="paneltype" value="admin"/>
                                          <button type="submit" class="btn btn-primary btn-addon btn-md pull-right"><i class="fa fa-pencil"></i> UPDATE</button>
                                        
                                      </div>
                                    
                                    </div>
                            </div>
                          </form>
                          </div>
                      </div>

                      <div class="modal bd-example-modal-lg fade" id="nidModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg" role="document">
                          <div class="modal-content">
                            <div class="modal-header">
                              <h5 class="modal-title font-bold" id="exampleModalLabel">Preview Client Application</h5>
                              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                              </button>
                            </div>
                            <div class="modal-body" style="height: 500px;">
                              @if($clientDocuments)
                                <iframe src="{{asset('/public/nid/'.$clientDocuments->nid)}}" height="480px;" width="100%"></iframe>
                              @else 
                                <h3 class="text-center">Document Not Upload Yet!</h3>
                              @endif
                            </div>
                            <div class="modal-footer">
                              <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            </div>
                          </div>
                        </div>
                      </div>

                      <div class="modal bd-example-modal-lg fade" id="applicationModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg" role="document">
                          <div class="modal-content">
                            <div class="modal-header">
                              <h5 class="modal-title font-bold" id="exampleModalLabel">Preview Client Application</h5>
                              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                              </button>
                            </div>
                            <div class="modal-body" style="height: 500px;">
                            
                              @if($clientDocuments)
                                <iframe src="https://docs.google.com/viewer?url={{asset('/public/applications/'.$clientDocuments->application)}}&embedded=true" height="480px;" width="100%" embedded=true></iframe>
                              @else 
                                <h3 class="text-center">Document Not Upload Yet!</h3>
                              @endif
                              
                            </div>
                            <div class="modal-footer">
                              <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            </div>
                          </div>
                        </div>
                      </div>

                      <div class="modal bd-example-modal-lg fade" id="customppModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg" role="document">
                          <div class="modal-content">
                            <div class="modal-header">
                              <h5 class="modal-title font-bold" id="exampleModalLabel">Preview Client Photo</h5>
                              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                              </button>
                            </div>
                            <div class="modal-body" style="height: 500px;">
                              @if($clientDocuments)
                                <iframe src="{{asset('/public/clientphoto/'.$clientDocuments->customppphoto)}}" height="480px;" width="100%"></iframe>
                              @else 
                                <h3 class="text-center">Document Not Upload Yet!</h3>
                              @endif
                            </div>
                            <div class="modal-footer">
                              <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            </div>
                          </div>
                        </div>
                      </div>

                      <div class="modal bd-example-modal-lg fade" id="tradelicenceModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg" role="document">
                          <div class="modal-content">
                            <div class="modal-header">
                              <h5 class="modal-title font-bold" id="exampleModalLabel">Preview Trade Licence</h5>
                              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                              </button>
                            </div>
                            <div class="modal-body" style="height: 500px;">
                              @if($clientDocuments)
                                <iframe src="{{asset('/public/tradelicence/'.$clientDocuments->tradelicence)}}" height="480px;" width="100%"></iframe>
                              @else 
                                <h3 class="text-center">Document Not Upload Yet!</h3>
                              @endif
                            </div>
                            <div class="modal-footer">
                              <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            </div>
                          </div>
                        </div>
                      </div>


                      <div class="tab-pane" id="tab_2">
                        <div class="panel panel-default">
                            <div class="panel-heading font-bold text-right">Upload Your Documents</div>
                            <div class="panel-body">
                                <div class="panel panel-default">
                                 
                                  <form role="form" action="{{route('reseller-client-document-upload',['userid'=>$user->id])}}" method="post" enctype="multipart/form-data">
                                  <div class="panel-heading font-bold">Document List</div>
                                      <div class="panel-body">
                                      
                                          @csrf
                                          <div class="form-group">
                                            <div class="row">
                                              <label class="col-sm-2 control-label font-bold">NID <span class="text-danger">*</span></label>
                                              <div class="col-sm-4">
                                                @if(@$user->documents[0]->isVerified == false)
                                                <input type="file" name="nationalid" class="btn btn-warning form-control @if($errors->has('nationalid')) border-danger @else '' @endif nid">
                                                  @if($errors->has('nationalid'))
                                                    <label class="col-sm-12 font-bold text-danger control-label">{{$errors->first('nationalid')}}</label>

                                                  @else 
                                                    <label class="col-sm-12 font-bold control-label nid-white">[**Doc Type** jpeg, jpg, png|max:200kb]</label>
                                                  @endif
                                                @endif
                                              </div>
                                              <div class="col-md-offset-2 col-sm-2">
                                                <img src="/public/images/noimage.jpg" alt="" class="gallery1 nidimg" width="100" height="100" onerror="this.src='/public/images/noimage.jpg';"/>
                                              </div>

                                              <div class="col-md-1 col-sm-1 col-lg-1">
                                                
                                                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#nidModal">
                                                @if(@$clientDocuments->nid !== null) <i class="fa fa-check"></i> @else <i class="fa fa-times"></i> @endif Preview Client NID
                                                </button>
                                              </div>
                                            </div>
                                            <div class="row" style="margin-top: 10px;">
                                              <label class="col-sm-2 control-label font-bold">Application</label>
                                              <div class="col-sm-4">
                                                @if(@$user->documents[0]->isVerified == false)
                                                <input type="file" name="application" class="btn btn-info application form-control @if($errors->has('application')) border-danger @else '' @endif">
                                                
                                                  @if($errors->has('application'))
                                                    <label class="col-sm-12 font-bold text-danger control-label">{{$errors->first('application')}}</label>
                                                  @else 
                                                    <label class="col-sm-12 font-bold control-label">[**Doc Type** doc, docx, pdf|max:200kb]</label>
                                                  @endif
                                                @endif
                                              </div>
                                              <div class="col-md-offset-2 col-sm-2">
                                                <img src="" class="applicationimg" width="100" height="100" onerror="this.src='/public/images/microsoftnoimage.jpg';"/>
                                              
                                              </div>

                                              <div class="col-md-1 col-sm-1 col-lg-1">
                                                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#applicationModal">
                                                @if(@$clientDocuments->application !== null) <i class="fa fa-check"></i> @else <i class="fa fa-times"></i> @endif Preview Application
                                                </button>
                                              </div>
                                            </div>
                                            <div class="row" style="margin-top: 10px;">
                                              <label class="col-sm-2 control-label font-bold">Custom PP Photo</label>
                                              <div class="col-sm-4">
                                                @if(@$user->documents[0]->isVerified == false)
                                                <input type="file" name="custppphoto" class="btn btn-danger custppphoto form-control @if($errors->has('custppphoto')) border-danger @else '' @endif">
                                                  @if($errors->has('custppphoto'))
                                                    <label class="col-sm-12 font-bold text-danger control-label">{{$errors->first('custppphoto')}}</label>
                                                  @else 
                                                    <label class="col-sm-12 font-bold control-label">[**Doc Type** jpeg, jpg, png|max:200kb]</label>
                                                  @endif
                                                @endif
                                              </div>
                                              <div class="col-md-offset-2 col-sm-2">
                                                <img src="" class="custppphotoimg" width="100" height="100" onerror="this.src='/public/images/noimage.jpg';"/>
                                              </div>

                                              <div class="col-md-1 col-sm-1 col-lg-1">

                                                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#customppModal">
                                                @if(@$clientDocuments->customppphoto !== null) <i class="fa fa-check"></i> @else <i class="fa fa-times"></i> @endif Preview Client Photo
                                                </button>
                                              </div>
                                            </div>
                                            <div class="row" style="margin-top: 10px;">
                                              <label class="col-sm-2 control-label font-bold">Trade Licence Copy</label>
                                              <div class="col-sm-4">
                                                @if(@$user->documents[0]->isVerified == false)
                                                <input type="file" name="tradelicence" class="btn btn-warning tradelicence form-control @if($errors->has('tradelicence')) border-danger @else '' @endif">
                                                  @if($errors->has('tradelicence'))
                                                    <label class="col-sm-12 font-bold text-danger control-label">{{$errors->first('tradelicence')}}</label>
                                                  @else 
                                                    <label class="col-sm-12 font-bold control-label">[**Doc Type** jpeg, jpg, pdf, png|max:1Mb]</label>
                                                  @endif
                                                @endif
                                              </div>
                                              <div class="col-md-offset-2 col-sm-2">
                                                <img src="" class="tradelicenceimg" width="100" height="100" onerror="this.src='/public/images/tradelicence.png';"/>
                                              </div>

                                              <div class="col-md-1 col-sm-1 col-lg-1">
                                                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#tradelicenceModal">
                                                @if(@$clientDocuments->tradelicence !== null) <i class="fa fa-check"></i> @else <i class="fa fa-times"></i> @endif Preview Trade Licence
                                                </button>
                                              </div>
                                            </div>
                                          </div>
                                          
                                          <input type="hidden" name="usertype" id="usertype" value="client"/>
                                          <input type="hidden" name="paneltype" value="admin"/>
                                          @if(@$user->documents[0]->isVerified == false)
                                            <button type="submit" class="btn btn-primary btn-addon btn-md pull-right"><i class="fa fa-save"></i> SUBMIT</button>
                                          @endif
                                        
                                      </div>
                                    </form>
                                    </div>
                            </div>
                          </div>
                      </div>
                      
                    </div>
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