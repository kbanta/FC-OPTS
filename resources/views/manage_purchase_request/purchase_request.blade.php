@extends('adminltelayout.layout')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Make Request</h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                    <li class="breadcrumb-item active">Purchase Request</li>
                </ol>
            </div><!-- /.col -->
        </div><!-- /.row -->
    </div><!-- /.container-fluid -->
</div>

<div class="col-lg-12">
    <ul id="clothing-nav" class="nav nav-tabs" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" href="#pr" id="pr-tab" role="tab" data-toggle="tab" aria-controls="pr" aria-expanded="true">My PR</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#denied_pr" id="denied_pr-tab" role="tab" data-toggle="tab" aria-controls="new_pr" aria-expanded="true">Denied PR</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#hold" id="hold-tab" role="tab" data-toggle="tab" aria-controls="new_pr" aria-expanded="true">On Hold PR</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#approved_pr" id="approved_pr-tab" role="tab" data-toggle="tab" aria-controls="new_pr" aria-expanded="true">Approved PR</a>
        </li>
    </ul>
    <div id="clothing-nav-content" class="tab-content">
        <div role="tabpanel" class="tab-pane fade show active" id="pr" aria-labelledby="pr-tab">
            <br>
            <div class="card">
                <div class="card-header">
                    <button class="btn btn-success" data-toggle="modal" data-target="#addPurchaseRequest">
                        <span class="spinner-grow spinner-grow-sm"></span>
                        <b>Make Request</b>
                    </button>
                </div>
                @if($purchaserequest->isEmpty())
                @else
                <div class="row">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="tbl_pr" class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>Purchase Number</th>
                                        <th>Type</th>
                                        <!-- <th>Purpose</th> -->
                                        <!-- <th>Remarks</th> -->
                                        <th>Status</th>
                                        <th style="width:13%;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        @foreach($purchaserequest as $purchase_requests)
                                        <input type="hidden" id="pr_id" name="pr_id" />
                                        <td class="pr_pr_no">{{$purchase_requests->pr_no}}</td>
                                        <td class="pr_type">{{$purchase_requests->type}}</td>
                                        <!-- <td class="pr_purpose">{{$purchase_requests->purpose}}</td> -->
                                        <!-- <td class="pr_purpose">{{$purchase_requests->remarks}}</td> -->
                                        <td class="pr_remark">{{$purchase_requests->action}}</td>
                                        <td>
                                            @if(Auth::user()->hasRole('Administrator'))
                                            <!-- <div class="btn-group"> -->
                                            <a href="{{route('vpr',$pr_no = $purchase_requests->pr_no )}}" class="btn btn-danger btn-block btn-sm view_btn"><i class="fa fa-eye"></i>View</a>
                                            <!-- <a href="{{route('ad_pr_track',$pr_no = $purchase_requests->pr_no )}}" class="btn btn-success btn-sm view_btn"><i class="fa fa-eye"></i>Track</a> -->
                                            <!-- </div> -->
                                            @endif
                                            @if(Auth::user()->hasRole('Approver'))
                                            <!-- <div class="btn-group"> -->
                                            <a href="{{route('app_vpr',$pr_no = $purchase_requests->pr_no )}}" class="btn btn-danger btn-block btn-sm view_btn"><i class="fa fa-eye"></i>View</a>
                                            <!-- <a href="{{route('app_pr_track',$pr_no = $purchase_requests->pr_no )}}" class="btn btn-success btn-sm view_btn"><i class="fa fa-eye"></i>Track</a>
                                            </div> -->
                                            @endif
                                            @if(Auth::user()->hasRole('Processor'))
                                            <!-- <div class="btn-group"> -->
                                            <a href="{{route('pro_vpr',$pr_no = $purchase_requests->pr_no )}}" class="btn btn-danger btn-block btn-sm view_btn"><i class="fa fa-eye"></i>View</a>
                                            <!-- <a href="{{route('pro_pr_track',$pr_no = $purchase_requests->pr_no )}}" class="btn btn-success btn-sm view_btn"><i class="fa fa-eye"></i>Track</a> -->
                                            <!-- <a href="#" class="btn btn-danger btn-block btn-sm track_btn"><i class="fa fa-eye"></i>Track</a> -->
                                            <!-- </div> -->
                                            @endif
                                            @if(Auth::user()->hasRole('Validator'))
                                            <!-- <div class="btn-group"> -->
                                            <a href="{{route('val_vpr',$pr_no = $purchase_requests->pr_no )}}" class="btn btn-danger btn-block btn-sm view_btn"><i class="fa fa-eye"></i>View</a>
                                            <!-- <a href="{{route('val_pr_track',$pr_no = $purchase_requests->pr_no )}}" class="btn btn-success btn-sm view_btn"><i class="fa fa-eye"></i>Track</a> -->
                                            <!-- </div> -->
                                            @endif
                                            @if(Auth::user()->hasRole('Requestor'))
                                            <!-- <div class="btn-group"> -->
                                            <a href="{{route('req_vpr',$pr_no = $purchase_requests->pr_no )}}" class="btn btn-danger btn-block btn-sm view_btn"><i class="fa fa-eye"></i>View</a>
                                            <!-- <a href="{{route('req_pr_track',$pr_no = $purchase_requests->pr_no )}}" class="btn btn-success btn-sm view_btn"><i class="fa fa-eye"></i>Track</a> -->
                                            <!-- </div> -->
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
        <div role="tabpanel" class="tab-pane fade" id="denied_pr" aria-labelledby="denied_pr-tab">
            <br>
            <div class="card">
                @if($deny_pr->isEmpty())
                @else
                <div class="row">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="table2" class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>Purchase Number</th>
                                        <th>Type</th>
                                        <!-- <th>Purpose</th> -->
                                        <!-- <th>Remarks</th> -->
                                        <th>Status</th>
                                        <th style="width:13%;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        @foreach($deny_pr as $denied)
                                        <input type="hidden" id="pr_id" name="pr_id" />
                                        <td class="pr_pr_no">{{$denied->pr_no}}</td>
                                        <td class="pr_type">{{$denied->type}}</td>
                                        <!-- <td class="pr_purpose">{{$denied->purpose}}</td> -->
                                        <!-- <td class="pr_purpose">{{$denied->remarks}}</td> -->
                                        <td class="pr_remark">{{$denied->action}}</td>
                                        <td>
                                            @if(Auth::user()->hasRole('Administrator'))
                                            <a href="{{route('add_denied',$pr_no = $denied->pr_no )}}" class="btn btn-danger btn-block btn-sm view_btn"><i class="fa fa-eye"></i>View</a>
                                            @endif
                                            @if(Auth::user()->hasRole('Approver'))
                                            <a href="{{route('app_denied',$pr_no = $denied->pr_no )}}" class="btn btn-danger btn-block btn-sm view_btn"><i class="fa fa-eye"></i>View</a>
                                            @endif
                                            @if(Auth::user()->hasRole('Processor'))
                                            <a href="{{route('pro_denied',$pr_no = $denied->pr_no )}}" class="btn btn-danger btn-block btn-sm view_btn"><i class="fa fa-eye"></i>View</a>
                                            @endif
                                            @if(Auth::user()->hasRole('Validator'))
                                            <a href="{{route('val_denied',$pr_no = $denied->pr_no )}}" class="btn btn-danger btn-block btn-sm view_btn"><i class="fa fa-eye"></i>View</a>
                                            @endif
                                            @if(Auth::user()->hasRole('Requestor'))
                                            <a href="{{route('req_denied',$pr_no = $denied->pr_no )}}" class="btn btn-danger btn-block btn-sm view_btn"><i class="fa fa-eye"></i>View</a>
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
        <div role="tabpanel" class="tab-pane fade" id="hold" aria-labelledby="hold-tab">
            <br>
            <div class="card">
                @if($hold->isEmpty())
                @else
                <div class="row">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="table3" class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>Purchase Number</th>
                                        <th>Type</th>
                                        <!-- <th>Purpose</th> -->
                                        <!-- <th>Remarks</th> -->
                                        <th>Status</th>
                                        <th style="width:13%;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        @foreach($hold as $holds)
                                        <input type="hidden" id="pr_id" name="pr_id" />
                                        <td class="pr_pr_no">{{$holds->pr_no}}</td>
                                        <td class="pr_type">{{$holds->type}}</td>
                                        <!-- <td class="pr_purpose">{{$holds->purpose}}</td> -->
                                        <!-- <td class="pr_purpose">{{$holds->remarks}}</td> -->
                                        <td class="pr_remark">{{$holds->action}}</td>
                                        <td>
                                            @if(Auth::user()->hasRole('Administrator'))
                                            <a href="{{route('add_denied',$pr_no = $holds->pr_no )}}" class="btn btn-danger btn-block btn-sm view_btn"><i class="fa fa-eye"></i>View</a>
                                            @endif
                                            @if(Auth::user()->hasRole('Approver'))
                                            <a href="{{route('app_denied',$pr_no = $holds->pr_no )}}" class="btn btn-danger btn-block btn-sm view_btn"><i class="fa fa-eye"></i>View</a>
                                            @endif
                                            @if(Auth::user()->hasRole('Processor'))
                                            <a href="{{route('pro_denied',$pr_no = $holds->pr_no )}}" class="btn btn-danger btn-block btn-sm view_btn"><i class="fa fa-eye"></i>View</a>
                                            @endif
                                            @if(Auth::user()->hasRole('Validator'))
                                            <a href="{{route('val_denied',$pr_no = $holds->pr_no )}}" class="btn btn-danger btn-block btn-sm view_btn"><i class="fa fa-eye"></i>View</a>
                                            @endif
                                            @if(Auth::user()->hasRole('Requestor'))
                                            <a href="{{route('req_hold',$pr_no = $holds->pr_no )}}" class="btn btn-danger btn-block btn-sm view_btn"><i class="fa fa-eye"></i>View</a>
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
        <div role="tabpanel" class="tab-pane fade" id="approved_pr" aria-labelledby="approved_pr-tab">
            <br>
            <div class="card">
                @if($approved_pr->isEmpty())
                @else
                <div class="row">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="table3" class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>Purchase Number</th>
                                        <th>Type</th>
                                        <!-- <th>Purpose</th> -->
                                        <!-- <th>Remarks</th> -->
                                        <th>Status</th>
                                        <th style="width:13%;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        @foreach($approved_pr as $pr)
                                        <input type="hidden" id="pr_id" name="pr_id" />
                                        <td class="pr_pr_no">{{$pr->pr_no}}</td>
                                        <td class="pr_type">{{$pr->type}}</td>
                                        <!-- <td class="pr_purpose">{{$pr->purpose}}</td> -->
                                        <!-- <td class="pr_purpose">{{$pr->remarks}}</td> -->
                                        <td class="pr_remark">{{$pr->action}}</td>
                                        <td>
                                            @if(Auth::user()->hasRole('Administrator'))
                                            <!-- <div class="btn-group"> -->
                                            <a href="{{route('vpr',$pr_no = $pr->pr_no )}}" class="btn btn-danger btn-block btn-sm view_btn"><i class="fa fa-eye"></i>View</a>
                                            <!-- <a href="{{route('ad_pr_track',$pr_no = $pr->pr_no )}}" class="btn btn-success btn-sm view_btn"><i class="fa fa-eye"></i>Track</a> -->
                                            <!-- </div> -->
                                            @endif
                                            @if(Auth::user()->hasRole('Approver'))
                                            <!-- <div class="btn-group"> -->
                                            <a href="{{route('app_vpr',$pr_no = $pr->pr_no )}}" class="btn btn-danger btn-block btn-sm view_btn"><i class="fa fa-eye"></i>View</a>
                                            <!-- <a href="{{route('app_pr_track',$pr_no = $pr->pr_no )}}" class="btn btn-success btn-sm view_btn"><i class="fa fa-eye"></i>Track</a>
                                            </div> -->
                                            @endif
                                            @if(Auth::user()->hasRole('Processor'))
                                            <!-- <div class="btn-group"> -->
                                            <a href="{{route('pro_vpr',$pr_no = $pr->pr_no )}}" class="btn btn-danger btn-block btn-sm view_btn"><i class="fa fa-eye"></i>View</a>
                                            <!-- <a href="{{route('pro_pr_track',$pr_no = $pr->pr_no )}}" class="btn btn-success btn-sm view_btn"><i class="fa fa-eye"></i>Track</a> -->
                                            <!-- <a href="#" class="btn btn-danger btn-block btn-sm track_btn"><i class="fa fa-eye"></i>Track</a> -->
                                            <!-- </div> -->
                                            @endif
                                            @if(Auth::user()->hasRole('Validator'))
                                            <!-- <div class="btn-group"> -->
                                            <a href="{{route('val_vpr',$pr_no = $pr->pr_no )}}" class="btn btn-danger btn-block btn-sm view_btn"><i class="fa fa-eye"></i>View</a>
                                            <!-- <a href="{{route('val_pr_track',$pr_no = $pr->pr_no )}}" class="btn btn-success btn-sm view_btn"><i class="fa fa-eye"></i>Track</a> -->
                                            <!-- </div> -->
                                            @endif
                                            @if(Auth::user()->hasRole('Requestor'))
                                            <!-- <div class="btn-group"> -->
                                            <a href="{{route('req_vpr',$pr_no = $pr->pr_no )}}" class="btn btn-danger btn-block btn-sm view_btn"><i class="fa fa-eye"></i>View</a>
                                            <!-- <a href="{{route('req_pr_track',$pr_no = $pr->pr_no )}}" class="btn btn-success btn-sm view_btn"><i class="fa fa-eye"></i>Track</a> -->
                                            <!-- </div> -->
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
    </div>
</div>

@include('manage_purchase_request.add_purchase_request')
<!-- @include('manage_purchase_request.view_purchase_request') -->


<script>
    $(document).ready(function() {
        $("#tbl_pr").DataTable({
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
</script>
<script>

</script>
@endsection