

@component('mail::layout')

@component('mail::message')
<p>この度は、emoleへ登録していただき、 ありがとうございます。</p>
<p>以下のボタンからメールアドレスを認証して 登録を完了してください。ボタン：</p>
@component('mail::button', ['url' => $urlActive.$token])
{{ trans('登録を完了する')}}
@endcomponent
@endcomponent

@endcomponent
