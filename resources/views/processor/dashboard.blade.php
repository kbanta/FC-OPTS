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
                </script>
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
        <a href="{{ route('pro_purchase_request') }}">
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
        <a href="{{ route('pro_purchase_order') }}">
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
        <a href="{{ route('pro_to_received') }}">
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
        <a href="{{ route('pr_for_canvass') }}">
            <div class="info-box">
                <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-search"></i></span>

                <div class="info-box-content">
                    <span class="info-box-text">PR for Canvass</span>
                    @if(!empty($canvass_pr))
                    <span class="info-box-number">
                        <span class="badge badge-danger">
                            <i>
                                {{$canvass_pr}}
                            </i>
                        </span>
                    </span>
                    @else
                    <span class="info-box-number">
                        {{$canvass_pr}}
                    </span>
                    @endif
                </div>
                <!-- /.info-box-content -->
            </div>
        </a>
        <!-- /.info-box -->
    </div>
    <div class="col-12 col-sm-6 col-md-3">
        <a href="{{ route('pr_to_po') }}">
            <div class="info-box">
                <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-thumbs-up"></i></span>

                <div class="info-box-content">
                    <span class="info-box-text">PR to PO</span>
                    <span class="info-box-number">
                        @if(!empty($approved_prr))
                        <span class="badge badge-danger">
                            <i>
                                {{$approved_prr}}
                            </i>
                        </span>
                        @else
                        <span>
                            {{$approved_prr}}
                        </span>
                        @endif
                    </span>
                </div>
                <!-- /.info-box-content -->
            </div>
        </a>

        <!-- /.info-box -->
    </div>
    <div class="col-12 col-sm-6 col-md-3">
        <a href="{{ route('order_po') }}">
            <div class="info-box">
                <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-shopping-cart"></i></span>

                <div class="info-box-content">
                    <span class="info-box-text">Need to Order PO</span>
                    <span class="info-box-number">
                        @if(!empty($approved_po))
                        <span class="badge badge-danger">
                            <i>
                                {{$approved_po}}
                            </i>
                        </span>
                        @else
                        <span>
                            {{$approved_po}}
                        </span>
                        @endif
                    </span>
                </div>
                <!-- /.info-box-content -->
            </div>
        </a>

        <!-- /.info-box -->
    </div>
    <div class="col-12 col-sm-6 col-md-3">
        <a href="{{ route('deliveries') }}">
            <div class="info-box">
                <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-truck"></i></span>

                <div class="info-box-content">
                    <span class="info-box-text">To Deliver</span>
                    <span class="info-box-number">
                        @if(!empty($delivery))
                        <span class="badge badge-danger">
                            <i>
                                {{$delivery}}
                            </i>
                        </span>
                        @else
                        <span>
                            {{$delivery}}
                        </span>
                        @endif
                    </span>
                </div>
                <!-- /.info-box-content -->
            </div>
        </a>

        <!-- /.info-box -->
    </div>
    <!-- /.col -->
</div>
<div class="row">
    <div class="col-12 col-sm-6 col-md-3">
        <a href="{{ route('reported_items') }}">
            <div class="info-box">
                <span class="info-box-icon bg-warning elevation-1"><i class="fa fa-exclamation-triangle"></i></span>

                <div class="info-box-content">
                    <span class="info-box-text">Reported Items</span>
                    <span class="info-box-number">
                        @if(!empty($rc))
                        <span class="badge badge-danger">
                            <i>
                                {{$rc}}
                            </i>
                        </span>
                        @else
                        <span>
                            {{$delivery}}
                        </span>
                        @endif
                    </span>
                </div>
                <!-- /.info-box-content -->
            </div>
        </a>

        <!-- /.info-box -->
    </div>
</div>
@endsection