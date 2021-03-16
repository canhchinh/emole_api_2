@extends('admin.layouts.default')
@section('content')
<form action="{{ route('admin.auth.login') }}" method="POST">
    @csrf
    <input type="email" name="email" placeholder="email forgot password">
    <button type="submit">submit</button>
</form>
@stop