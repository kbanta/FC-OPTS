@extends('adminltelayout.layout')
@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Purchase Order</h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                    <li class="breadcrumb-item active">Purchase Order</li>
                </ol>
            </div><!-- /.col -->
        </div><!-- /.row -->
    </div><!-- /.container-fluid -->
</div>
<div class="col-12">
    <div class="card">
        @if($purchaseorder->isEmpty())
        @else
        <div class="row">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="tbl_pr" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>Purchase Order</th>
                                <th>Purchase Request</th>
                                <th>Status</th>
                                <th>Created Date</th>
                                <th style="width:13%;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                @foreach($purchaseorder as $p_o)
                                <input type="hidden" id="pr_id" name="pr_id" value="{{$p_o->id}}" />
                                <td class="pr_no_for_canvass">{{$p_o->po_no}}</td>
                                <td class="pr_no_for_canvass">{{$p_o->pr_no}}</td>
                                <td class="pr_purpose">{{$p_o->status}}</td>
                                <td class="pr_remark">{{$p_o->createdDate}}</td>
                                <td>
                                    @if(Auth::user()->hasRole('Administrator'))
                                    <!-- <div class="btn-group"> -->
                                    <a href="{{route('view_approved_po_ad',$pr_no = $p_o->pr_no )}}" class="btn btn-danger btn-block btn-sm view_btn"><i class="fa fa-eye"></i>View</a>
                                    <!-- <a href="{{route('ad_po_track',$pr_no = $p_o->pr_no )}}" class="btn btn-success btn-sm view_btn"><i class="fa fa-eye"></i>Track</a>
                                    </div> -->
                                    @endif
                                    @if(Auth::user()->hasRole('Requestor'))
                                    <!-- <div class="btn-group"> -->
                                    <a href="{{route('view_approved_po_req',$pr_no = $p_o->pr_no )}}" class="btn btn-danger btn-block btn-sm view_btn"><i class="fa fa-eye"></i>View</a>
                                    <!-- <a href="{{route('req_po_track',$pr_no = $p_o->pr_no )}}" class="btn btn-success btn-sm view_btn"><i class="fa fa-eye"></i>Track</a>
                                    </div> -->
                                    @endif
                                    @if(Auth::user()->hasRole('Processor'))
                                    <!-- <div class="btn-group"> -->
                                    <a href="{{route('view_approved_po_pro',$pr_no = $p_o->pr_no )}}" class="btn btn-danger btn-block btn-sm view_btn"><i class="fa fa-eye"></i>View</a>
                                    <!-- <a href="{{route('pro_po_track',$pr_no = $p_o->pr_no )}}" class="btn btn-success btn-sm view_btn"><i class="fa fa-eye"></i>Track</a>
                                    </div> -->
                                    @endif
                                    @if(Auth::user()->hasRole('Validator'))
                                    <!-- <div class="btn-group"> -->
                                    <a href="{{route('view_approved_po_val',$pr_no = $p_o->pr_no )}}" class="btn btn-danger btn-block btn-sm view_btn"><i class="fa fa-eye"></i>View</a>
                                    <!-- <a href="{{route('val_po_track',$pr_no = $p_o->pr_no )}}" class="btn btn-success btn-sm view_btn"><i class="fa fa-eye"></i>Track</a> -->
                                    <!-- </div> -->
                                    @endif
                                    @if(Auth::user()->hasRole('Approver'))
                                    <!-- <div class="btn-group"> -->
                                    <a href="{{route('view_approved_po_app',$pr_no = $p_o->pr_no )}}" class="btn btn-danger btn-block btn-block btn-sm view_btn"><i class="fa fa-eye"></i>View</a>
                                    <!-- <a href="{{route('app_po_track',$pr_no = $p_o->pr_no )}}" class="btn btn-success btn-sm view_btn"><i class="fa fa-eye"></i>Track</a> -->
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
<script>
    $(document).ready(function() {
        $("#tbl_pr").DataTable({
            order: [
                [0, 'desc']
            ],
        });

    });
</script>
@endsection