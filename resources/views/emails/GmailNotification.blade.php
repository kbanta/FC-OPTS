@component('mail::message')

# Good Day!

{{$data['subject']}}<br>
{{$data['body']}}

Thanks,<br>
{{ config('app.name') }}
@endcomponent