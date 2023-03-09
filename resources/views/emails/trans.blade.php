@component('mail::message')
# Good Day!

{{$data['subject']}} <br>
Forward no. - {{$data['fwd_no']}}<br>
Delivery no. - {{$data['dln']}}<br>
PO no. - {{$data['po']}}<br>
PR no. - {{$data['body']}}

@if($data['subject']=='Purchase Order has been forwarded to ASSD to be approved')
@component('mail::button', ['url' => 'http://127.0.0.1:8000/approver/to_transmit'])
View Transmittal!
@endcomponent
@elseif($data['subject']=='Item Received')
@component('mail::button', ['url' => 'http://127.0.0.1:8000/processor/deliveries'])
View Transmittal!
@endcomponent



Thanks,<br>
{{ config('app.name') }}
@else
@endif
@endcomponent