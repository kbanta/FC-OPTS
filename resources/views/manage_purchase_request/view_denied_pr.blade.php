@extends('manage_purchase_request.view_purchase_requestt')

@section('deny_message')
@if(!empty($deny_pr[0]['deny_message']))
<div class="form-group">
  <label for="comment"><span class="badge badge-danger" style="font-size: 15px;">Message:</span></label>
  <textarea class="form-control" id="comment" readonly>{{$deny_pr[0]['deny_message']}}</textarea>
</div>
@endif
@endsection