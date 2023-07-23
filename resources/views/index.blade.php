@extends('layouts.app')

@section('content')
<div class="container">

    <span class="text">your code is:</span>
    
    <div class="d-flex justify-content-center">

        @foreach ($digitArr as $digit)
        <div class="small-box">
            <span class="number">{{$digit}}</span>
        </div>
        @endforeach
    </div>
</div>
@endsection