<ul class="nav nav-pills nav-sm">
    @if(Route::currentRouteName() == 'reseller-profile-index')
    <li class="active"><a href="/reseller-profile/{{$user->id}}/index"><i class="icon-speedometer"></i> Dashboard</a></li>
    @else 
    <li><a href="/reseller-profile/{{$user->id}}/index"><i class="icon-speedometer"></i> Dashboard</a></li>
    @endif
    @if(Route::currentRouteName() == 'reseller-profile')
    <li class="active"><a href="/reseller-profile/{{$user->id}}/profile"><i class="icon-pencil"></i> Edit Profile</a></li>
    @else 
    <li><a href="/reseller-profile/{{$user->id}}/profile"><i class="icon-pencil"></i> Edit Profile</a></li>
    @endif
    
    @if(Route::currentRouteName() == 'reseller-document')
    <li class="active"><a href="/reseller-profile/{{$user->id}}/document-upload"><i class="icon-cloud-upload"></i> Upload Documents</a></li>
    @else 
    <li><a href="/reseller-profile/{{$user->id}}/document-upload"><i class="icon-cloud-upload"></i> Upload Documents</a></li>
    @endif

    @if(Route::currentRouteName() == 'reseller-profile-senderid')
    <li class="active"><a href="/reseller-profile/{{$user->id}}/senderid"><i class="icon-tag"></i> Manage SenderID</a></li>
    @else 
    <li><a href="/reseller-profile/{{$user->id}}/senderid"><i class="icon-tag"></i> Manage SenderID</a></li>
    @endif


    @if(Route::currentRouteName() == 'reseller-profile-invoice' || Route::currentRouteName() == 'reseller-profile-smssale')
    <li class="active"><a href="/reseller-profile/{{$user->id}}/invoice"><i class="icon-tag"></i> Manage Invoice</a></li>
    @else 
    <li><a href="/reseller-profile/{{$user->id}}/invoice"><i class="icon-tag"></i> Manage Invoice</a></li>
    @endif
</ul>