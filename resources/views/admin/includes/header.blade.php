<header class="header">
    <a href="#" class="header-logo">
        <img src="{{asset('/assets/images/left_logo.png')}}" alt="logo">
    </a>
    @if (Request::segment(1) !== "login")
    <ul class="header-item">
        <li>
            <a href="#">お知らせ配信</a>
        </li>
        <li>
            <img src="{{asset('/assets/images/right_logo.png')}}" alt="avatar admin">
        </li>
    </ul>
    @endif
</header>