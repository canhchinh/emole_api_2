@extends('admin.layouts.default')
@section('content')
<div class="wrapper">
    <div class="logo">
        <img src="{{asset('/assets/images/logo_emole.svg')}}" alt="logo-emole.svg">
    </div>
    <div class="title">
        <span>管理画面ログイン</span>
    </div>
    <div class="form">
        <form action="{{ route('admin.auth.login') }}" method="POST">
            @csrf
            <div class="contain">
                <label for="username">メールアドレス</label><br>
                <input type="text" id="username" name="username" placeholder="メールアドレスを入力してください" required>
            </div>
            <div class="contain">
                <label for="password">パスワード</label><br>
                <input type="password" id="password" name="password" placeholder="パスワードを入力してください" required>
            </div>
            <div class="footer">
                <button type="submit">ログイン</button>
            </div>
        </form>
    </div>
</div>
@stop