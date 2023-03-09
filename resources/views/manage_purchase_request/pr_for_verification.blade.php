@extends('adminltelayout.layout')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Verify PR!</h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                    <li class="breadcrumb-item active">Verify Purchase Request</li>
                </ol>
            </div><!-- /.col -->
        </div><!-- /.row -->
    </div><!-- /.container-fluid -->
</div>

@if($user->position=="Corporate Treasurer" or $user->position=="Chief Executive Officer")
<p>No Access Rights</p>
@else
<div class="col-lg-12">

    <ul id="clothing-nav" class="nav nav-tabs" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" href="#verify_pr" id="verify_pr-tab" role="tab" data-toggle="tab" aria-controls="verify_pr" aria-expanded="true">Verify Pr</a>
        </li>
        <!-- <li class="nav-item">
            <a class="nav-link" href="#verified_pr" role="tab" id="verified_pr-tab" data-toggle="tab" aria-controls="verified_pr">Verified Pr</a>
        </li> -->
        <li class="nav-item">
            <a class="nav-link" href="#approved_prr" id="approved_prr-tab" data-toggle="tab" aria-controls="approved_prr" aria-expanded="true">Approved PR</a>
        </li>
        </li>
    </ul>
    <div id="clothing-nav-content" class="tab-content">
        <div role="tabpanel" class="tab-pane fade show active" id="verify_pr" aria-labelledby="verify_pr-tab">
            <br>
            <div class="card">
                @if($pr->isEmpty())
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
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        @foreach($pr as $pr_for_verify)
                                        <input type="hidden" id="pr_id" name="pr_id" />
                                        <td class="pr_no_for_canvass">{{$pr_for_verify->pr_no}}</td>
                                        <td class="pr_type">{{$pr_for_verify->type}}</td>
                                        <!-- <td class="pr_purpose">{{$pr_for_verify->purpose}}</td> -->
                                        <!-- <td class="pr_remark">{{$pr_for_verify->remarks}}</td> -->
                                        <td class="pr_remark">{{$pr_for_verify->action}}</td>
                                        <td>
                                            <a href="{{route('pr_verify',$pr_no = $pr_for_verify->pr_no )}}" class="btn btn-danger btn-block btn-sm view_btn"><i class="fa fa-eye"></i>View</a>
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

        <div role="tabpanel" class="tab-pane fade" id="verified_pr" aria-labelledby="verified_pr-tab">
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
                                        <!-- <th>Purpose</th> -->
                                        <!-- <th>Remarks</th> -->
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
                                        <!-- <td class="pr_purpose">{{$verified->purpose}}</td> -->
                                        <!-- <td class="pr_remark">{{$verified->remarks}}</td> -->
                                        <td class="pr_remark">{{$verified->action}}</td>
                                        <td>
                                            <a href="{{route('update_verified',$pr_no = $verified->pr_no )}}" class="btn btn-danger btn-block btn-sm view_btn"><i class="fa fa-eye"></i>View</a>
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
        <div role="tabpanel" class="tab-pane fade" id="approved_prr" aria-labelledby="approved_prr-tab">
            <br>
            <div class="card">
                @if($approved_prr->isEmpty())
                @else
                <div class="row">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="table99" class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>Purchase Number</th>
                                        <th>Type</th>
                                        <!-- <th>Purpose</th> -->
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
                                        <!-- <td class="pr_purpose">{{$approved->purpose}}</td> -->
                                        <!-- <td class="pr_remark">{{$approved->remarks}}</td> -->
                                        <td class="pr_remark">{{$approved->action}}</td>
                                        <td>
                                            <a href="{{route('view_approved_pr_app',$pr_no = $approved->pr_no )}}" class="btn btn-danger btn-block btn-sm view_btn"><i class="fa fa-eye"></i>View</a>
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
@endif
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
        $("#table99").DataTable({
            order: [
                [0, 'desc']
            ],
        });

    });
</script>
@endsection