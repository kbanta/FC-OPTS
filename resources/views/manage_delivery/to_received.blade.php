@extends('adminltelayout.layout')
@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Order P.O.</h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                    <li class="breadcrumb-item active">Order</li>
                </ol>
            </div><!-- /.col -->
        </div><!-- /.row -->
    </div><!-- /.container-fluid -->
</div>

<div class="col-12">
    <ul id="clothing-nav" class="nav nav-tabs" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" href="#delivery" role="tab" id="delivery-tab" data-toggle="tab" aria-controls="delivery">To Received</a>
        </li>
        <li class="nav-item">
            <a class="nav-link " href="#received" role="tab" id="received-tab" data-toggle="tab" aria-controls="received">Received</a>
        </li>
    </ul>
    <br>
    <div id="clothing-nav-content" class="tab-content">
        <div role="tabpanel" class="tab-pane fade show active" id="delivery" aria-labelledby="delivery-tab">
            <div class="card">
                <div class="row">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="order_po" class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>Delivery no.</th>
                                        <th>Purchase Order no.</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        @foreach($to_received as $received)
                                        <input type="hidden" id="pr_id" name="pr_id" value="{{$received->id}}" />
                                        <td class="pr_no_for_canvass">{{$received->delivery_no}}</td>
                                        <td class="pr_purpose">{{$received->po_no}}</td>
                                        <td>
                                            @if(Auth::user()->hasRole('Requestor'))
                                            <a href="{{route('view_po_to_received',[$pr_no = $received->pr_no,$po_no = $received->po_no] )}}" class="btn btn-danger btn-block btn-sm view_btn"><i class="fa fa-eye"></i>View</a>
                                            @endif
                                            @if(Auth::user()->hasRole('Approver'))
                                            <a href="{{route('view_po_to_received_app',[$pr_no = $received->pr_no,$po_no = $received->po_no] )}}" class="btn btn-danger btn-block btn-sm view_btn"><i class="fa fa-eye"></i>View</a>
                                            @endif
                                            @if(Auth::user()->hasRole('Validator'))
                                            <a href="{{route('view_po_to_received_val',[$pr_no = $received->pr_no,$po_no = $received->po_no] )}}" class="btn btn-danger btn-block btn-sm view_btn"><i class="fa fa-eye"></i>View</a>
                                            @endif
                                            @if(Auth::user()->hasRole('Processor'))
                                            <a href="{{route('view_po_to_received_pro',[$pr_no = $received->pr_no,$po_no = $received->po_no] )}}" class="btn btn-danger btn-block btn-sm view_btn"><i class="fa fa-eye"></i>View</a>
                                            @endif
                                            @if(Auth::user()->hasRole('Administrator'))
                                            <a href="{{route('view_po_to_received_ad',[$pr_no = $received->pr_no,$po_no = $received->po_no] )}}" class="btn btn-danger btn-block btn-sm view_btn"><i class="fa fa-eye"></i>View</a>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div role="tabpanel" class="tab-pane fade" id="received" aria-labelledby="received-tab">
            <div class="card">
                <div class="row">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="order_po2" class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>Delivery no.</th>
                                        <th>Purchase Order no.</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        @foreach($to_received as $received)
                                        <input type="hidden" id="pr_id" name="pr_id" value="{{$received->id}}" />
                                        <td class="pr_no_for_canvass">{{$received->delivery_no}}</td>
                                        <td class="pr_purpose">{{$received->po_no}}</td>
                                        <td>
                                            
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        $("#order_po").DataTable({});
        $("#order_po2").DataTable({});
    });
</script>
@endsection