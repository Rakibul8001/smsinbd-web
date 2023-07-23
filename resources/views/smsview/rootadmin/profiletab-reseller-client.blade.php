<ul class="nav nav-pills nav-sm">
    @if(Route::currentRouteName() == 'reseller-client-profile-index')
    <li class="active"><a href="/reseller-client-profile/{{$user->id}}/index"><i class="icon-speedometer"></i> Dashboard</a></li>
    @else 
    <li><a href="/reseller-client-profile/{{$user->id}}/index"><i class="icon-speedometer"></i> Dashboard</a></li>
    @endif
    @if(Route::currentRouteName() == 'reseller-client-profile')
    <li class="active"><a href="/reseller-client-profile/{{$user->id}}/profile"><i class="icon-pencil"></i> Edit Profile</a></li>
    @else 
    <li><a href="/reseller-client-profile/{{$user->id}}/profile"><i class="icon-pencil"></i> Edit Profile</a></li>
    @endif
    
    @if(Route::currentRouteName() == 'reseller-client-document')
    <li class="active"><a href="/reseller-client-profile/{{$user->id}}/document-upload"><i class="icon-cloud-upload"></i> Upload Documents</a></li>
    @else 
    <li><a href="/reseller-client-profile/{{$user->id}}/document-upload"><i class="icon-cloud-upload"></i> Upload Documents</a></li>
    @endif

    @if(Route::currentRouteName() == 'reseller-client-profile-senderid')
    <li class="active"><a href="/reseller-client-profile/{{$user->id}}/senderid"><i class="icon-tag"></i> Manage SenderID</a></li>
    @else 
    <li><a href="/reseller-client-profile/{{$user->id}}/senderid"><i class="icon-tag"></i> Manage SenderID</a></li>
    @endif

    @if(Route::currentRouteName() == 'reseller-client-profile-template')
    <li class="active"><a href="/reseller-client-profile/{{$user->id}}/template"><i class="icon-tag"></i> Manage Templates</a></li>
    @else 
    <li><a href="/reseller-client-profile/{{$user->id}}/template"><i class="icon-tag"></i> Manage Templates</a></li>
    @endif

    @if(Route::currentRouteName() == 'reseller-client-profile-invoice' || Route::currentRouteName() == 'client-profile-smssale')
    <li class="active"><a href="/reseller-client-profile/{{$user->id}}/invoice"><i class="icon-tag"></i> Manage Invoice</a></li>
    @else 
    <li><a href="/reseller-client-profile/{{$user->id}}/invoice"><i class="icon-tag"></i> Manage Invoice</a></li>
    @endif
</ul>