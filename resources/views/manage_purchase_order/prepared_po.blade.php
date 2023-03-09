@extends('adminltelayout.layout')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Prepared PO!</h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                    <li class="breadcrumb-item active">Purchase Order Approval</li>
                </ol>
            </div><!-- /.col -->
        </div><!-- /.row -->
    </div><!-- /.container-fluid -->
</div>

<div class="col-lg-12">

    <ul id="clothing-nav" class="nav nav-tabs" role="tablist">
        @if(Auth::user()->position =="Corporate Treasurer" or Auth::user()->position =="Chief Executive Officer" )
        <!-- <li class="nav-item">
            <a class="nav-link active" href="#verified_po" role="tab" id="verified_po-tab" role="tab" data-toggle="tab" aria-controls="verified_po" aria-expanded="true">Verified PO</a>
        </li> -->
        <li class="nav-item">
            <a class="nav-link active" href="#approving" role="tab" id="approving-tab" data-toggle="tab" aria-controls="verified_po">Need For Approval</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#approved_po" role="tab" id="approved_po-tab" data-toggle="tab" aria-controls="verified_po">Approved PO</a>
        </li>
        @else
        @if(Auth::user()->hasRole('Validator'))
        <li class="nav-item">
            <a class="nav-link active" href="#prepared_po" id="prepared_po-tab" role="tab" data-toggle="tab" aria-controls="prepared_po" aria-expanded="true">Prepared PO</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#verified_po" role="tab" id="verified_po-tab" data-toggle="tab" aria-controls="prepared_po">Verified & Approved PO</a>
        </li>
        <!-- <li class="nav-item">
            <a class="nav-link" href="#approving" role="tab" id="approving-tab" data-toggle="tab" aria-controls="prepared_po">Need For Approval</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#approved_po" role="tab" id="approved_po-tab" data-toggle="tab" aria-controls="prepared_po">Approved PO</a>
        </li> -->
        @else
        <!-- <li class="nav-item">
            <a class="nav-link active" href="#purchase_order" id="purchase_order-tab" role="tab" data-toggle="tab" aria-controls="purchase_order" aria-expanded="true">Purchase Order</a>
        </li> -->
        @if(Auth::user()->hasRole('Processor'))
        <li class="nav-item">
            <a class="nav-link active" href="#prepared_po" id="prepared_po-tab" role="tab" data-toggle="tab" aria-controls="prepared_po" aria-expanded="true">Prepared PO</a>
        </li>
        @else
        <li class="nav-item">
            <a class="nav-link active" href="#verified_po" role="tab" id="verified_po-tab" data-toggle="tab" aria-controls="prepared_po">PO for Approval</a>
        </li>
        @endif

        <!-- <li class="nav-item">
            <a class="nav-link" href="#approving" role="tab" id="approving-tab" data-toggle="tab" aria-controls="prepared_po">Need For Approval</a>
        </li> -->
        <li class="nav-item">
            <a class="nav-link" href="#approved_po" role="tab" id="approved_po-tab" data-toggle="tab" aria-controls="prepared_po">Approved PO</a>
        </li>
        @endif

        @endif
    </ul>
    <div id="clothing-nav-content" class="tab-content">
        @if($user->position=="Corporate Treasurer" or $user->position=="Chief Executive Officer")
        <!-- <div role="tabpanel" class="tab-pane fade show active" id="verified_po" aria-labelledby="verified_po-tab">
            <br>
            <div class="card">
                <div class="row">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="table2" class="table table-striped table-bordered">
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
                                        @foreach($verified_po as $vpo)
                                        <input type="hidden" id="pr_id" name="pr_id" value="{{$vpo->id}}" />
                                        <td class="pr_no_for_canvass">{{$vpo->po_no}}</td>
                                        <td class="pr_purpose">{{$vpo->status}}</td>
                                        <td class="pr_remark">{{$vpo->createdDate}}</td>
                                        <td>
                                            <a href="{{route('view_verified_po',$pr_no = $vpo->pr_no )}}" class="btn btn-danger btn-block btn-sm view_btn"><i class="fa fa-eye"></i>View</a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div> -->
        <div role="tabpanel" class="tab-pane fade show active" id="approving" aria-labelledby="approving-tab">
            <br>
            <div class="card">
                @if($approving->isEmpty())
                @else
                <div class="row">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="table3" class="table table-striped table-bordered">
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
                                        @foreach($approving as $app_po)
                                        <input type="hidden" id="pr_id" name="pr_id" value="{{$app_po->id}}" />
                                        <td class="pr_no_for_canvass">{{$app_po->po_no}}</td>
                                        <td class="pr_purpose">{{$app_po->status}}</td>
                                        <td class="pr_remark">{{$app_po->createdDate}}</td>
                                        <td>
                                            <a href="{{route('view_approving_po_vip',$pr_no = $app_po->pr_no )}}" class="btn btn-danger btn-block btn-sm view_btn"><i class="fa fa-eye"></i>View</a>
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
        <div role="tabpanel" class="tab-pane fade" id="approved_po" aria-labelledby="approved_po-tab">
            <br>
            <div class="card">
                @if($approved_po->isEmpty())
                @else
                <div class="row">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="table4" class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>Purchase Order</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        @foreach($approved_po as $app_po)
                                        <input type="hidden" id="pr_id" name="pr_id" value="{{$app_po->id}}" />
                                        <td class="pr_no_for_canvass">{{$app_po->po_no}}</td>
                                        <td class="pr_purpose">{{$app_po->status}}</td>
                                        <td class="pr_remark">{{$app_po->createdDate}}</td>
                                        <td>
                                            <a href="{{route('view_approved_po_app',$pr_no = $app_po->pr_no )}}" class="btn btn-danger btn-block btn-sm view_btn"><i class="fa fa-eye"></i>View</a>
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
        @if(Auth::user()->hasRole('Validator'))
        <div role="tabpanel" class="tab-pane fade show active" id="prepared_po" aria-labelledby="prepared_po-tab">
            <br>
            <div class="card">
                @if($po->isEmpty())
                @else
                <div class="row">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="table2" class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>Purchase Order</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        @foreach($po as $prepared)
                                        <input type="hidden" id="pr_id" name="pr_id" value="{{$prepared->id}}" />
                                        <td class="pr_no_for_canvass">{{$prepared->po_no}}</td>
                                        <td class="pr_purpose">{{$prepared->status}}</td>
                                        <td class="pr_remark">{{$prepared->preparedDate}}</td>
                                        <td>
                                            @if(Auth::user()->hasRole('Validator'))
                                            <a href="{{route('view_prepared_po_val',$pr_no = $prepared->pr_no )}}" class="btn btn-danger btn-block btn-sm view_btn"><i class="fa fa-eye"></i>View</a>
                                            @elseif(Auth::user()->hasRole('Processor'))
                                            <a href="{{route('view_prepared_po_pro',$pr_no = $prepared->pr_no )}}" class="btn btn-danger btn-block btn-sm view_btn"><i class="fa fa-eye"></i>View</a>
                                            @else
                                            <a href="{{route('view_prepared_po_app',$pr_no = $prepared->pr_no )}}" class="btn btn-danger btn-block btn-sm view_btn"><i class="fa fa-eye"></i>View</a>
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
        @elseif(Auth::user()->hasRole('Approver'))
        <div role="tabpanel" class="tab-pane fade show active" id="verified_po" aria-labelledby="verified_po-tab">
            <br>
            <div class="card">
                @if($verified_po->isEmpty())
                @else
                <div class="row">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="table3" class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>Purchase Order</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        @foreach($verified_po as $vpo)
                                        <input type="hidden" id="pr_id" name="pr_id" value="{{$vpo->id}}" />
                                        <td class="pr_no_for_canvass">{{$vpo->po_no}}</td>
                                        <td class="pr_purpose">{{$vpo->status}}</td>
                                        <td class="pr_remark">{{$vpo->verifiedDate}}</td>
                                        <td>
                                            @if(Auth::user()->hasRole('Validator'))
                                            <a href="{{route('view_verified_po_val',$pr_no = $vpo->pr_no )}}" class="btn btn-danger btn-block btn-sm view_btn"><i class="fa fa-eye"></i>View</a>
                                            @elseif(Auth::user()->hasRole('Processor'))
                                            <a href="{{route('view_verified_po_pro',$pr_no = $vpo->pr_no )}}" class="btn btn-danger btn-block btn-sm view_btn"><i class="fa fa-eye"></i>View</a>
                                            @else
                                            <a href="{{route('view_verified_po',$pr_no = $vpo->pr_no )}}" class="btn btn-danger btn-block btn-sm view_btn"><i class="fa fa-eye"></i>View</a>
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
        <!-- <div role="tabpanel" class="tab-pane fade show active" id="purchase_order" aria-labelledby="purchase_order-tab">
            <br>
            <div class="card">
                <div class="row">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="table1" class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>Purchase Order</th>
                                        <th>Purchase Request</th>
                                        <th>Status</th>
                                        <th>Created Date</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        @foreach($purchase_order as $poo)
                                        <input type="hidden" id="pr_id" name="pr_id" value="{{$poo->id}}" />
                                        <td class="pr_no_for_canvass">{{$poo->po_no}}</td>
                                        <td class="pr_no_for_canvass">{{$poo->pr_no}}</td>
                                        <td class="pr_purpose">{{$poo->status}}</td>
                                        <td class="pr_remark">{{$poo->createdDate}}</td>
                                        <td>
                                            @if(Auth::user()->hasRole('Processor'))
                                            <a href="{{route('view_approved_po_pro',$pr_no = $poo->pr_no )}}" class="btn btn-danger btn-block btn-sm view_btn"><i class="fa fa-eye"></i>View</a>
                                            @else
                                            <a href="{{route('view_approved_po_app',$pr_no = $poo->pr_no )}}" class="btn btn-danger btn-block btn-sm view_btn"><i class="fa fa-eye"></i>View</a>
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
        </div> -->
        @elseif(Auth::user()->hasRole('Processor'))
        <div role="tabpanel" class="tab-pane fade show active" id="prepared_po" aria-labelledby="prepared_po-tab">
            <br>
            <div class="card">
                @if($po->isEmpty())
                @else
                <div class="row">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="table2" class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>Purchase Order</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        @foreach($po as $prepared)
                                        <input type="hidden" id="pr_id" name="pr_id" value="{{$prepared->id}}" />
                                        <td class="pr_no_for_canvass">{{$prepared->po_no}}</td>
                                        <td class="pr_purpose">{{$prepared->status}}</td>
                                        <td class="pr_remark">{{$prepared->preparedDate}}</td>
                                        <td>
                                            @if(Auth::user()->hasRole('Validator'))
                                            <a href="{{route('view_prepared_po_val',$pr_no = $prepared->pr_no )}}" class="btn btn-danger btn-block btn-sm view_btn"><i class="fa fa-eye"></i>View</a>
                                            @elseif(Auth::user()->hasRole('Processor'))
                                            <a href="{{route('view_prepared_po_pro',$pr_no = $prepared->pr_no )}}" class="btn btn-danger btn-block btn-sm view_btn"><i class="fa fa-eye"></i>View</a>
                                            @else
                                            <a href="{{route('view_prepared_po_app',$pr_no = $prepared->pr_no )}}" class="btn btn-danger btn-block btn-sm view_btn"><i class="fa fa-eye"></i>View</a>
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
        @else

        @endif

        <!-- <div role="tabpanel" class="tab-pane fade" id="approving" aria-labelledby="approving-tab">
            <br>
            <div class="card">
                <div class="row">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="table4" class="table table-striped table-bordered">
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
                                        @foreach($approving as $app_po)
                                        <input type="hidden" id="pr_id" name="pr_id" value="{{$app_po->id}}" />
                                        <td class="pr_no_for_canvass">{{$app_po->po_no}}</td>
                                        <td class="pr_purpose">{{$app_po->status}}</td>
                                        <td class="pr_remark">{{$app_po->createdDate}}</td>
                                        <td>
                                            @if(Auth::user()->hasRole('Validator'))
                                            <a href="{{route('view_approving_po_val',$pr_no = $app_po->pr_no )}}" class="btn btn-danger btn-block btn-sm view_btn"><i class="fa fa-eye"></i>View</a>
                                            @elseif(Auth::user()->hasRole('Approver'))
                                            <a href="{{route('view_approving_po_app',$pr_no = $app_po->pr_no )}}" class="btn btn-danger btn-block btn-sm view_btn"><i class="fa fa-eye"></i>View</a>
                                            @else
                                            <a href="{{route('view_approving_po_pro',$pr_no = $app_po->pr_no )}}" class="btn btn-danger btn-block btn-sm view_btn"><i class="fa fa-eye"></i>View</a>
                                        </td>
                                        @endif
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div> -->

        <div role="tabpanel" class="tab-pane fade" id="verified_po" aria-labelledby="verified_po-tab">
            <br>
            <div class="card">
                @if($verified_po->isEmpty())
                @else
                <div class="row">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="table3" class="table table-striped table-bordered">
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
                                        @foreach($verified_po as $vpo)
                                        <input type="hidden" id="pr_id" name="pr_id" value="{{$vpo->id}}" />
                                        <td class="pr_no_for_canvass">{{$vpo->po_no}}</td>
                                        <td class="pr_purpose">{{$vpo->status}}</td>
                                        @if($vpo->status == 'Ordered')
                                        <td class="pr_remark">{{$vpo->approvedDate}}</td>
                                        @else
                                        <td class="pr_remark">{{$vpo->verifiedDate}}</td>
                                        @endif
                                        <td>
                                            @if(Auth::user()->hasRole('Validator'))
                                            <a href="{{route('view_verified_po_val',$pr_no = $vpo->pr_no )}}" class="btn btn-danger btn-block btn-sm view_btn"><i class="fa fa-eye"></i>View</a>
                                            @elseif(Auth::user()->hasRole('Processor'))
                                            <a href="{{route('view_verified_po_pro',$pr_no = $vpo->pr_no )}}" class="btn btn-danger btn-block btn-sm view_btn"><i class="fa fa-eye"></i>View</a>
                                            @else
                                            <a href="{{route('view_verified_po',$pr_no = $vpo->pr_no )}}" class="btn btn-danger btn-block btn-sm view_btn"><i class="fa fa-eye"></i>View</a>
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
        <div role="tabpanel" class="tab-pane fade" id="approved_po" aria-labelledby="approved_po-tab">
            <br>
            <div class="card">
                @if($approved_po->isEmpty())
                @else
                <div class="row">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="table5" class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>Purchase Order</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        @foreach($approved_po as $app_po2)
                                        <input type="hidden" id="pr_id" name="pr_id" value="{{$app_po2->id}}" />
                                        <td class="pr_no_for_canvass">{{$app_po2->po_no}}</td>
                                        <td class="pr_purpose">{{$app_po2->status}}</td>
                                        <td class="pr_remark">{{$app_po2->approvedDate}}</td>
                                        <td>
                                            @if(Auth::user()->hasRole('Processor'))
                                            <a href="{{route('view_approved_po_pro',$pr_no = $app_po2->pr_no )}}" class="btn btn-danger btn-block btn-sm view_btn"><i class="fa fa-eye"></i>View</a>
                                            @else
                                            <a href="{{route('view_approved_po_app',$pr_no = $app_po2->pr_no )}}" class="btn btn-danger btn-block btn-sm view_btn"><i class="fa fa-eye"></i>View</a>
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