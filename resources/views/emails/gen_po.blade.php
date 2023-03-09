@component('mail::message')

# Good Day!

{{$data['subject']}} - {{$data['po']}}<br>
{{$data['body']}}

Thanks,<br>
{{ config('app.name') }}
@endcomponent