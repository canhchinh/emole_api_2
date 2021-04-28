@component('mail::message')
<h3>Hello</h3>
<p>Please click on the url to activate your account</p>
@component('mail::button', ['url' => $urlActive.$token])
{{ trans('Active now')}}
@endcomponent
@endcomponent