@extends('adminltelayout.layout')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">New Purchase Request!</h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                    <li class="breadcrumb-item active">New Purchase Request</li>
                </ol>
            </div><!-- /.col -->
        </div><!-- /.row -->
    </div><!-- /.container-fluid -->
</div>
@if(Auth::user()->position=="Corporate Treasurer" or Auth::user()->position=="Chief Executive Officer")
<p>No Access Rights</p>
@else

<div class="col-lg-12">
    <ul id="clothing-nav" class="nav nav-tabs" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" href="#new_pr" id="new_pr-tab" role="tab" data-toggle="tab" aria-controls="new_pr" aria-expanded="true">New Pr</a>
        </li>
        <!-- <li class="nav-item">
            <a class="nav-link" href="#canvassing_pr" id="canvassing_pr-tab" role="tab" data-toggle="tab" aria-controls="new_pr" aria-expanded="true">For Canvass</a>
        </li> -->
        <li class="nav-item">
            <a class="nav-link" href="#denied_pr" id="denied_pr-tab" role="tab" data-toggle="tab" aria-controls="new_pr" aria-expanded="true">Denied Pr</a>
        </li>
        </li>
    </ul>
    <div id="clothing-nav-content" class="tab-content">
        <div role="tabpanel" class="tab-pane fade show active" id="new_pr" aria-labelledby="new_pr-tab">
            <br>
            <div class="card">
                @if($pr->isEmpty())
                @else
                <div class="row">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="table1" class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>Purchase Number</th>
                                        <th>Type</th>
                                        <!-- <th>Purpose</th> -->
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        @foreach($pr as $new_pr)
                                        <input type="hidden" id="pr_id" name="pr_id" />
                                        <td class="pr_no_for_canvass">{{$new_pr->pr_no}}</td>
                                        <td class="pr_type">{{$new_pr->type}}</td>
                                        <!-- <td class="pr_purpose">{{$new_pr->purpose}}</td> -->
                                        <td class="pr_remark">{{$new_pr->remarks}}</td>
                                        <td>
                                            <a href="{{route('view_new_pr',$pr_no = $new_pr->pr_no )}}" class="btn btn-danger btn-block btn-sm view_btn"><i class="fa fa-eye"></i>View</a>
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
        <div role="tabpanel" class="tab-pane fade" id="canvassing_pr" aria-labelledby="canvassing_pr-tab">
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
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        @foreach($canvassing as $canvassing_pr)
                                        <input type="hidden" id="pr_id" name="pr_id" />
                                        <td class="pr_no_for_canvass">{{$canvassing_pr->pr_no}}</td>
                                        <td class="pr_type">{{$canvassing_pr->type}}</td>
                                        <!-- <td class="pr_purpose">{{$canvassing_pr->purpose}}</td> -->
                                        <td class="pr_remark">{{$canvassing_pr->remarks}}</td>
                                        <td>
                                            <a href="{{route('view_new_pr',$pr_no = $canvassing_pr->pr_no )}}" class="btn btn-danger btn-block btn-sm view_btn"><i class="fa fa-eye"></i>View</a>
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
        <div role="tabpanel" class="tab-pane fade" id="denied_pr" aria-labelledby="denied_pr-tab">
            <br>
            <div class="card">
                @if($deny->isEmpty())
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
                                        <th>Status</th>
                                        <th>Date</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        @foreach($deny as $denied_pr)
                                        <input type="hidden" id="pr_id" name="pr_id" />
                                        <td class="pr_no_for_canvass">{{$denied_pr->pr_no}}</td>
                                        <td class="pr_type">{{$denied_pr->type}}</td>
                                        <!-- <td class="pr_purpose">{{$denied_pr->purpose}}</td> -->
                                        <td class="pr_remark">{{$denied_pr->remarks}}</td>
                                        <td class="pr_remark">{{$denied_pr->updated_at}}</td>
                                        <td>
                                            <a href="{{route('view_new_pr',$pr_no = $denied_pr->pr_no )}}" class="btn btn-danger btn-block btn-sm view_btn"><i class="fa fa-eye"></i>View</a>
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
</script>
@endsection