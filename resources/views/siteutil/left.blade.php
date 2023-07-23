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
                
                <i class="glyphicon glyphicon-stats icon text-primary-dker"></i>
                <span class="font-bold">Dashboard</span>
              </a>
              
            </li>
            
            <li class="line dk"></li>

            <li class="hidden-folded padder m-t m-b-sm text-muted text-xs">

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
                <li class="nav-sub-header">
                  <a href>
                    <span>Manage Clients</span>
                  </a>
                </li>
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
                  <a href="{{route('rotation-sms-senderid',['senderidtype'=>'general'])}}">
                    <span>Dynamic Sender ID</span>
                  </a>
                </li>
                <li>
                  <a href="{{route('turbo-sms-senderid',['senderidtype'=>'general'])}}">
                    <span>Turbo Sender ID</span>
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
                <li>
                  <a href="{{route('manage-template')}}">
                    <span>Manage Template</span>
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
                <i class="fa fa-flag"></i>
                <span>Sender ID</span>
              </a>

              <ul class="nav nav-sub dk">
                <li class="nav-sub-header">
                  <a href>
                    <span>Sender ID</span>
                  </a>
                </li>
                <li>
                  <a href="{{route('create-senderid')}}">
                    <span>Create Sender ID</span>
                  </a>
                </li>

                <li>
                  <a href="{{route('manage-senderid')}}">
                    <span>Manage Sender ID</span>
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
                <li class="nav-sub-header">
                  <a href>
                    <span>Manage Reseller</span>
                  </a>
                </li>
                <li>
                  <a href="{{route('reseller-registration')}}">
                    <span>Add New Reseller</span>
                  </a>
                </li>
                <li>
                  <a href="{{route('root-resellers')}}">
                    <span>All Resellers</span>
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
            @if(Auth::guard('root')->user()->id==3)
            <li>
              <a href="{{route('customer-ledger-selection')}}">
                <span>Customer Ledger</span>
              </a>
            </li>
            @endif


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
                <a href="{{route('root-campaign-report')}}">
                  <span>Campaign Report (NEW**)</span>
                </a>
              </li>
              <li>
                <a href="{{route('root-clients-sms-report')}}">
                  <span>DLR Report</span>
                </a>
              </li>
              
              <li>
                <a href="{{route('root-client-archive-campaign-report')}}">
                  <span>Archive Report</span>
                </a>
              </li>
              <li>
                <a href="{{route('currentday-gateway-errors')}}">
                  <span>Gateway Errors</span>
                </a>
              </li>
              <!-- <li>
                <a href="{{route('root-clients-send-sms-count')}}">
                  <span>Campaign Report</span>
                </a>
              </li> -->
              
            </ul>
          </li>


          <li>
              <a href class="auto">
                <span class="pull-right text-muted">
                  <i class="fa fa-fw fa-angle-right text"></i>
                  <i class="fa fa-fw fa-angle-down text-active"></i>
                </span>
                <i class="glyphicon glyphicon-list"></i>
                <span>Account's Chart</span>
              </a>
              <ul class="nav nav-sub dk">
                <li class="nav-sub-header">
                  <a href>
                    <span>Account's Chart</span>
                  </a>
                </li>
                <li>
                  <a href="{{route('manage-root-accounts')}}">
                    <span>Root Accounts</span>
                  </a>
                </li>
                <li>
                  <a href="{{route('manage-group-accounts')}}">
                    <span>Group Accounts</span>
                  </a>
                </li>
                <li>
                  <a href="{{route('manage-bottom-accounts')}}">
                    <span>Transection Accounts</span>
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
                <i class="glyphicon glyphicon-briefcase icon"></i>
                <span>Settings</span>
              </a>
              <ul class="nav nav-sub dk">
                <li class="nav-sub-header">
                  <a href>
                    <span>Settings</span>
                  </a>
                </li>
                <li>
                  <a href="{{route('root-managers')}}">
                    <span>Manage Staff</span>
                  </a>
                </li>
                <li>
                  <a href="{{route('show-operators')}}">
                    <span>Operators</span>
                  </a>
                </li>
                <li>
                  <a href="{{route('show-gateways')}}">
                    <span>Gateways</span>
                  </a>
                </li>
                <li>
                  <a href="{{route('manage-modems')}}">
                    <span>Manage Modems</span>
                  </a>
                </li>
                <li>
                  <a href="{{route('edit-divider')}}">
                    <span>Manage Divider</span>
                  </a>
                </li>
                <li>
                  <a href="{{route('smsadmin-settings')}}">
                    <span>General Settings</span>
                  </a>
                </li>
                
                <li>
                  <a href="{{route('root-users')}}">
                    <span>Root Users</span>
                  </a>
                </li>
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