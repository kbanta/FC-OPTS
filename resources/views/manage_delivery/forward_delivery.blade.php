@extends('manage_order.view_ordered_po')

@section('forward')
@if(Auth::user()->hasRole('Processor'))
@if(!empty($forwarded))
@else
<button data-toggle="modal" data-target="#forwardDelivery" class="btn btn-warning "><i class="fa fa-share"> Send for Approval</i></button>
<!-- <a href="#" data-toggle="modal" data-target="#forwardDelivery" class="btn btn-warning "> <i class="fa fa-share"> Send for Approval</i></a> -->
@endif
@endif
@include('manage_delivery.forward_delivery_modal')
@endsection