@extends('adminltelayout.layout')
@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Forwarded Diliveries</h1>
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
        <li class="nav-item">
            <a class="nav-link active" href="#to_received" role="tab" id="to_received-tab" data-toggle="tab" aria-controls="to_received">To Received</a>
        </li>
        <li class="nav-item">
            <a class="nav-link " href="#received" role="tab" id="received-tab" data-toggle="tab" aria-controls="received">Received</a>
        </li>
        <li class="nav-item">
            <a class="nav-link " href="#reported" role="tab" id="reported-tab" data-toggle="tab" aria-controls="reported">Reported</a>
        </li>
    </ul>
    <br>
    <div id="clothing-nav-content" class="tab-content">
        <div role="tabpanel" class="tab-pane fade show active" id="to_received" aria-labelledby="to_received-tab">
            <div class="card">
                <div class="row">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="table3" class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>Delivery no.</th>
                                        <th>Purchase Order No.</th>
                                        <th width="15%">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        @foreach($forwarded as $forward)
                                        <input type="hidden" id="pr_id" name="pr_id" value="{{$forward->id}}" />
                                        <td class="pr_purpose">{{$forward->delivery_no}}</td>
                                        <td class="pr_no_for_canvass">{{$forward->po_no}}</td>
                                        <td>
                                            @if(Auth::user()->hasRole('Approver'))
                                            <div class="btn btn-group">
                                                <a href="{{route('view_forwarded2',[$dln = $forward->delivery_no,$po_no = $forward->po_no,$user_id = $forward->user_id] )}}" class="btn btn-danger btn-sm view_btn"><i class="fa fa-eye"></i>View</a>
                                                <a href="{{route('track_forwarded_app2',[$dln = $forward->delivery_no,$po_no = $forward->po_no,$user_id = $forward->user_id] )}}" class="btn btn-success  btn-sm view_btn"><i class="fa fa-eye"></i>Track</a>
                                            </div>
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
                            <table id="table4" class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>Forward no.</th>
                                        <th>Delivery no.</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        @foreach($item_received as $ir)
                                        <input type="hidden" id="pr_id" name="pr_id" value="{{$ir->id}}" />
                                        <td class="pr_no_for_canvass">{{$ir->forward_no}}</td>
                                        <td class="pr_purpose">{{$ir->delivery_no}}</td>
                                        <td width="15%">
                                            @if(Auth::user()->hasRole('Approver'))
                                            <div class="btn btn-group">
                                                <a href="{{route('view_forwarded_app2',[$dln = $ir->delivery_no,$po_no = $ir->po_no,$user_id = $ir->user_id] )}}" class="btn btn-danger btn-sm view_btn"><i class="fa fa-eye"></i>View</a>
                                                <a href="{{route('track_forwarded_app2',[$dln = $ir->delivery_no,$po_no = $ir->po_no,$user_id = $ir->user_id] )}}" class="btn btn-success  btn-sm view_btn"><i class="fa fa-eye"></i>Track</a>
                                            </div>
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
        <div role="tabpanel" class="tab-pane fade" id="reported" aria-labelledby="reported-tab">
            <div class="card">
                <div class="row">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="table5" class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>Report no.</th>
                                        <th>Date</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        @foreach($reported as $rep)
                                        <input type="hidden" id="pr_id" name="pr_id" value="{{$rep->id}}" />
                                        <td class="pr_no_for_canvass">{{$rep->Report_no}}</td>
                                        <td class="pr_purpose">{{$rep->created_at}}</td>
                                        <td width="15%">
                                            <a href="{{route('view_reported_item_app',[$rn = $rep->Report_no] )}}" class="btn btn-danger btn-block btn-sm view_btn"><i class="fa fa-eye"></i>View</a>
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