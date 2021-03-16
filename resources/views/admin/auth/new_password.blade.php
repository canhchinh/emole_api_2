@extends('admin.layouts.default')
@section('content')
<form action="{{ route('admin.auth.post.newPassword') }}" method="POST">
    @csrf
    <input type="hidden" name="token_reset" value="{{token}}" />
    <input type="password" name="password" placeholder="password">
    <input type="email" name="email" placeholder="confirm password">
    <button type="submit">submit</button>
</form>
@stop