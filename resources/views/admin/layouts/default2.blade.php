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

            @if ((Request::segment(1) !== "login")
            && (Request::segment(1) !== "notify" || Request::segment(2) !== "create"))
            <div class="col-2 col-sidebar">
                @include('admin.includes.sidebar')
            </div>
            @endif
            <div class="col-12">
                @yield('content')
            </div>
        </div>

        <footer class="row">
            @include('admin.includes.footer')
        </footer>

    </div>
</body>

</html>