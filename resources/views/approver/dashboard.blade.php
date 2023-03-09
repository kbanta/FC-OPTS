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
    @if(Auth::user()->position == 'ASSD Manager')
    <div class="col-12 col-sm-6 col-md-4">
        @else
        <div class="col-12 col-sm-6 col-md-4">
            @endif
            <a href="{{ route('app_purchase_request') }}">
                <div class="info-box">
                    <span class="info-box-icon bg-success elevation-1"><i class="fas fa-pencil-alt"></i></span>

                    <div class="info-box-content">
                        <span class="info-box-text">My Requisition</span>
                        <span class="info-box-number">
                            <h3>
                                <i>
                                    <span>
                                        {{$my_pr}}
                                    </span>
                                </i>
                            </h3>
                        </span>
                    </div>
                    <!-- /.info-box-content -->
                </div>
            </a>
            <!-- /.info-box -->
        </div>
        @if(Auth::user()->position == 'ASSD Manager')
        <div class="col-12 col-sm-6 col-md-4">
            @else
            <div class="col-12 col-sm-6 col-md-4">
                @endif
                <a href="{{ route('app_purchase_order') }}">
                    <div class="info-box mb-3">
                        <span class="info-box-icon bg-success elevation-1"><i class="fas fa-file"></i></span>

                        <div class="info-box-content">
                            <span class="info-box-text">My Purchase Order</span>
                            <span class="info-box-number">
                                <h3>
                                    <i>
                                        <span>
                                            {{$purchaseorder}}
                                        </span>
                                    </i>
                                </h3>
                            </span>
                        </div>
                        <!-- /.info-box-content -->
                    </div>
                </a>
                <!-- /.info-box -->
            </div>
            @if(Auth::user()->position == 'ASSD Manager')
            <div class="col-12 col-sm-6 col-md-4">
                @else
                <div class="col-12 col-sm-6 col-md-4">
                    @endif
                    <a href="{{ route('app_to_received') }}">
                        <div class="info-box mb-3">
                            <span class="info-box-icon bg-success elevation-1"><i class="fas fa-calendar-alt"></i></span>

                            <div class="info-box-content">
                                <span class="info-box-text">To Receive</span>
                                <span class="info-box-number">
                                    <h3>
                                        <i>
                                            <span>
                                                {{$toreceived}}
                                            </span>
                                        </i>
                                    </h3>
                                </span>
                            </div>
                            <!-- /.info-box-content -->
                        </div>
                    </a>
                    <!-- /.info-box -->
                </div>
                @if(Auth::user()->position == 'ASSD Manager')

                @else
                @endif
            </div>

            <div class="row">
                @if(Auth::user()->position == 'ASSD Manager')
                <div class="col-12 col-sm-6 col-md-3">
                    <a href="{{ route('new_pr') }}">
                        <div class="info-box">
                            <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-envelope"></i></span>

                            <div class="info-box-content">
                                <span class="info-box-text">New PR!</span>
                                <span class="info-box-number">
                                    <i>
                                        @if(!empty($newpr))
                                        <span class="badge badge-danger">
                                            {{$newpr}}
                                        </span>
                                        @else
                                        <span>
                                            {{$newpr}}
                                        </span>
                                        @endif
                                    </i>
                                </span>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-12 col-sm-6 col-md-3">
                    <a href="{{ route('pr_for_verification') }}">
                        <div class="info-box">
                            <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-search"></i></span>

                            <div class="info-box-content">
                                <span class="info-box-text">For Verification!</span>
                                <span class="info-box-number">
                                    @if(!empty($verificationpr))
                                    <span class="badge badge-danger">
                                        <i>
                                            {{$verificationpr}}
                                        </i>
                                    </span>
                                    @else
                                    <span>
                                        {{$verificationpr}}
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
                    <a href="{{ route('po_for_approval') }}">
                        <div class="info-box">
                            <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-check"></i></span>

                            <div class="info-box-content">
                                <span class="info-box-text">PO for Approval!</span>
                                <span class="info-box-number">
                                    @if(!empty($poforapproval))
                                    <span class="badge badge-danger">
                                        <i>
                                            {{$poforapproval}}
                                        </i>
                                    </span>
                                    @elseif(!empty($poforapproval2))
                                    <span class="badge badge-danger">
                                        <i>
                                            {{$poforapproval2}}
                                        </i>
                                    </span>
                                    @else
                                    <span>
                                        {{$poforapproval}}
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
                    <a href="{{ route('to_transmit') }}">
                        <div class="info-box">
                            <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-paper-plane"></i></span>

                            <div class="info-box-content">
                                <span class="info-box-text">To Transmit!</span>
                                <span class="info-box-number">
                                    @if(!empty($totransmit))
                                    <span class="badge badge-danger">
                                        <i>
                                            {{$totransmit}}
                                        </i>
                                    </span>
                                    @else
                                    <span>
                                        {{$totransmit}}
                                    </span>
                                    @endif
                                </span>
                            </div>
                            <!-- /.info-box-content -->
                        </div>
                    </a>
                    <!-- /.info-box -->
                </div>
                @else
                <div class="col-12 col-sm-6 col-md-3">
                    <a href="{{ route('pr_for_approval') }}">
                        <div class="info-box">
                            <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-check"></i></span>

                            <div class="info-box-content">
                                <span class="info-box-text">PR for Approval!</span>
                                <span class="info-box-number">
                                    @if(!empty($prforapproval ))
                                    <span class="badge badge-danger">
                                        <i>
                                            {{$prforapproval}}

                                        </i>
                                    </span>
                                    @else
                                    <span>
                                        {{$prforapproval}}
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
                    <a href="{{ route('po_for_approval') }}">
                        <div class="info-box">
                            <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-check"></i></span>

                            <div class="info-box-content">
                                <span class="info-box-text">PO for Approval!</span>
                                <span class="info-box-number">
                                    @if(!empty($poforapproval))
                                    <span class="badge badge-danger">
                                        <i>
                                            {{$poforapproval}}
                                        </i>
                                    </span>
                                    @elseif(!empty($poforapproval2))
                                    @if(Auth::user()->position == 'ASSD Manager')
                                    <span class="badge badge-danger">
                                        @else
                                        @endif
                                        <i>
                                            {{$poforapproval2}}
                                        </i>
                                    </span>
                                    @else
                                    <span>
                                        {{$poforapproval}}
                                    </span>
                                    @endif
                                </span>
                            </div>
                            <!-- /.info-box-content -->
                        </div>
                    </a>
                    <!-- /.info-box -->
                </div>
                @endif
            </div>
            @endsection