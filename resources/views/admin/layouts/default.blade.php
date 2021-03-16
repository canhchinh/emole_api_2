<!doctype html>
<html>

<head>
    @include('admin.includes.meta')
</head>

<body>
    <div class="container-fluid">

        <header class="row">
            @include('admin.includes.header')
        </header>

        <div id="main" class="row">
            <div class="col-2 col-sidebar">
                @if (Request::segment(1) !== "login")
                @include('admin.includes.sidebar')
                @endif
            </div>
            <div class="col-10">
                @yield('content')
            </div>
        </div>

        <footer class="row">
            @include('admin.includes.footer')
        </footer>

    </div>
</body>

</html>