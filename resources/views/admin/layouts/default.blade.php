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
            <div class="col-10">
                @yield('content')
            </div>
        </div>

        <footer class="row">
            @include('admin.includes.footer')
        </footer>

    </div>

    <!-- Modal -->
    <div class="modal" id="send-email-to-all-users" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header border-bottom-0">
                    <h5 class="modal-title" id="exampleModalLabel">メール送信</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('admin.user.sendEmailAll') }}" method="POST" id="sendEmailToAllUserForm">
                    @csrf
                    <input type="hidden" name="user_id" value="0">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="email1">メールの件名</label>
                            <input type="text" class="form-control" id="email_subject" name="email_subject" placeholder="メールの件名" required minlength="2">
                        </div>
                        <div class="form-group">
                            <label for="email-content">メールの内容</label>
                            <textarea class="form-control" id="email_content" name="email_content" placeholder="メールの内容" rows="5" required minlength="10"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer border-top-0 d-flex justify-content-center">
                        <div class="w-100 ajax-response text-success text-center"></div>
                        <div class="w-100 ajax-response text-danger text-center"></div>
                        <button type="submit" class="btn btn-dark" data-dismiss="modal">キャンセル</button>
                        <input class="btn btn-primary" type="submit" value="送信">
                    </div>
                </form>
            </div>
        </div>
    </div>


</body>

</html>
