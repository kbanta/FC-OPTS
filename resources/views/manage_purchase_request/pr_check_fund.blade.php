@extends('adminltelayout.layout')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Check PR for Fund!</h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                    <li class="breadcrumb-item active">Check Purchase Request</li>
                </ol>
            </div><!-- /.col -->
        </div><!-- /.row -->
    </div><!-- /.container-fluid -->
</div>

<div class="col-lg-12">
    <div class="card">
        <div class="row">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="tbl_pr" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>Purchase Number</th>
                                <th>Type</th>
                                <th>Purpose</th>
                                <th>Remarks</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                @foreach($pr as $pr_check_fund)
                                <input type="hidden" id="pr_id" name="pr_id" />
                                <td class="pr_no_for_canvass">{{$pr_check_fund->pr_no}}</td>
                                <td class="pr_type">{{$pr_check_fund->type}}</td>
                                <td class="pr_purpose">{{$pr_check_fund->purpose}}</td>
                                <td class="pr_remark">{{$pr_check_fund->remarks}}</td>
                                <td class="pr_remark">{{$pr_check_fund->action}}</td>
                                <td>
                                <a href="{{route('view_pr_check_fund',$pr_no = $pr_check_fund->pr_no )}}" class="btn btn-danger btn-block btn-sm view_btn"><i class="fa fa-eye"></i>View</a>
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
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Checked PR!</h1>
            </div><!-- /.col -->
            </div><!-- /.row -->
    </div><!-- /.container-fluid -->
</div>
<div class="col-lg-12">
    <div class="card">
        <div class="row">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="tbl_pr2" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>Purchase Number</th>
                                <th>Type</th>
                                <th>Purpose</th>
                                <th>Remarks</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                @foreach($checked_pr as $checked)
                                <input type="hidden" id="pr_id" name="pr_id" />
                                <td class="pr_no_for_canvass">{{$checked->pr_no}}</td>
                                <td class="pr_type">{{$checked->type}}</td>
                                <td class="pr_purpose">{{$checked->purpose}}</td>
                                <td class="pr_remark">{{$checked->remarks}}</td>
                                <td class="pr_remark">{{$checked->action}}</td>
                                <td>
                                <a href="{{route('view_canvassed',$pr_no = $verifying->pr_no )}}" class="btn btn-danger btn-block btn-sm view_btn"><i class="fa fa-eye"></i>View</a>
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

<script>
    $(document).ready(function() {
        $("#tbl_pr").DataTable({});
        $("#tbl_pr2").DataTable({});
    });
</script>
@endsection