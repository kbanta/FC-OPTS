@component('mail::message')
# Good Day!

{{$data['subject']}} <br>
PO# - {{$data['po']}}<br>
from PR# - {{$data['body']}}

@if($data['subject']=='Generated Purchase Order')
@component('mail::button', ['url' => 'http://127.0.0.1:8000/processor/purchase_order'])
View Purchase Order!
@endcomponent
@elseif($data['subject']=='Purchase Order has been Prepared')
@component('mail::button', ['url' => 'http://127.0.0.1:8000/validator/po_for_approval'])
View PO for Approval!
@endcomponent
@elseif($data['subject']=='Purchase Order has been Verified')
@component('mail::button', ['url' => 'http://127.0.0.1:8000/approver/po_for_approval'])
View PO for Approval!
@endcomponent
@elseif($data['subject']=='Purchase Order has been Approved')
@component('mail::button', ['url' => 'http://127.0.0.1:8000/processor/order_po'])
View Approved PO!
@endcomponent



Thanks,<br>
{{ config('app.name') }}
@else
@endif
@endcomponent