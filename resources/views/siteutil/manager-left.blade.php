 <!-- aside -->
 <aside id="aside" class="app-aside hidden-xs bg-dark">
    <div class="aside-wrap">
      <div class="navi-wrap">
        <!-- user -->
        <div class="clearfix hidden-xs text-center hide" id="aside-user">
          <div class="dropdown wrapper">
            <a href="app.page.profile">
              <span class="thumb-lg w-auto-folded avatar m-t-sm">
                <img src="img/a0.jpg" class="img-full" alt="...">
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
              <span>Navigation</span>
            </li>
            <li>
              <a href="{{route('manager')}}" class="auto">      
                <span class="pull-right text-muted">
                  <i class="fa fa-fw fa-angle-down text-active"></i>
                </span>
                <i class="glyphicon glyphicon-stats icon text-primary-dker"></i>
                <span class="font-bold">Dashboard</span>
              </a>
            </li>
            
            <li class="line dk"></li>

            <li class="hidden-folded padder m-t m-b-sm text-muted text-xs">
              <span>Components</span>
            </li>
            <li>
              <a href class="auto">      
                <span class="pull-right text-muted">
                  <i class="fa fa-fw fa-angle-right text"></i>
                  <i class="fa fa-fw fa-angle-down text-active"></i>
                </span>
                <i class="icon icon-user"></i>
                <span>Manager Clients</span>
              </a>
              <ul class="nav nav-sub dk">
                <li>
                  <a href="{{route('client-registration')}}">
                    <span>Add New Client</span>
                  </a>
                </li>
                
                <li>
                  <a href="{{route('root-clients')}}">
                    <span>All Clients</span>
                  </a>
                </li>
                <li>
                  <a href="{{route('sms-senderid',['senderidtype'=>'general'])}}">
                    <span>Sender ID</span>
                  </a>
                </li>
                
                <li>
                  <a href="{{route('sms-saleto-client')}}">
                    <span>Create Invoice</span>
                  </a>
                </li>
                <li>
                  <a href="{{route('client-invoicelist')}}">
                    <span>Manage Invoice</span>
                  </a>
                </li>
                  
              </ul>
            </li>
            <li>
              <a href class="auto">      
                <span class="pull-right text-muted">
                  <i class="fa fa-fw fa-angle-right text"></i>
                  <i class="fa fa-fw fa-angle-down text-active"></i>
                </span>
                <i class="icon icon-user"></i>
                <span>Manager Reseller</span>
              </a>
              <ul class="nav nav-sub dk">
                <li>
                  <a href="{{route('reseller-registration')}}">
                    <span>Add New Resellers</span>
                  </a>
                </li>
                <li>
                  <a href="{{route('root-resellers')}}">
                    <span>All Resellers</span>
                  </a>
                </li>
                <li>
                  <a href="{{route('reseller-senderid',['senderidtype'=>'general'])}}">
                    <span>Sender ID</span>
                  </a>
                </li>
                <li>
                  <a href="{{route('sms-saleto-reseller')}}">
                    <span>Create Invoice</span>
                  </a>
                </li>
                <li>
                  <a href="{{route('reseller-invoicelist')}}">
                    <span>Manage Invoice</span>
                  </a>
                </li>
                  
              </ul>
            </li>
            <li>
            <a href class="auto">
              <span class="pull-right text-muted">
                <i class="fa fa-fw fa-angle-right text"></i>
                <i class="fa fa-fw fa-angle-down text-active"></i>
              </span>
              <i class="fa fa-book"></i>
              <span>Report</span>
            </a>
            <ul class="nav nav-sub dk">
              <li class="nav-sub-header">
                <a href>
                  <span>Report</span>
                </a>
              </li>
              <li>
                <a href="{{route('root-clients-sms-report')}}">
                  <span>DLR Report</span>
                </a>
              </li>
              <li>
                <a href="{{route('root-client-campaign-report')}}">
                  <span>Campaign Report</span>
                </a>
              </li>
              <!-- <li>
                <a href="{{route('root-clients-send-sms-count')}}">
                  <span>Campaign Report</span>
                </a>
              </li> -->
              
            </ul>
          </li>
            

           
           
          </ul>
        </nav>
        <!-- nav -->

        <!-- aside footer -->
       
        <!-- / aside footer -->
      </div>
    </div>
</aside>
<!-- / aside -->