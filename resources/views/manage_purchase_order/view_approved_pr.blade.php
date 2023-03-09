@extends('manage_purchase_request.view_approved_pr')

@section('generate_po')
@if(!empty($po[0]['po_no']))

@else
<div class="modal-footer" id="print_to_po">
    <div class="btn-group btn-group" role="group" aria-label="...">
        <a href="" data-toggle="modal" data-target="#addGeneratePO" class="btn btn-success">
            <i class="fa fa-plus"> Generate Purchase Order</i>
        </a>
        <a class="btn btn-warning print_btn">
            <i class="fa fa-print"> Print</i>
        </a>
    </div>
</div>
@endif

@include('manage_purchase_order.generate_PO')
@endsection