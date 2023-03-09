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
            <a class="nav-link active" href="#approved_po" id="approved_po-tab" role="tab" data-toggle="tab" aria-controls="approved_po" aria-expanded="true">Need to Order</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#ordered_po" id="ordered_po-tab" role="tab" data-toggle="tab" aria-controls="new_pr" aria-expanded="true">Ordered PO</a>
        </li>
    </ul>
    <br>
    <div id="clothing-nav-content" class="tab-content">
        <div role="tabpanel" class="tab-pane fade show active" id="approved_po" aria-labelledby="approved_po-tab">
            <div class="card">
                @if($approved_po->isEmpty())
                @else
                <div class="row">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="app_tbl" class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>Purchase Order</th>
                                        <th>Status</th>
                                        <th>Created Date</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        @foreach($approved_po as $app_po2)
                                        <input type="hidden" id="pr_id" name="pr_id" value="{{$app_po2->id}}" />
                                        <td class="pr_no_for_canvass">{{$app_po2->po_no}}</td>
                                        <td class="pr_purpose">{{$app_po2->status}}</td>
                                        <td class="pr_remark">{{$app_po2->createdDate}}</td>
                                        <td>
                                            @if(Auth::user()->hasRole('Processor'))
                                            <a href="{{route('view_po_to_order',$pr_no = $app_po2->pr_no )}}" class="btn btn-danger btn-block btn-sm view_btn"><i class="fa fa-eye"></i>View</a>
                                            @else
                                            <a href="{{route('view_approved_po_app',$pr_no = $app_po2->pr_no )}}" class="btn btn-danger btn-block btn-sm view_btn"><i class="fa fa-eye"></i>View</a>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div role="tabpanel" class="tab-pane fade" id="ordered_po" aria-labelledby="ordered_po-tab">
            <div class="card">
                @if($ordered_po->isEmpty())
                @else
                <div class="row">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="table2" class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>Ordered PO</th>
                                        <th>Status</th>
                                        <th>Order Date</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        @foreach($ordered_po as $ordered)
                                        <input type="hidden" id="pr_id" name="pr_id" value="{{$ordered->id}}" />
                                        <td class="pr_no_for_canvass">{{$ordered->order_no}}</td>
                                        <td class="pr_purpose">{{$ordered->stat}}</td>
                                        <td class="pr_remark">{{$ordered->orderDate}}</td>
                                        <td>
                                            @if(Auth::user()->hasRole('Processor'))
                                            <a href="{{route('view_ordered_po',[$pr_no = $ordered->pr_no,$order_no = $ordered->order_no,$sid = $ordered->supplier_id] )}}" class="btn btn-danger btn-block btn-sm view_btn"><i class="fa fa-eye"></i>View</a>
                                            @else
                                            <a href="{{route('view_approved_po_app',$pr_no = $ordered->pr_no)}}" class="btn btn-danger btn-block btn-sm view_btn"><i class="fa fa-eye"></i>View</a>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        $("#app_tbl").DataTable({
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
</script>
@endsection