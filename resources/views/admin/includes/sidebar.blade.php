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
                        class="nav-link {{ \Request::segment(1)==='notify' ? 'active-sidebar' : '' }}">
                        <img src="{{asset('/assets/images/notice_icon.png')}}" alt="notice_icon">
                        <span class="title">
                            &nbsp;お知らせ一覧
                        </span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#"
                       class="nav-link {{ \Request::segment(1)==='notify' ? 'active-sidebar' : '' }} sendEmailToAllUser last">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-envelope" viewBox="0 0 16 16">
                            <path d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V4zm2-1a1 1 0 0 0-1 1v.217l7 4.2 7-4.2V4a1 1 0 0 0-1-1H2zm13 2.383-4.758 2.855L15 11.114v-5.73zm-.034 6.878L9.271 8.82 8 9.583 6.728 8.82l-5.694 3.44A1 1 0 0 0 2 13h12a1 1 0 0 0 .966-.739zM1 11.114l4.758-2.876L1 5.383v5.73z"/>
                        </svg>
                        <span class="title">
                            &nbsp;メール送信
                        </span>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</aside>
