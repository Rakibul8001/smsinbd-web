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
            @include('smsview.reseller.profiletab')
            </div>
            <div class="padder" style="margin-top: 20px;">    
              @if(session()->has('msg'))
                <div class="alert alert-success font-weight-bold clientsuccess" role="alert">
                  {{session()->get('msg')}}
                </div>
              @endif  
              <div class="alert alert-danger font-weight-bold clientunsuccess" style="display:none;" role="alert"></div>
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
                                <iframe src="{{asset('/public/reseller/nid/'.$clientDocuments->nid)}}" height="480px;" width="100%"></iframe>
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
                                <iframe src="https://docs.google.com/viewer?url={{asset('/public/reseller/applications/'.$clientDocuments->application)}}&embedded=true" height="480px;" width="100%" embedded=true></iframe>
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
                                <iframe src="{{asset('/public/reseller/clientphoto/'.$clientDocuments->customppphoto)}}" height="480px;" width="100%"></iframe>
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
                                <iframe src="{{asset('/public/reseller/tradelicence/'.$clientDocuments->tradelicence)}}" height="480px;" width="100%"></iframe>
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
                
                <div class="panel panel-default">
                    <div class="panel-heading font-bold text-right">Upload Your Documents</div>
                    <div class="panel-body">
                        <div class="panel panel-default">
                            
                            <form role="form" action="{{route('root-reseller-document-upload',['userid'=>$user->id])}}" method="post" enctype="multipart/form-data">
                            <div class="panel-heading font-bold">Document List</div>
                                <div class="panel-body">
                                
                                    @csrf
                                    <div class="form-group">
                                    <div class="row">
                                        <label class="col-sm-2 control-label font-bold">NID <span class="text-danger">*</span></label>
                                        <div class="col-sm-4">
                                        <!-- <input ui-jq="filestyle" ui-options="{icon: false, buttonName: 'btn-primary'}" type="file"> -->
                                        <input ui-jq="filestyle" ui-options="{icon: false, buttonName: 'btn-primary'}"  type="file" name="nationalid" class="btn form-control @if($errors->has('nationalid')) border-danger @else '' @endif nid">
                                        @if($errors->has('nationalid'))
                                            <label class="col-sm-12 font-bold text-danger control-label">{{$errors->first('nationalid')}}</label>

                                        @else 
                                            <label class="col-sm-12 font-bold control-label nid-white">[**Doc Type** jpeg, jpg, pdf, png|max:200kb]</label>
                                        @endif
                                        </div>
                                        <div class="col-md-offset-2 col-sm-2">
                                        <img src="/public/images/noimage.jpg" alt="" class="gallery1 nidimg" width="100" height="100" onerror="this.src='/public/images/noimage.jpg';"/>
                                        </div>

                                        <div class="col-md-1 col-sm-1 col-lg-1">
                                        
                                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#nidModal">
                                            @if(!empty($clientDocuments->nid)) <i class="fa fa-check"></i> @else <i class="fa fa-times"></i> @endif Preview Client NID
                                        </button>
                                        </div>
                                    </div>
                                    <div class="row" style="margin-top: 10px;">
                                        <label class="col-sm-2 control-label font-bold">Application</label>
                                        <div class="col-sm-4">
                                        <input type="file" name="application" class="btn application form-control @if($errors->has('application')) border-danger @else '' @endif">
                                        
                                        @if($errors->has('application'))
                                            <label class="col-sm-12 font-bold text-danger control-label">{{$errors->first('application')}}</label>
                                        @else 
                                            <label class="col-sm-12 font-bold control-label">[**Doc Type** doc, docx, pdf|max:200kb]</label>
                                        @endif
                                        </div>
                                        <div class="col-md-offset-2 col-sm-2">
                                        <img src="" class="applicationimg" width="100" height="100" onerror="this.src='/public/images/microsoftnoimage.jpg';"/>
                                        
                                        </div>

                                        <div class="col-md-1 col-sm-1 col-lg-1">
                                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#applicationModal">
                                            @if(!empty($clientDocuments->application)) <i class="fa fa-check"></i> @else <i class="fa fa-times"></i> @endif Preview Application
                                        </button>
                                        </div>
                                    </div>
                                    <div class="row" style="margin-top: 10px;">
                                        <label class="col-sm-2 control-label font-bold">Custom PP Photo</label>
                                        <div class="col-sm-4">
                                        <input type="file" name="custppphoto" class="btn custppphoto form-control @if($errors->has('custppphoto')) border-danger @else '' @endif">
                                        @if($errors->has('custppphoto'))
                                            <label class="col-sm-12 font-bold text-danger control-label">{{$errors->first('custppphoto')}}</label>
                                        @else 
                                            <label class="col-sm-12 font-bold control-label">[**Doc Type** jpeg, jpg, png|max:200kb]</label>
                                        @endif
                                        </div>
                                        <div class="col-md-offset-2 col-sm-2">
                                        <img src="" class="custppphotoimg" width="100" height="100" onerror="this.src='/public/images/noimage.jpg';"/>
                                        </div>

                                        <div class="col-md-1 col-sm-1 col-lg-1">

                                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#customppModal">
                                            @if(!empty($clientDocuments->customppphoto)) <i class="fa fa-check"></i> @else <i class="fa fa-times"></i> @endif Preview Client Photo
                                        </button>
                                        </div>
                                    </div>
                                    <div class="row" style="margin-top: 10px;">
                                        <label class="col-sm-2 control-label font-bold">Trade Licence Copy</label>
                                        <div class="col-sm-4">
                                        <input type="file" name="tradelicence" class="btn tradelicence form-control @if($errors->has('tradelicence')) border-danger @else '' @endif">
                                        @if($errors->has('tradelicence'))
                                            <label class="col-sm-12 font-bold text-danger control-label">{{$errors->first('tradelicence')}}</label>
                                        @else 
                                            <label class="col-sm-12 font-bold control-label">[**Doc Type** jpeg, jpg, pdf, png|max:1Mb]</label>
                                        @endif
                                        </div>
                                        <div class="col-md-offset-2 col-sm-2">
                                        <img src="" class="tradelicenceimg" width="100" height="100" onerror="this.src='/public/images/tradelicence.png';"/>
                                        </div>

                                        <div class="col-md-1 col-sm-1 col-lg-1">
                                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#tradelicenceModal">
                                            @if(!empty($clientDocuments->tradelicence)) <i class="fa fa-check"></i> @else <i class="fa fa-times"></i> @endif Preview Trade Licence
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
        <!-- / main -->
        </div>



        </div>
        </div>
        <!-- /content -->
        
@endsection