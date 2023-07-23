<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'SMSinBD') }}</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

    <style>
        body {
            background: linear-gradient(to bottom, #3498db, #9b59b6) !important;
        }

        .container {
        text-align: center !important;
        height: 100vh !important;
        display: flex !important;
        flex-direction: column !important;
        justify-content: center !important;
        }


        .text {
        font-size: 24px;
        margin-bottom: 10px;
        }

        .small-box {
        display: inline-block;
        width: 50px;
        height: 50px;
        background-color: #f1f1f1;
        border: 1px solid #ccc;
        }

        .number {
        font-size: 20px;
        }



    </style>
</head>
<body>
    <div id="app">

        <main class="py-4">
            @yield('content')
        </main>
    </div>
</body>
</html>
