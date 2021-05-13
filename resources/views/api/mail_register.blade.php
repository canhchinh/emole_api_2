

@component('mail::layout')
{{-- Header --}}
@slot ('header')
{{-- @component('mail::header', ['url' => config('app.url')]) --}}
    <!-- header -->
{{-- @endcomponent --}}
@endslot

{{-- Content here --}}
@component('mail::message')
<p>この度は、emoleへ登録していただき、 ありがとうございます。</p>
<p>以下のボタンからメールアドレスを認証して 登録を完了してください。ボタン：</p>
@component('mail::button', ['url' => $urlActive.$token])
{{ trans('登録を完了する')}}
@endcomponent
@endcomponent

{{-- Subcopy --}}
@slot('subcopy')
{{-- @component('mail::subcopy') --}}
<!-- subcopy -->
{{-- @endcomponent --}}
@endslot

{{-- Footer --}}
@slot ('footer')
{{-- @component('mail::footer') --}}
<!-- footer -->
{{-- @endcomponent --}}
@endslot
@endcomponent
