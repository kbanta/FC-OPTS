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
            <a class="nav-link active" href="#reported_items" role="tab" id="reported_items-tab" data-toggle="tab" aria-controls="reported_items">Reported Items</a>
        </li>
    </ul>
    <br>
    <div id="clothing-nav-content" class="tab-content">
        <div role="tabpanel" class="tab-pane fade show active" id="reported_items" aria-labelledby="reported_items-tab">
            <div class="card">
                <div class="row">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="table1" class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>Report no.</th>
                                        <th>Delivery no.</th>
                                        <th>Date</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        @foreach($reported as $rep)
                                        <input type="hidden" id="pr_id" name="pr_id" value="{{$rep['id']}}" />
                                        <td class="pr_no_for_canvass">{{$rep['Report_no']}}</td>
                                        <td class="pr_no_for_canvass">{{$rep['delivery_no']}}</td>
                                        <td class="pr_purpose">{{date('Y-m-d' ,strtotime($rep['created_at']))}}</td>
                                        <td>
                                            <a href="{{route('view_reported_items',[$rn = $rep['Report_no']] )}}" class="btn btn-danger btn-block btn-sm view_btn"><i class="fa fa-eye"></i>View</a>
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
</script>
@endsection