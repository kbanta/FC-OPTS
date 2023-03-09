@component('mail::message')
# Good Day!

{{$data['subject']}}<br>
{{$data['body']}}

<!-- @component('mail::button', ['url' => ''])
Button Text
@endcomponent -->

Thanks,<br>
{{ config('app.name') }}
@endcomponent