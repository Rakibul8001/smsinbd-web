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
               <div class="row row-sm text-center">
                    Download Documents    
               </div>
            
            <div class="col-sm-6 text-right hidden-xs">
                
            </div>
            </div>
        </div>
        <!-- / main header -->
        <div class="wrapper-md" ng-controller="FlotChartDemoCtrl">
            <!-- stats -->
            <div class="row row-sm text-center">
                <div class="col-md-3">
                   <a href="{{route('download1')}}" class="btn btn-primary pull-right"><i class="fa fa-download"></i>  Download Sample Authorization for GP</a>
                </div>   
                
                <div class="col-md-4">   
                   <a href="{{route('download2')}}" class="btn btn-primary pull-right"><i class="fa fa-download"></i>  Download Sample Authorization for Other Operators</a> 
                </div>
                
                <div class="col-md-3">
                     <a href="{{route('download3')}}" class="btn btn-primary pull-right"><i class="fa fa-download"></i>  Download Content Vetting Form</a>
                </div>
                
            </div>
                
        </div>

            <div class="row row-sm text-center">
                
            </div>

            

        </div>



        </div>
        </div>
        <!-- /content -->
@endsection