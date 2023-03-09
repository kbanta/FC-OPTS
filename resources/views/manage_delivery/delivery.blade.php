@extends('adminltelayout.layout')
@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Deliveries</h1>
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
            <a class="nav-link active" href="#delivery" role="tab" id="delivery-tab" data-toggle="tab" aria-controls="delivery">With Delivery No.</a>
        </li>
        <!-- <li class="nav-item">
            <a class="nav-link" href="#received_del" role="tab" id="received_del-tab" data-toggle="tab" aria-controls="received_del">Received Delivery</a>
        </li> -->
        <li class="nav-item">
            <a class="nav-link" href="#for_approval" role="tab" id="for_approval-tab" data-toggle="tab" aria-controls="for_approval">Approval of Transmittal</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#delivery_received" role="tab" id="delivery_received-tab" data-toggle="tab" aria-controls="delivery_received">Forwarded to Requestor</a>
        </li>
    </ul>
    <br>
    <div id="clothing-nav-content" class="tab-content">
        <div role="tabpanel" class="tab-pane fade show active" id="delivery" aria-labelledby="delivery-tab">
            <div class="card">
                @if($delivery->isEmpty())
                @else
                <div class="row">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="table1" class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>Delivery no.</th>
                                        <th>Order no.</th>
                                        <th>Date</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        @foreach($delivery as $dlnn)
                                        <input type="hidden" id="pr_id" name="pr_id" value="{{$dlnn->id}}" />
                                        <td class="pr_no_for_canvass">{{$dlnn->delivery_no}}</td>
                                        <td class="pr_purpose">{{$dlnn->order_no}}</td>
                                        <td class="pr_purpose">{{$dlnn->created_at}}</td>
                                        <td>
                                            <a href="{{route('view_po_deliveries',[$pr_no = $dlnn->pr_no,$po_no = $dlnn->po_no,$dln = $dlnn->delivery_no,$order_no = $dlnn->order_no,$sid = $dlnn->supplier_id] )}}" class="btn btn-danger btn-block btn-sm view_btn"><i class="fa fa-eye"></i>View</a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
        <div role="tabpanel" class="tab-pane fade" id="for_approval" aria-labelledby="for_approval-tab">
            <div class="card">
                @if($send_for_approval->isEmpty())
                @else
                <div class="row">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="table2" class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>Delivery no.</th>
                                        <th>Order no.</th>
                                        <th>Date</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        @foreach($send_for_approval as $sfa)
                                        <input type="hidden" id="pr_id" name="pr_id" value="{{$sfa->id}}" />
                                        <td class="pr_no_for_canvass">{{$sfa->delivery_no}}</td>
                                        <td class="pr_purpose">{{$sfa->order_no}}</td>
                                        <td class="pr_purpose">{{$sfa->forwardedDate}}</td>
                                        <td>
                                            <a href="{{route('view_deliveries_for_approval',[$pr_no = $sfa->pr_no,$po_no = $sfa->po_no,$dln = $sfa->delivery_no,$order_no = $sfa->order_no,$sid = $sfa->supplier_id] )}}" class="btn btn-danger btn-block btn-sm view_btn"><i class="fa fa-eye"></i>View</a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
        <div role="tabpanel" class="tab-pane fade" id="received_del" aria-labelledby="received_del-tab">
            <div class="card">
                <div class="row">
                    @if($received->isEmpty())
                    @else
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="table3" class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>Delivery no.</th>
                                        <th>Purchase Order no.</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        @foreach($received as $sfa)
                                        <input type="hidden" id="pr_id" name="pr_id" value="{{$sfa->id}}" />
                                        <td class="pr_no_for_canvass">{{$sfa->delivery_no}}</td>
                                        <td class="pr_purpose">{{$sfa->po_no}}</td>
                                        <td>
                                            <a href="{{route('view_po_deliveries',[$pr_no = $sfa->pr_no,$po_no = $sfa->po_no,$order_no = $dlnn->order_no,$sid = $dlnn->supplier_id] )}}" class="btn btn-danger btn-block btn-sm view_btn"><i class="fa fa-eye"></i>View</a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        <div role="tabpanel" class="tab-pane fade" id="delivery_received" aria-labelledby="delivery_received-tab">
            <div class="card">
                <div class="row">
                    @if($del_received->isEmpty())
                    @else
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="table4" class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>Delivery no.</th>
                                        <th>Purchase Order no.</th>
                                        <th>Date</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        @foreach($del_received as $dr)
                                        <input type="hidden" id="pr_id" name="pr_id" value="{{$dr->id}}" />
                                        <td class="pr_no_for_canvass">{{$dr->delivery_no}}</td>
                                        <td class="pr_purpose">{{$dr->po_no}}</td>
                                        <td class="pr_purpose">{{$dr->reqreceivedDate}}</td>
                                        <td>
                                            <a href="{{route('view_forwarded_pro',[$dln = $dr->delivery_no,$po_no = $dr->po_no,$staff_id = $dr->staff_id] )}}" class="btn btn-danger btn-block btn-sm view_btn"><i class="fa fa-eye"></i>View</a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        $("#table1").DataTable({
            order: [
                [0, 'desc']
            ],
        });
    });
    $(document).ready(function() {
        $("#table2").DataTable({
            order: [
                [0, 'desc']
            ],
        });
    });
    $(document).ready(function() {
        $("#table3").DataTable({
            order: [
                [0, 'desc']
            ],
        });
    });
    $(document).ready(function() {
        $("#table4").DataTable({
            order: [
                [0, 'desc']
            ],
        });
    });
</script>
@endsection