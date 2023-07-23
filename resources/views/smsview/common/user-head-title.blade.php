@if(Auth::guard('root')->check())
    <h1 class="m-n font-thin h3 text-black font-bold">Root Panel</h1>
@elseif(Auth::guard('manager')->check())
    <h1 class="m-n font-thin h3 text-black font-bold">Manager Panel</h1>
@elseif(Auth::guard('reseller')->check())
    <h1 class="m-n font-thin h3 text-black font-bold">Reseller Panel</h1>
@elseif(Auth::guard('web')->check())
    <h1 class="m-n font-thin h3 text-black font-bold">Client Panel</h1>
@endif