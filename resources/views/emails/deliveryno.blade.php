@component('mail::message')
# Good Day!


{{$data['subject']}} - {{$data['dln']}}<br>
@if($data['fwd_no']!=null)
Forward no. - {{$data['fwd_no']}}<br>
@else
@endif
PO no. - {{$data['po']}}<br>
PR no. - {{$data['body']}}
<!-- @component('mail::button', ['url' => ''])
Button Text
@endcomponent -->

Thanks,<br>
{{ config('app.name') }}
@endcomponent