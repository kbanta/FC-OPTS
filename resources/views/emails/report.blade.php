@component('mail::message')
# Good Day!

{{$data['subject']}}<br>
Report# - {{$data['body']}}

@component('mail::button', ['url' => 'http://127.0.0.1:8000/processor/reported_items'])
View Report Items
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent