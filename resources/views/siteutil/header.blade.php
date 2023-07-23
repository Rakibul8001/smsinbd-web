<!-- header -->
<header id="header" class="app-header navbar" role="menu">
    <!-- navbar header -->
    <div class="navbar-header bg-dark bg-success">
      <button class="pull-right visible-xs dk" ui-toggle-class="show" target=".navbar-collapse">
        <i class="glyphicon glyphicon-cog"></i>
      </button>
      <button class="pull-right visible-xs" ui-toggle-class="off-screen" target=".app-aside" ui-scroll="app">
        <i class="glyphicon glyphicon-align-justify"></i>
      </button>
      <!-- brand -->
      <a href="#/" class="navbar-brand text-lt">
        {{-- <i class="fa fa-btc"></i> --}}
        <img src="{{ asset('/images/logo.png') }}" alt=".">
        
        <span class="hidden-folded m-l-xs">Administrator</span>
      </a>
      <!-- / brand -->
    </div>
    <!-- / navbar header -->

    <!-- navbar collapse -->
    <div class="collapse pos-rlt navbar-collapse box-shadow bg-white-only">
      <!-- buttons -->

      <div class="nav navbar-nav hidden-xs">
        <a href="#" class="btn no-shadow navbar-btn" ui-toggle-class="app-aside-folded" target=".app">
          <i class="fa fa-dedent fa-fw text"></i>
          <i class="fa fa-indent fa-fw text-active"></i>
        </a>
        <a href="#" class="btn no-shadow navbar-btn" ui-toggle-class="show" target="#aside-user">
          <i class="icon-user fa-fw"></i>
        </a>
      </div>
      @if(Auth::guard('web')->check())
      @if(session()->has('totalmaskbal') || session()->has('totalnonmaskbal') || session()->has('totalvoicebal'))
      <div class="nav navbar-nav hidden-xs">
        <a href="#" class="btn no-shadow navbar-btn" ui-toggle-class="app-aside-folded" target=".app">
        <span style="font-size: 14px; margin-right:20px; font-family: Verdana;"> Masking Balance: <span class="text-white-dk font-thin font-bold text-danger maskbal">{{ Auth::guard('web')->user()->mask_balance }} </span> SMS</span>
        <span style="font-size: 14px; margin-right:20px; font-family: Verdana;">  Non Masking Balance: <span class="text-white-dk font-thin font-bold text-success nonmaskbal">{{ Auth::guard('web')->user()->nonmask_balance }}  </span> SMS</span>
  <!--  <span style="font-size: 14px; margin-right:20px;font-family: Roboto;">  Voice SMS Balance</span> <span class="text-white-dk font-thin font-bold voicebal">{{ Auth::guard('web')->user()->voice_balance }}</span> -->
          
        </a>
       
      </div>
      @endif
      @endif
      <!-- / buttons -->

      <!-- link and dropdown -->
      
      <!-- / link and dropdown -->


      <!-- nabar right -->
      <ul class="nav navbar-nav navbar-right">
        <li class="dropdown">
          <!-- <a href="#" data-toggle="dropdown" class="dropdown-toggle">
            <i class="icon-bell fa-fw"></i>
            <span class="visible-xs-inline">Notifications</span>
            <span class="badge badge-sm up bg-danger pull-right-xs">2</span>
          </a> -->
          <!-- dropdown -->
          <div class="dropdown-menu w-xl animated fadeInUp">
            <div class="panel bg-white">
              <div class="panel-heading b-light bg-light">
                <strong>You have <span>2</span> notifications</strong>
              </div>
              <div class="list-group">
                <a href class="list-group-item">
                  <span class="pull-left m-r thumb-sm">
                    <img src="img/a0.jpg" alt="..." class="img-circle">
                  </span>
                  <span class="clear block m-b-none">
                    Use awesome animate.css<br>
                    <small class="text-muted">10 minutes ago</small>
                  </span>
                </a>
                <a href class="list-group-item">
                  <span class="clear block m-b-none">
                    1.0 initial released<br>
                    <small class="text-muted">1 hour ago</small>
                  </span>
                </a>
              </div>
              <div class="panel-footer text-sm">
                <a href class="pull-right"><i class="fa fa-cog"></i></a>
                <a href="#notes" data-toggle="class:show animated fadeInRight">See all the notifications</a>
              </div>
            </div>
          </div>
          <!-- / dropdown -->
        </li>
        <li class="dropdown">
          <a href="#" data-toggle="dropdown" class="dropdown-toggle clear" data-toggle="dropdown">
            <span class="thumb-sm avatar pull-right m-t-n-sm m-b-n-sm m-l-sm">
              
             <i class="on md b-white bottom"></i>
            </span>
            <i class="icon-user icon" style="margin-right:5px;"></i> 
            <span class="hidden-sm hidden-md"><?php echo @Auth::user()->name; ?></span> <b class="caret"></b>
          </a>
          <!-- dropdown -->
          <ul class="dropdown-menu animated fadeInRight w">
            <!-- <li class="wrapper b-b m-b-sm bg-light m-t-n-xs">
              <div>
                <p>300mb of 500mb used</p>
              </div>
              <div class="progress progress-xs m-b-none dker">
                <div class="progress-bar progress-bar-info" data-toggle="tooltip" data-original-title="50%" style="width: 50%"></div>
              </div>
            </li>  -->
            <!-- <li>
              <a href>
                <span class="badge bg-danger pull-right">30%</span>
                <span>Settings</span>
              </a>
            </li>
            <li>
              <a ui-sref="app.page.profile">Profile</a>
            </li>
            <li>
              <a ui-sref="app.docs">
                <span class="label bg-info pull-right">new</span>
                Help
              </a>
            </li>
            <li class="divider"></li> -->
            <li>
              @if(Auth::guard('root')->check())
                <a href="{{route('root-users')}}">Profile</a>
              @elseif(Auth::guard('reseller')->check())
                <a href="{{route('reseller')}}">Profile</a>
              @elseif(Auth::guard('web')->check())
              <a href="{{route('client-settings',[auth()->user()->id])}}">Profile</a>
              @endif
            </li>
            <li class="divider"></li>
            <li>
              <a href="{{route('userlogout')}}">Logout</a>
            </li>
          </ul>
          <!-- / dropdown -->
        </li>
      </ul>
      <!-- / navbar right -->
    </div>
    <!-- / navbar collapse -->
</header>
<!-- / header -->