@extends('adminltelayout.layout')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Track Purchase Order!</h1>
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
    <div class="card mb-3">
        <div class="p-4 text-center text-white text-lg bg-dark rounded-top"><span class="text-uppercase">Purchase Order - </span><span class="text-medium" style="font-style:italic;">{{$po[0]['po_no']}}</span></div>
        <div class="d-flex flex-wrap flex-sm-nowrap justify-content-between py-3 px-2 bg-secondary">
            <div class="w-100 text-center py-1 px-2"><span class="text-medium">Department:</span> {{$po[0]['Dept_name']}}</div>
            <div class="w-100 text-center py-1 px-2"><span class="text-medium">Pr #:</span> {{$po[0]['pr_no']}}</div>
            <div class="w-100 text-center py-1 px-2"><span class="text-medium">Request Date:</span> {{date('Y-m-d' ,strtotime($po[0]['created_at']))}}</div>
        </div>
        <div class="card-body">
            <div class="steps d-flex flex-wrap flex-sm-nowrap justify-content-between padding-top-2x padding-bottom-1x">
                @if(!empty($preparedBy[0]['preparedBy']))
                <div class="step completed">
                    @else
                    <div class="step">
                        @endif
                        <div class="step-icon-wrap">
                            <div class="step-icon"><i class="pe-7s-cart"></i></div>
                        </div>
                        <h4 class="step-title">Prepared</h4>
                    </div>
                    @if(!empty($verifiedBy[0]['verifiedBy']))
                    <div class="step completed">
                        @else
                        <div class="step">
                            @endif
                            <div class="step-icon-wrap">
                                <div class="step-icon"><i class="pe-7s-bookmarks"></i></div>
                            </div>
                            <h4 class="step-title">Finance Approval</h4>
                        </div>
                        @if(!empty($approvedBy[0]['approvedBy']))
                        <div class="step completed">
                            @else
                            <div class="step">
                                @endif
                                <div class="step-icon-wrap">
                                    <div class="step-icon"><i class="pe-7s-note2"></i></div>
                                </div>
                                <h4 class="step-title">ASSD Approval</h4>
                            </div>
                            @if(!empty($orderDate[0]['orderDate']))
                            <div class="step completed">
                                @else
                                <div class="step">
                                    @endif
                                    <div class="step-icon-wrap">
                                        <div class="step-icon"><i class="pe-7s-cart"></i></div>
                                    </div>
                                    <h4 class="step-title">Ordered</h4>
                                </div>
                            <!-- @if(!empty($approved3By[0]['approved3By']))
                                <div class="step completed">
                                    @else
                                    <div class="step">
                                        @endif
                                        <div class="step-icon-wrap">
                                            <div class="step-icon"><i class="pe-7s-like2"></i></div>
                                        </div>
                                        <h4 class="step-title">CEO Approval</h4>
                                    </div>
                                </div> -->
                            <!-- </div> -->
                            <!-- <div class="d-flex flex-wrap flex-md-nowrap justify-content-center justify-content-sm-between align-items-center">
                                <div class="text-left text-sm-right"> <span class="badge badge-secondary" style="font-size:20px;"> PR Details</span> </div>
                            </div> -->
                        </div>
                        <div class="d-flex flex-wrap flex-md-nowrap justify-content-center justify-content-sm-between align-items-center">
                            <div class="text-left text-sm-right"> <span class="badge badge-secondary" style="font-size:20px;"> Activities</span> </div>
                        </div>
                        <br>
                        <div class="col-12">
                            <div class="card">
                                <div class="table">

                                    <!--Table-->
                                    <table class="table">

                                        <!--Table head-->
                                        <thead>
                                            <tr>
                                                <th class="text-center">Step</th>
                                                <th class="text-center">Activity</th>
                                                <th class="text-center">Date</th>
                                                <th class="text-center">Conducted</th>
                                            </tr>
                                        </thead>
                                        <!--Table head-->

                                        <!--Table body-->
                                        <tbody>
                                            @if(!empty($preparedBy[0]['preparedBy']))
                                            <tr>
                                                <th class="text-center" scope="row">1</th>
                                                <td class="text-center">Prepare PO</td>
                                                <td class="text-center">{{date('Y-m-d H:i:s' ,strtotime($preparedBy[0]['preparedDate']))}}</td>
                                                <td class="text-center">{{$preparedBy[0]['fname']}} {{$preparedBy[0]['mname']}} {{$preparedBy[0]['lname']}}</td>
                                            </tr>
                                            @else
                                            @endif
                                            @if(!empty($verifiedBy[0]['verifiedBy']))
                                            <tr>
                                                <th class="text-center" scope="row">2</th>
                                                <td class="text-center">Verified PO</td>
                                                <td class="text-center">{{date('Y-m-d H:i:s' ,strtotime($verifiedBy[0]['verifiedDate']))}}</td>
                                                <td class="text-center">{{$verifiedBy[0]['fname']}} {{$verifiedBy[0]['mname']}} {{$verifiedBy[0]['lname']}}</td>
                                            </tr>
                                            @else
                                            @endif
                                            @if(!empty($approvedBy[0]['approvedBy']))
                                            <tr>
                                                <th class="text-center" scope="row">3</th>
                                                <td class="text-center">Approved PO</td>
                                                <td class="text-center">{{date('Y-m-d H:i:s' ,strtotime($approvedBy[0]['approvedDate']))}}</td>
                                                <td class="text-center">{{$approvedBy[0]['fname']}} {{$approvedBy[0]['mname']}} {{$approvedBy[0]['lname']}}</td>
                                            </tr>
                                            @else
                                            @endif
                                            @if(!empty($orderDate[0]['orderDate']))
                                            <tr>
                                                <th class="text-center" scope="row">4</th>
                                                <td class="text-center">Ordered PO</td>
                                                <td class="text-center">{{date('Y-m-d H:i:s' ,strtotime($orderDate[0]['orderDate']))}}</td>
                                                <td class="text-center">{{$preparedBy[0]['fname']}} {{$preparedBy[0]['mname']}} {{$preparedBy[0]['lname']}}</td>   
                                            </tr>
                                            @else
                                            @endif
                                        </tbody>
                                        <!--Table body-->

                                    </table>
                                    <!--Table-->

                                </div>
                            </div>
                        </div>

                        <style>
                            body {
                                margin-top: 20px;
                            }

                            .steps .step {
                                display: block;
                                width: 100%;
                                margin-bottom: 35px;
                                text-align: center
                            }

                            .steps .step .step-icon-wrap {
                                display: block;
                                position: relative;
                                width: 100%;
                                height: 80px;
                                text-align: center
                            }

                            .steps .step .step-icon-wrap::before,
                            .steps .step .step-icon-wrap::after {
                                display: block;
                                position: absolute;
                                top: 50%;
                                width: 50%;
                                height: 3px;
                                margin-top: -1px;
                                background-color: #e1e7ec;
                                content: '';
                                z-index: 1
                            }

                            .steps .step .step-icon-wrap::before {
                                left: 0
                            }

                            .steps .step .step-icon-wrap::after {
                                right: 0
                            }

                            .steps .step .step-icon {
                                display: inline-block;
                                position: relative;
                                width: 80px;
                                height: 80px;
                                border: 1px solid #e1e7ec;
                                border-radius: 50%;
                                background-color: #f5f5f5;
                                color: #374250;
                                font-size: 38px;
                                line-height: 81px;
                                z-index: 5
                            }

                            .steps .step .step-title {
                                margin-top: 16px;
                                margin-bottom: 0;
                                color: white;
                                font-size: 14px;
                                font-weight: 500
                            }

                            .steps .step:first-child .step-icon-wrap::before {
                                display: none
                            }

                            .steps .step:last-child .step-icon-wrap::after {
                                display: none
                            }

                            .steps .step.completed .step-icon-wrap::before,
                            .steps .step.completed .step-icon-wrap::after {
                                background-color: #0f9d13
                            }

                            .steps .step.completed .step-icon {
                                border-color: #0f9d13;
                                background-color: #0f9d13;
                                color: #fff
                            }

                            @media (max-width: 576px) {

                                .flex-sm-nowrap .step .step-icon-wrap::before,
                                .flex-sm-nowrap .step .step-icon-wrap::after {
                                    display: none
                                }
                            }

                            @media (max-width: 768px) {

                                .flex-md-nowrap .step .step-icon-wrap::before,
                                .flex-md-nowrap .step .step-icon-wrap::after {
                                    display: none
                                }
                            }

                            @media (max-width: 991px) {

                                .flex-lg-nowrap .step .step-icon-wrap::before,
                                .flex-lg-nowrap .step .step-icon-wrap::after {
                                    display: none
                                }
                            }

                            @media (max-width: 1200px) {

                                .flex-xl-nowrap .step .step-icon-wrap::before,
                                .flex-xl-nowrap .step .step-icon-wrap::after {
                                    display: none
                                }
                            }

                            .bg-faded,
                            .bg-secondary {
                                background-color: #f5f5f5 !important;
                            }
                        </style>
                        @endsection