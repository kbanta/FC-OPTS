@component('mail::message')

# Good Day!

{{$data['subject']}}<br>
{{$data['body']}}
@if($data['subject']=='New Purchase Request')
@component('mail::button', ['url' => 'http://127.0.0.1:8000/approver/new_pr'])
View New PR!
@endcomponent
@elseif($data['subject']=='PR is ready for Canvass')
@component('mail::button', ['url' => 'http://127.0.0.1:8000/processor/pr_for_canvass'])
View PR for Canvass!
@endcomponent
@elseif($data['subject']=='PR has been Canvassed')
@component('mail::button', ['url' => 'http://127.0.0.1:8000/approver/pr_for_verification'])
View Canvassed PR!
@endcomponent
@elseif($data['subject']=='Supplier has been Selected')
@component('mail::button', ['url' => 'http://127.0.0.1:8000/validator/pr_check_fund'])
View PR Check for Fund!
@endcomponent
@elseif($data['subject']=='Checked')
@component('mail::button', ['url' => 'http://127.0.0.1:8000/approver/pr_for_approval'])
View PR for Approval!
@endcomponent
@elseif($data['subject']=='PR has been approved by Corporate Treasurer')
@component('mail::button', ['url' => 'http://127.0.0.1:8000/approver/pr_for_approval'])
View PR for Approval!
@endcomponent
@elseif($data['subject']=='Purchase Request has been Approved!')
@component('mail::button', ['url' => 'http://127.0.0.1:8000/processor/pr_to_po'])
View Approved PR!
@endcomponent




Thanks,<br>
{{ config('app.name') }}
@else
@endif
@endcomponent