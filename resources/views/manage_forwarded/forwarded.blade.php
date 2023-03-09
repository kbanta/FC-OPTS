@extends('adminltelayout.layout')
@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Forwarded Deliveries</h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                    <li class="breadcrumb-item active">Forwarded Deliveries</li>
                </ol>
            </div><!-- /.col -->
        </div><!-- /.row -->
    </div><!-- /.container-fluid -->
</div>

<div class="col-12">
    <ul id="clothing-nav" class="nav nav-tabs" role="tablist">
        @if(Auth::user()->hasRole('Approver'))
        <li class="nav-item">
            <a class="nav-link active" href="#delivery" role="tab" id="delivery-tab" data-toggle="tab" aria-controls="delivery">For Approval</a>
        </li>
        <li class="nav-item">
            <a class="nav-link " href="#fw_approved" role="tab" id="fw_approved-tab" data-toggle="tab" aria-controls="fw_approved">Approved</a>
        </li>
        @else
        <li class="nav-item">
            <a class="nav-link active" href="#to_received" role="tab" id="to_received-tab" data-toggle="tab" aria-controls="to_received">To Receive</a>
        </li>
        <li class="nav-item">
            <a class="nav-link " href="#received" role="tab" id="received-tab" data-toggle="tab" aria-controls="received">Received</a>
        </li>
        <li class="nav-item">
            <a class="nav-link " href="#reported" role="tab" id="reported-tab" data-toggle="tab" aria-controls="reported">Reported</a>
        </li>
        @endif
    </ul>
    <br>
    <div id="clothing-nav-content" class="tab-content">
        @if(Auth::user()->hasRole('Approver'))
        <div role="tabpanel" class="tab-pane fade show active" id="delivery" aria-labelledby="delivery-tab">
            <div class="card">
                @if($forwarded->isEmpty())
                @else
                <div class="row">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="table1" class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>Forward no.</th>
                                        <!-- <th>Delivery no.</th> -->
                                        <th>Order no.</th>
                                        <th>Date</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        @foreach($forwarded as $forward)
                                        <input type="hidden" id="pr_id" name="pr_id" value="{{$forward->id}}" />
                                        <td class="pr_no_for_canvass">{{$forward->forward_no}}</td>
                                        <!-- <td class="pr_purpose"></td> -->
                                        <td class="pr_purpose">{{$forward->order_no}}</td>
                                        <td class="pr_purpose">{{$forward->forwardedDate}}</td>
                                        <td>
                                            @if(Auth::user()->hasRole('Approver'))
                                            <a href="{{route('view_forwarded_app',[$dln = $forward->delivery_no,$po_no = $forward->po_no,$staff_id = $forward->staff_id] )}}" class="btn btn-danger btn-block btn-sm view_btn"><i class="fa fa-eye"></i>View</a>
                                            @endif
                                            @if(Auth::user()->hasRole('Validator'))
                                            <a href="{{route('view_forwarded_val',[$fwn = $forward->forward_no,$dln = $forward->delivery_no,$po_no = $forward->po_no,$staff_id = $forward->staff_id] )}}" class="btn btn-danger btn-block btn-sm view_btn"><i class="fa fa-eye"></i>View</a>
                                            @endif
                                            @if(Auth::user()->hasRole('Processor'))
                                            <a href="{{route('view_forwarded_pro',[$dln = $forward->delivery_no,$po_no = $forward->po_no,$staff_id = $forward->staff_id] )}}" class="btn btn-danger btn-block btn-sm view_btn"><i class="fa fa-eye"></i>View</a>
                                            @endif
                                            @if(Auth::user()->hasRole('Administrator'))
                                            <a href="{{route('view_forwarded_add',[$fwn = $forward->forward_no,$dln = $forward->delivery_no,$po_no = $forward->po_no,$staff_id = $forward->user_id] )}}" class="btn btn-danger btn-block btn-sm view_btn"><i class="fa fa-eye"></i>View</a>
                                            @endif
                                            @if(Auth::user()->hasRole('Requestor'))
                                            <a href="{{route('view_forwarded_req',[$dln = $forward->delivery_no,$po_no = $forward->po_no] )}}" class="btn btn-danger btn-block btn-sm view_btn"><i class="fa fa-eye"></i>View</a>
                                            @endif
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
        <div role="tabpanel" class="tab-pane fade" id="fw_approved" aria-labelledby="fw_approved-tab">
            <div class="card">
                @if($fw_approved->isEmpty())
                @else
                <div class="row">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="table2" class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>Forward no.</th>
                                        <th>Delivery no.</th>
                                        <th>Order no.</th>
                                        <th>Date</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        @foreach($fw_approved as $approved)
                                        <input type="hidden" id="pr_id" name="pr_id" value="{{$approved->id}}" />
                                        <td class="pr_no_for_canvass">{{$approved->forward_no}}</td>
                                        <td class="pr_purpose">{{$approved->delivery_no}}</td>
                                        <td class="pr_purpose">{{$approved->order_no}}</td>
                                        <td class="pr_purpose">{{$approved->approvedDate}}</td>
                                        <td>
                                            <a href="{{route('view_forwardeddd',[$dln = $approved->delivery_no,$po_no = $approved->po_no,] )}}" class="btn btn-danger btn-block btn-sm view_btn"><i class="fa fa-eye"></i>View</a>
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
        @else
        <div role="tabpanel" class="tab-pane fade show active" id="to_received" aria-labelledby="to_received-tab">
            <div class="card">
                @if($to_received->isEmpty())
                @else
                <div class="row">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="table3" class="table table-striped table-bordered">
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
                                        @foreach($to_received as $forward)
                                        <input type="hidden" id="pr_id" name="pr_id" value="{{$forward->id}}" />
                                        <td class="pr_purpose">{{$forward->delivery_no}}</td>
                                        <td class="pr_no_for_canvass">{{$forward->order_no}}</td>
                                        <td class="pr_no_for_canvass">{{$forward->created_at}}</td>
                                        <td width="15%">
                                            @if(Auth::user()->hasRole('Approver'))
                                            <a href="{{route('view_forwarded',[$fwn = $forward->forward_no,$dln = $forward->delivery_no,$po_no = $forward->po_no,$staff_id = $forward->staff_id] )}}" class="btn btn-danger btn-block btn-sm view_btn"><i class="fa fa-eye"></i>View</a>
                                            @endif
                                            @if(Auth::user()->hasRole('Validator'))
                                            <a href="{{route('view_forwarded_val',[$dln = $forward->delivery_no,$po_no = $forward->po_no,$staff_id = $forward->user_id] )}}" class="btn btn-danger btn-block  btn-sm view_btn"><i class="fa fa-eye"></i>View</a>
                                            @endif
                                            @if(Auth::user()->hasRole('Processor'))
                                            <a href="{{route('view_forwarded_pro',[$dln = $forward->delivery_no,$po_no = $forward->po_no,$staff_id = $forward->user_id] )}}" class="btn btn-danger btn-block btn-sm view_btn"><i class="fa fa-eye"></i>View</a>
                                            @endif
                                            @if(Auth::user()->hasRole('Administrator'))
                                            <a href="{{route('view_forwarded_add',[$dln = $forward->delivery_no,$po_no = $forward->po_no,$staff_id = $forward->user_id] )}}" class="btn btn-danger btn-block  btn-sm view_btn"><i class="fa fa-eye"></i>View</a>
                                            @endif
                                            @if(Auth::user()->hasRole('Requestor'))
                                            <a href="{{route('view_forwarded_req',[$dln = $forward->delivery_no,$po_no = $forward->po_no,$staff_id = $forward->user_id] )}}" class="btn btn-danger btn-block  btn-sm view_btn"><i class="fa fa-eye"></i>View</a>
                                            @endif
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
        <div role="tabpanel" class="tab-pane fade" id="received" aria-labelledby="received-tab">
            <div class="card">
                @if($item_received->isEmpty())
                @else
                <div class="row">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="table4" class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>Forward no.</th>
                                        <th>Delivery no.</th>
                                        <th>Order no.</th>
                                        <th>Date</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        @foreach($item_received as $ir)
                                        <input type="hidden" id="pr_id" name="pr_id" value="{{$ir->id}}" />
                                        <td class="pr_no_for_canvass">{{$ir->forward_no}}</td>
                                        <td class="pr_purpose">{{$ir->delivery_no}}</td>
                                        <td class="pr_purpose">{{$ir->order_no}}</td>
                                        <td class="pr_purpose">{{$ir->reqreceivedDate}}</td>
                                        <td width="15%">
                                            @if(Auth::user()->hasRole('Validator'))
                                            <a href="{{route('view_forwarded_val',[$dln = $ir->delivery_no,$po_no = $ir->po_no,$staff_id = $ir->staff_id])}}" class="btn btn-danger btn-block  btn-sm view_btn"><i class="fa fa-eye"></i>View</a>
                                            @endif
                                            @if(Auth::user()->hasRole('Requestor'))
                                            <a href="{{route('view_forwarded_req',[$dln = $ir->delivery_no,$po_no = $ir->po_no,$staff_id = $ir->staff_id] )}}" class="btn btn-danger btn-block  btn-sm view_btn"><i class="fa fa-eye"></i>View</a>
                                            @endif
                                            @if(Auth::user()->hasRole('Processor'))
                                            <!-- <div class="btn btn-group"> -->
                                            <a href="{{route('view_forwarded_pro',[$dln = $ir->delivery_no,$po_no = $ir->po_no,$staff_id = $ir->staff_id] )}}" class="btn btn-danger btn-block btn-sm view_btn"><i class="fa fa-eye"></i>View</a>
                                            <!-- <a href="{{route('track_forwarded_pro',[$dln = $ir->delivery_no,$po_no = $ir->po_no,$staff_id = $ir->staff_id] )}}" class="btn btn-success  btn-sm view_btn"><i class="fa fa-eye"></i>Track</a>
                                            </div> -->
                                            @endif
                                            @if(Auth::user()->hasRole('Administrator'))
                                            <!-- <div class="btn btn-group"> -->
                                            <a href="{{route('view_forwarded_add',[$dln = $ir->delivery_no,$po_no = $ir->po_no,$staff_id = $ir->staff_id] )}}" class="btn btn-danger btn-block btn-sm view_btn"><i class="fa fa-eye"></i>View</a>
                                            <!-- <a href="{{route('track_forwarded_add',[$fwn = $ir->forward_no,$dln = $ir->delivery_no,$po_no = $ir->po_no,$staff_id = $ir->staff_id] )}}" class="btn btn-success  btn-sm view_btn"><i class="fa fa-eye"></i>Track</a>
                                            </div> -->
                                            @endif
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
        <div role="tabpanel" class="tab-pane fade" id="reported" aria-labelledby="reported-tab">
            <div class="card">
                @if($reported->isEmpty())
                @else
                <div class="row">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="table5" class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>Report no.</th>
                                        <th>Delivery no.</th>
                                        <th>Order no.</th>
                                        <th>Date</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        @foreach($reported as $rep)
                                        <input type="hidden" id="pr_id" name="pr_id" value="{{$rep->id}}" />
                                        <td class="pr_no_for_canvass">{{$rep->Report_no}}</td>
                                        <td class="pr_no_for_canvass">{{$rep->delivery_no}}</td>
                                        <td class="pr_no_for_canvass">{{$rep->order_no}}</td>
                                        <td class="pr_purpose">{{$rep->created_at}}</td>
                                        <td width="15%">
                                            @if(Auth::user()->hasRole('Validator'))
                                            <a href="{{route('view_reported_item_val',[$rn = $rep->Report_no] )}}" class="btn btn-danger btn-block btn-sm view_btn"><i class="fa fa-eye"></i>View</a>
                                            @endif
                                            @if(Auth::user()->hasRole('Requestor'))
                                            <a href="{{route('view_reported_item_req',[$rn = $rep->Report_no] )}}" class="btn btn-danger btn-block btn-sm view_btn"><i class="fa fa-eye"></i>View</a>
                                            @endif
                                            @if(Auth::user()->hasRole('Processor'))
                                            <a href="{{route('view_reported_item_pro',[$rn = $rep->Report_no] )}}" class="btn btn-danger btn-block btn-sm view_btn"><i class="fa fa-eye"></i>View</a>
                                            @endif
                                            @if(Auth::user()->hasRole('Administrator'))
                                            <a href="{{route('view_reported_item_add',[$rn = $rep->Report_no] )}}" class="btn btn-danger btn-block btn-sm view_btn"><i class="fa fa-eye"></i>View</a>
                                            @endif
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
        @endif

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
    $(document).ready(function() {
        $("#table5").DataTable({
            order: [
                [0, 'desc']
            ],
        });
    });
</script>
@endsection