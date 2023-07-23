 <!-- aside -->
 <aside id="aside" class="app-aside hidden-xs bg-dark">
  <div class="aside-wrap">
    <div class="navi-wrap">
      <!-- user -->
      <div class="clearfix hidden-xs text-center hide" id="aside-user">
        <div class="dropdown wrapper">
          <a href="app.page.profile">
            <span class="thumb-lg w-auto-folded avatar m-t-sm">
              
            </span>
          </a>
          <a href="#" data-toggle="dropdown" class="dropdown-toggle hidden-folded">
            <span class="clear">
              <span class="block m-t-sm">
                <strong class="font-bold text-lt">John.Smith</strong> 
                <b class="caret"></b>
              </span>
              <span class="text-muted text-xs block">Art Director</span>
            </span>
          </a>
          <!-- dropdown -->
          <ul class="dropdown-menu animated fadeInRight w hidden-folded">
            <li class="wrapper b-b m-b-sm bg-info m-t-n-xs">
              <span class="arrow top hidden-folded arrow-info"></span>
              <div>
                <p>300mb of 500mb used</p>
              </div>
              <div class="progress progress-xs m-b-none dker">
                <div class="progress-bar bg-white" data-toggle="tooltip" data-original-title="50%" style="width: 50%"></div>
              </div>
            </li>
            <li>
              <a href>Settings</a>
            </li>
            <li>
              <a href="page_profile.html">Profile</a>
            </li>
            <li>
              <a href>
                <span class="badge bg-danger pull-right">3</span>
                Notifications
              </a>
            </li>
            <li class="divider"></li>
            <li>
              <a href="page_signin.html">Logout</a>
            </li>
          </ul>
          <!-- / dropdown -->
        </div>
        <div class="line dk hidden-folded"></div>
      </div>
      <!-- / user -->

      <!-- nav -->
      <nav ui-nav class="navi clearfix">
        <ul class="nav">
          <li class="hidden-folded padder m-t m-b-sm text-muted text-xs">

          </li>
          <li>
          <a href="{{route('superadmin')}}" class="auto">      
              
              <i class="glyphicon glyphicon-stats text-success icon text-primary-dker"></i>
              <span class="font-bold">Dashboard</span>
            </a>
            
          </li>
          
          <li class="line dk"></li>

          <li class="hidden-folded padder m-t m-b-sm text-muted text-xs">
   
          </li>
    <!--      
          <li>
            <a href class="auto">
              <span class="pull-right text-muted">
                <i class="fa fa-fw fa-angle-right text"></i>
                <i class="fa fa-fw fa-angle-down text-active"></i>
              </span>
              <i class="glyphicon glyphicon-briefcase icon text-success icon text-primary-dker"></i>
              <span>Manage SenderID</span>
            </a>
            <ul class="nav nav-sub dk">
              <li class="nav-sub-header">
                <a href>
                  <span>Manage SenderID</span>
                </a>
              </li>
              <li>
                <a href="{{route('client-senderids')}}">
                  <span>User SenderIDs</span>
                </a>
              </li>
            </ul>
          </li>  -->
          <li>
            <a href class="auto">
              <span class="pull-right text-muted">
                <i class="fa fa-fw fa-angle-right text"></i>
                <i class="fa fa-fw fa-angle-down text-active"></i>
              </span>
              <i class="glyphicon glyphicon-list text-success icon text-primary-dker"></i>
              <span>Messaging</span>
            </a>
            <ul class="nav nav-sub dk">
              <li class="nav-sub-header">
                <a href>
                  <span>Messaging</span>
                </a>
              </li>
              <li>
                <a href="{{route('send-sms')}}">
                  <span>Send SMS</span>
                </a>
              </li>

              @if(Auth::guard('web')->user()->lowcost_balance>0)
              <li>
                <a href="{{route('send-sms-lowcost')}}">
                  <span>Low Cost SMS</span>
                </a>
              </li>
              @endif
              @if(Auth::guard('web')->user()->id==145)
              <li>
               <a href="{{route('client-template')}}">
                 <span>Manage Template</span>
               </a>
              </li>
              @endif
              <li>
                <a href="{{route('manage-campaing')}}">
                  <span>Manage Campaing</span>
                </a>
              </li> 
              
            </ul>
          </li>


          <li>
            <a href="{{ route('manage-groups') }}" class="auto">
              
              <i class="fa fa-users text-success icon text-primary-dker"></i>
              <span>Phonebook</span>
            </a>
          </li>

          <li>
            <a href class="auto">
              <span class="pull-right text-muted">
                <i class="fa fa-fw fa-angle-right text"></i>
                <i class="fa fa-fw fa-angle-down text-active"></i>
              </span>
              <i class="fa fa-book text-success icon text-primary-dker"></i>
              <span>Report</span>
            </a>
            <ul class="nav nav-sub dk">
              <li class="nav-sub-header">
                <a href>
                  <span>Report</span>
                </a>
              </li>
               <li>
                <a href="{{route('clients-sms-report')}}">
                  <span>Today's Report</span>
                </a>
              </li> 
              <li>
                <a href="{{route('campaign-report')}}">
                  <span>Campaign Report</span>
                </a>
              </li>
              <li>
                <a href="{{route('single-sms-report')}}">
                  <span>Single SMS Report</span>
                </a>
              </li>
               <li>
                <a href="{{route('my-invoicelist')}}">
                  <span>Invoice Report</span>
                </a>
              </li> 
              <!-- <li>
                <a href="{{route('clients-send-sms-count')}}">
                  <span>Campaign SMS Report</span>
                </a>
              </li> -->
              
            </ul>
          </li>
          <li>
            <a href="{{route('developer-doc')}}"> 
              <i class="fa fa-book text-success icon text-primary-dker"></i>
              <span class="font-bold">Developers</span>

            </a>
          </li>
          <li>
            <a href="{{route('downloads')}}"> 
              <i class="fa fa-download text-success icon text-primary-dker"></i>
              <span class="font-bold">Downlaods</span>

            </a>
          </li>
          <li>
            <a href class="auto">      
              <span class="pull-right text-muted">
                <i class="fa fa-fw fa-angle-right text"></i>
                <i class="fa fa-fw fa-angle-down text-active"></i>
              </span>
              <i class="icon icon-settings text-success icon text-primary-dker"></i>
              <span>Settings</span>
            </a>
            <ul class="nav nav-sub dk">
              <li>
                <a href="{{route('myprofile-index',[Auth::guard('web')->user()->id,''])}}">
                  <span>Profile</span>
                </a>
              </li>
            </ul>
          </li>
        </ul>
      </nav>
      <!-- nav -->

      
      <!-- / aside footer -->
    </div>
  </div>
</aside>
<!-- / aside -->