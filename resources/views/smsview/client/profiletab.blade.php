<ul class="nav nav-pills nav-sm">
    @if(Route::currentRouteName() == 'myprofile-index')
    <li class="active"><a href="/myprofile/{{$user->id}}/index"><i class="icon-speedometer"></i> Dashboard</a></li>
    @else 
    <li><a href="/myprofile/{{$user->id}}/index"><i class="icon-speedometer"></i> Dashboard</a></li>
    @endif
    @if(Route::currentRouteName() == 'myprofile-edit')
    <li class="active"><a href="/myprofile/{{$user->id}}/edit"><i class="icon-pencil"></i> Edit Profile</a></li>
    @else 
    <li><a href="/myprofile/{{$user->id}}/edit"><i class="icon-pencil"></i> Edit Profile</a></li>
    @endif
    
    @if(Route::currentRouteName() == 'myprofile-document')
    <li class="active"><a href="/myprofile/{{$user->id}}/document-upload"><i class="icon-cloud-upload"></i> Upload Documents</a></li>
    @else 
    <li><a href="/myprofile/{{$user->id}}/document-upload"><i class="icon-cloud-upload"></i> Upload Documents</a></li>
    @endif

    @if(Route::currentRouteName() == 'myprofile-senderid')
    <li class="active"><a href="/myprofile/{{$user->id}}/senderid"><i class="icon-tag"></i> Manage SenderID</a></li>
    @else 
    <li><a href="/myprofile/{{$user->id}}/senderid"><i class="icon-tag"></i> Manage SenderID</a></li>
    @endif

    @if(Route::currentRouteName() == 'myprofile-template')
    <li class="active"><a href="/myprofile/{{$user->id}}/template"><i class="icon-tag"></i> Manage Templates</a></li>
    @else 
    <li><a href="/myprofile/{{$user->id}}/template"><i class="icon-tag"></i> Manage Templates</a></li>
    @endif

    @if(Route::currentRouteName() == 'myprofile-invoice')
    <li class="active"><a href="/myprofile/{{$user->id}}/invoice"><i class="icon-wallet"></i> Recharge History</a></li>
    @else 
    <li><a href="/myprofile/{{$user->id}}/invoice"><i class="icon-wallet"></i> Recharge History</a></li>
    @endif
</ul>