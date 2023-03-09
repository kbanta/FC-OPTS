@extends('adminltelayout.layout')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Approved PR!</h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                    <li class="breadcrumb-item active">Purchase Request Approval</li>
                </ol>
            </div><!-- /.col -->
        </div><!-- /.row -->
    </div><!-- /.container-fluid -->
</div>

<div class="col-lg-12">

    <ul id="clothing-nav" class="nav nav-tabs" role="tablist">
        <!-- <li class="nav-item">
            <a class="nav-link active" href="#new_pr" id="new_pr-tab" role="tab" data-toggle="tab" aria-controls="new_pr" aria-expanded="true">New PR</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#canvass_pr" id="canvass_pr-tab" data-toggle="tab" aria-controls="canvass_pr" aria-expanded="true">For Canvass</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#canvassed_pr" id="canvassed_pr-tab" data-toggle="tab" aria-controls="canvassed_pr" aria-expanded="true">Canvassed PR</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#verify_pr" id="verify_pr-tab" data-toggle="tab" aria-controls="verify_pr" aria-expanded="true">Verify PR</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#verified_pr" id="verified_pr-tab" data-toggle="tab" aria-controls="verified_pr" aria-expanded="true">Verified PR</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#checkfund_pr" id="checkfund_pr-tab" data-toggle="tab" aria-controls="checkfund_pr" aria-expanded="true">CheckFund PR</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#checked_pr" id="checked_pr-tab" data-toggle="tab" aria-controls="checked_pr" aria-expanded="true">Checked PR</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#approval_pr" id="approval_pr-tab" data-toggle="tab" aria-controls="approval_pr" aria-expanded="true">PR for Approval</a>
        </li> -->
        <li class="nav-item">
            <a class="nav-link active" href="#approved_prr" id="approved_prr-tab" data-toggle="tab" aria-controls="approved_prr" aria-expanded="true">Approved</a>
        </li>
        </li>
    </ul>
    <div id="clothing-nav-content" class="tab-content">
        <!-- <div role="tabpanel" class="tab-pane fade show active" id="new_pr" aria-labelledby="new_pr-tab">
            <br>
            <div class="card">
                <div class="row">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="table1" class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>Purchase Number</th>
                                        <th>Type</th>
                                        <th>Purpose</th>
                                        <th>Remarks</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        @foreach($pr as $new_pr)
                                        <input type="hidden" id="pr_id" name="pr_id" />
                                        <td class="pr_no_for_canvass">{{$new_pr->pr_no}}</td>
                                        <td class="pr_type">{{$new_pr->type}}</td>
                                        <td class="pr_purpose">{{$new_pr->purpose}}</td>
                                        <td class="pr_remark">{{$new_pr->remarks}}</td>
                                        <td>
                                            <a href="{{route('view_new_pr_pro',$pr_no = $new_pr->pr_no )}}" class="btn btn-danger btn-block btn-sm view_btn"><i class="fa fa-eye"></i>View</a>
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

        <div role="tabpanel" class="tab-pane fade" id="canvass_pr" aria-labelledby="canvass_pr-tab">
            <br>
            <div class="card">
                <div class="row">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="table2" class="table table-striped table-bordered">
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
                                        @foreach($canvass_pr as $pr_for_canvass)
                                        <input type="hidden" id="pr_id" name="pr_id" />
                                        <td class="pr_no_for_canvass">{{$pr_for_canvass->pr_no}}</td>
                                        <td class="pr_type">{{$pr_for_canvass->type}}</td>
                                        <td class="pr_purpose">{{$pr_for_canvass->purpose}}</td>
                                        <td class="pr_remark">{{$pr_for_canvass->remarks}}</td>
                                        <td class="pr_remark">{{$pr_for_canvass->action}}</td>
                                        <td>
                                            <a href="{{route('pr_canvass',$pr_no = $pr_for_canvass->pr_no )}}" class="btn btn-danger btn-block btn-sm view_btn"><i class="fa fa-eye"></i>View</a>
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

        <div role="tabpanel" class="tab-pane fade" id="canvassed_pr" aria-labelledby="canvassed_pr-tab">
            <br>
            <div class="card">
                <div class="row">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="table3" class="table table-striped table-bordered">
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
                                        @foreach($verifying_pr as $verifying)
                                        <input type="hidden" id="pr_id" name="pr_id" />
                                        <td class="pr_no_for_canvass">{{$verifying->pr_no}}</td>
                                        <td class="pr_type">{{$verifying->type}}</td>
                                        <td class="pr_purpose">{{$verifying->purpose}}</td>
                                        <td class="pr_remark">{{$verifying->remarks}}</td>
                                        <td class="pr_remark">{{$verifying->action}}</td>
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

        <div role="tabpanel" class="tab-pane fade" id="verify_pr" aria-labelledby="verify_pr-tab">
            <br>
            <div class="card">
                <div class="row">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="table4" class="table table-striped table-bordered">
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
                                        @foreach($verify_pr as $pr_for_verify)
                                        <input type="hidden" id="pr_id" name="pr_id" />
                                        <td class="pr_no_for_canvass">{{$pr_for_verify->pr_no}}</td>
                                        <td class="pr_type">{{$pr_for_verify->type}}</td>
                                        <td class="pr_purpose">{{$pr_for_verify->purpose}}</td>
                                        <td class="pr_remark">{{$pr_for_verify->remarks}}</td>
                                        <td class="pr_remark">{{$pr_for_verify->action}}</td>
                                        <td>
                                            <a href="{{route('pr_verify_pro',$pr_no = $pr_for_verify->pr_no )}}" class="btn btn-danger btn-block btn-sm view_btn"><i class="fa fa-eye"></i>View</a>
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

        <div role="tabpanel" class="tab-pane fade" id="verified_pr" aria-labelledby="verified_pr-tab">
            <br>
            <div class="card">
                <div class="row">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="table5" class="table table-striped table-bordered">
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
                                        @foreach($verified_pr as $verified)
                                        <input type="hidden" id="pr_id" name="pr_id" />
                                        <td class="pr_no_for_canvass">{{$verified->pr_no}}</td>
                                        <td class="pr_type">{{$verified->type}}</td>
                                        <td class="pr_purpose">{{$verified->purpose}}</td>
                                        <td class="pr_remark">{{$verified->remarks}}</td>
                                        <td class="pr_remark">{{$verified->action}}</td>
                                        <td>
                                            <a href="{{route('update_verified_pro',$pr_no = $verified->pr_no )}}" class="btn btn-danger btn-block btn-sm view_btn"><i class="fa fa-eye"></i>View</a>
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

        <div role="tabpanel" class="tab-pane fade" id="checkfund_pr" aria-labelledby="checkfund_pr-tab">
            <br>
            <div class="card">
                <div class="row">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="table6" class="table table-striped table-bordered">
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
                                        @foreach($checkfund_pr as $pr_check_fund)
                                        <input type="hidden" id="pr_id" name="pr_id" />
                                        <td class="pr_no_for_canvass">{{$pr_check_fund->pr_no}}</td>
                                        <td class="pr_type">{{$pr_check_fund->type}}</td>
                                        <td class="pr_purpose">{{$pr_check_fund->purpose}}</td>
                                        <td class="pr_remark">{{$pr_check_fund->remarks}}</td>
                                        <td class="pr_remark">{{$pr_check_fund->action}}</td>
                                        <td>
                                            <a href="{{route('view_pr_check_fund_pro',$pr_no = $pr_check_fund->pr_no )}}" class="btn btn-danger btn-block btn-sm view_btn"><i class="fa fa-eye"></i>View</a>
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

        <div role="tabpanel" class="tab-pane fade" id="checked_pr" aria-labelledby="checked_pr-tab">
            <br>
            <div class="card">
                <div class="row">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="table7" class="table table-striped table-bordered">
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
                                            <a href="{{route('view_pr_checked_fund_pro',$pr_no = $checked->pr_no )}}" class="btn btn-danger btn-block btn-sm view_btn"><i class="fa fa-eye"></i>View</a>
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

        <div role="tabpanel" class="tab-pane fade" id="approval_pr" aria-labelledby="approval_pr-tab">
            <br>
            <div class="card">
                <div class="row">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="table8" class="table table-striped table-bordered">
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
                                        @foreach($approval_pr as $pr_for_approval)
                                        <input type="hidden" id="pr_id" name="pr_id" />
                                        <td class="pr_no_for_canvass">{{$pr_for_approval->pr_no}}</td>
                                        <td class="pr_type">{{$pr_for_approval->type}}</td>
                                        <td class="pr_purpose">{{$pr_for_approval->purpose}}</td>
                                        <td class="pr_remark">{{$pr_for_approval->remarks}}</td>
                                        <td class="pr_remark">{{$pr_for_approval->action}}</td>
                                        <td>
                                            <a href="{{route('pr_approval_pro',$pr_no = $pr_for_approval->pr_no )}}" class="btn btn-danger btn-block btn-sm view_btn"><i class="fa fa-eye"></i>View</a>
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

        <div role="tabpanel" class="tab-pane fade show active" id="approved_prr" aria-labelledby="approved_prr-tab">
            <br>
            <div class="card">
                <div class="row">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="table9" class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>Purchase Number</th>
                                        <th>Type</th>
                                        <th>Purpose</th>
                                        <!-- <th>Remarks</th> -->
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        @foreach($approved_prr as $approved)
                                        <input type="hidden" id="pr_id" name="pr_id" />
                                        <td class="pr_no_for_canvass">{{$approved->pr_no}}</td>
                                        <td class="pr_type">{{$approved->type}}</td>
                                        <td class="pr_purpose">{{$approved->purpose}}</td>
                                        <!-- <td class="pr_remark">{{$approved->remarks}}</td> -->
                                        <td class="pr_remark">{{$approved->action}}</td>
                                        <td>
                                            <a href="{{route('view_approved_pr',$pr_no = $approved->pr_no )}}" class="btn btn-danger btn-block btn-sm view_btn"><i class="fa fa-eye"></i>View</a>
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
<!-- <div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Verified PR!</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                    <li class="breadcrumb-item active">Verify Purchase Request</li>
                </ol>
            </div>
        </div>
    </div>
</div>
<div class="col-lg-12">

</div> -->

<script>
    // $().ready(function(){
    //   $('.order_btn').on('click',function(){
    //     $('#OrderPO').modal('show');
    //     // $tr = $(this).closest('tr');

    //     // var data = $tr.children("td").map(function(){
    //     //   return $(this).text();
    //     // }).get();
    //     // console.log(data);
    //   });

    // });
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
    $(document).ready(function() {
        $("#table6").DataTable({
            order: [
                [0, 'desc']
            ],
        });

    });
    $(document).ready(function() {
        $("#table7").DataTable({
            order: [
                [0, 'desc']
            ],
        });

    });
    $(document).ready(function() {
        $("#table8").DataTable({
            order: [
                [0, 'desc']
            ],
        });

    });
    $(document).ready(function() {
        $("#table9").DataTable({
            order: [
                [0, 'desc']
            ],
        });

    });
</script>
@endsection