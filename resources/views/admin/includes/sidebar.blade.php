<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <div class="sidebar">
        <nav class="mt-1">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <li class="nav-item">
                    <a href="{{route('admin.users.list')}}"
                        class="nav-link {{ \Request::segment(1)==='user' ? 'active-sidebar' : '' }}">
                        <img src="{{asset('/assets/images/list_user_icon.png')}}" alt="list_user_icon">
                        <span class="title">
                            &nbsp;ユーザー一覧
                        </span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{route('admin.portfolio.list')}}"
                        class="nav-link {{ \Request::segment(1)==='portfolio' ? 'active-sidebar' : '' }}">
                        <img src="{{asset('/assets/images/portfolio_icon.svg')}}" alt="portfolio_icon">
                        <span class="title">
                            &nbsp;ポートフォリオ一覧
                        </span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{route('admin.notify.list')}}"
                        class="nav-link {{ \Request::segment(1)==='notify' ? 'active-sidebar' : '' }} last">
                        <img src="{{asset('/assets/images/notice_icon.png')}}" alt="notice_icon">
                        <span class="title">
                            &nbsp;お知らせ一覧
                        </span>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</aside>