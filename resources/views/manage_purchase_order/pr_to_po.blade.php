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
        <li class="nav-item">
            <a class="nav-link active" href="#approved_pr" id="approved_pr-tab" data-toggle="tab" aria-controls="approved_pr" aria-expanded="true">Approved PR</a>
        </li>
        <!-- <li class="nav-item">
            <a class="nav-link" href="#pr_w_po" role="tab" id="pr_w_po-tab" data-toggle="tab" aria-controls="pr_w_po">With PO</a>
        </li> -->
        </li>
    </ul>
    <div id="clothing-nav-content" class="tab-content">
        <div role="tabpanel" class="tab-pane fade show active" id="approved_pr" aria-labelledby="approved_pr-tab">
            <br>
            <div class="card">
                @if($approved_pr->isEmpty())
                @else
                <div class="row">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="table2" class="table table-striped table-bordered">
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
                                        @foreach($approved_pr as $approved)
                                        <input type="hidden" id="pr_id" name="pr_id" />
                                        <td class="pr_no_for_canvass">{{$approved->pr_no}}</td>
                                        <td class="pr_type">{{$approved->type}}</td>
                                        <td class="pr_purpose">{{$approved->purpose}}</td>
                                        <!-- <td class="pr_remark">{{$approved->remarks}}</td> -->
                                        <td class="pr_remark">{{$approved->action}}</td>
                                        <td>
                                            <a href="{{route('view_approved_pr_pro',$pr_no = $approved->pr_no )}}" class="btn btn-danger btn-block btn-sm view_btn"><i class="fa fa-eye"></i>View</a>
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

        <div role="tabpanel" class="tab-pane fade" id="pr_w_po" aria-labelledby="pr_w_po-tab">
            <br>
            <div class="card">
                @if($po->isEmpty())
                @else
                <div class="row">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="table3" class="table table-striped table-bordered">
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
                                        @foreach($po as $pos)
                                        <input type="hidden" id="pr_id" name="pr_id" value="{{$pos->id}}" />
                                        <td class="pr_no_for_canvass">{{$pos->po_no}}</td>
                                        <td class="pr_type">{{$pos->pr_no}}</td>
                                        <td class="pr_purpose">{{$pos->status}}</td>
                                        <td class="pr_remark">{{$pos->createdDate}}</td>
                                        <td>
                                            <!-- <a href="#" class="btn btn-danger btn-block btn-sm order_btn"><i class="fa fa-eye"></i>View</a> -->
                                            <a href="{{route('po_form',$pr_no = $pos->pr_no )}}" class="btn btn-danger btn-block btn-sm view_btn"><i class="fa fa-eye"></i>View</a>
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
</script>
@endsection