@extends('adminltelayout.layout')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Dashboard</h1>
                @if(empty($userr->zipcode))
                @include('manage_profile.update_profile')
                <script>
                    $(document).ready(function() {
                        $('#updateProfileModal').modal('show');
                        var closeButton = document.getElementById("closeButton");

                        // Disable the button
                        closeButton.disabled = true;
                    });
                </script>>
                @else
                @endif
            </div><!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                    <li class="breadcrumb-item active">Dashboard</li>
                </ol>
            </div><!-- /.col -->
        </div><!-- /.row -->
    </div><!-- /.container-fluid -->
</div>
<div class="row">
    <div class="col-12 col-sm-6 col-md-4">
        <a href="{{ route('val_purchase_request') }}">
            <div class="info-box mb-3">
                <span class="info-box-icon bg-success elevation-1"><i class="fas fa-pencil-alt"></i></span>

                <div class="info-box-content">
                    <span class="info-box-text">My Requisition</span>
                    <span class="info-box-number">
                        <h4>
                            <i>
                                <span>
                                    {{$my_pr}}
                                </span>
                            </i>
                        </h4>
                    </span>
                </div>
                <!-- /.info-box-content -->
            </div>
        </a>
        <!-- /.info-box -->
    </div>
    <div class="col-12 col-sm-6 col-md-4">
        <a href="{{ route('val_purchase_order') }}">
            <div class="info-box mb-3">
                <span class="info-box-icon bg-success elevation-1"><i class="fas fa-file"></i></span>

                <div class="info-box-content">
                    <span class="info-box-text">My Purchase Order</span>
                    <span class="info-box-number">
                        <h4>
                            <i>
                                <span>
                                    {{$purchaseorder}}
                                </span>
                            </i>
                        </h4>
                    </span>
                </div>
                <!-- /.info-box-content -->
            </div>
        </a>
        <!-- /.info-box -->
    </div>
    <div class="col-12 col-sm-6 col-md-4">
        <a href="{{ route('val_to_received') }}">
            <div class="info-box mb-3">
                <span class="info-box-icon bg-success elevation-1"><i class="fas fa-calendar-alt"></i></span>

                <div class="info-box-content">
                    <span class="info-box-text">To Receive</span>
                    <span class="info-box-number">
                        <h4>
                            <i>
                                <span>
                                    {{$toreceived}}
                                </span>
                            </i>
                        </h4>
                    </span>
                </div>
                <!-- /.info-box-content -->
            </div>
        </a>
        <!-- /.info-box -->
    </div>
</div>
<div class="row">
    <div class="col-12 col-sm-6 col-md-3">
        <a href="{{ route('pr_check_fund') }}">
            <div class="info-box mb-3">
                <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-check"></i></span>

                <div class="info-box-content">
                    <span class="info-box-text">Pr Check for Fund!</span>
                    <span class="info-box-number">
                        @if(!empty($checkfund))
                        <span class="badge badge-danger">
                            <i>
                                {{$checkfund}}
                            </i>
                        </span>
                        @else
                        <span>
                            {{$checkfund}}
                        </span>
                        @endif

                    </span>
                    </i>
                </div>
                <!-- /.info-box-content -->
            </div>
        </a>
        <!-- /.info-box -->
    </div>

    <div class="col-12 col-sm-6 col-md-3">
        <a href="{{ route('po_for_approval_val') }}">
            <div class="info-box mb-3">
                <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-check"></i></span>

                <div class="info-box-content">
                    <span class="info-box-text">PO for Approval</span>
                    <span class="info-box-number">
                        @if(!empty($po))
                        <span class="badge badge-danger">
                            <i>{{$po}}</i>
                        </span>
                        @else
                        <span>
                            {{$po}}
                        </span>
                        @endif
                    </span>
                </div>
                <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
    </div>
    </a>
    <!-- /.col -->
</div>

@endsection