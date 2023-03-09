@extends('adminltelayout.layout')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Track Forwarded Deliveries!</h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                    <li class="breadcrumb-item active">forwarded delivery</li>
                </ol>
            </div><!-- /.col -->
        </div><!-- /.row -->
    </div><!-- /.container-fluid -->
</div>
<div class="col-12">
    <div class="card mb-3">
        <div class="p-4 text-center text-white text-lg bg-dark rounded-top"><span class="text-uppercase">Forward # - </span><span class="text-medium">@if(!empty($forwarded[0]['delivery_no'])){{$forwarded[0]['forward_no']}}@else @endif</span></div>
        <div class="d-flex flex-wrap flex-sm-nowrap justify-content-between py-3 px-2 bg-secondary">
            <div class="w-100 text-center py-1 px-2"><span class="text-medium">Delivery # : {{$delivery[0]['delivery_no']}}</span> </div>

            <div class="w-100 text-center py-1 px-2"><span class="text-medium">Requesting Department: {{$delivery[0]['Dept_name']}}</span> </div>
        </div>
        <div class="card-body">
            <div class="steps d-flex flex-wrap flex-sm-nowrap justify-content-between padding-top-2x padding-bottom-1x">
            @if(!empty($del[0]['status']))
                <div class="step completed">
                    @else
                    <div class="step">
                        @endif
                        <div class="step-icon-wrap">
                            <div class="step-icon"><i class="pe-7s-cart"></i></div>
                        </div>
                        <h4 class="step-title">With Delivery Number</h4>
                    </div>    
                @if(!empty($isApproved[0]['isForwarded']))
                <div class="step completed">
                    @else
                    <div class="step">
                        @endif
                        <div class="step-icon-wrap">
                            <div class="step-icon"><i class="pe-7s-next"></i></div>
                        </div>
                        <h4 class="step-title">Procurement Officer forwarded transmittal</h4>
                    </div>
                    @if(!empty($isApproved[0]['isApproved']))
                    <div class="step completed">
                        @else
                        <div class="step">
                            @endif
                            <div class="step-icon-wrap">
                                <div class="step-icon"><i class="pe-7s-like2"></i></div>
                            </div>
                            <h4 class="step-title">ASSD approved transmittal</h4>
                        </div>
                        @if(!empty($isApproved[0]['isReqReceived']))
                        <div class="step completed">
                            @else
                            <div class="step">
                                @endif
                                <div class="step-icon-wrap">
                                    <div class="step-icon"><i class="pe-7s-download"></i></div>
                                </div>
                                <h4 class="step-title">Item Recieved</h4>
                            </div>
                        </div>
                    </div>
                </div>
                <br>
                <div class="col-12">
                    <span class="">
                        <h4>Transmital info</h4>
                    </span>

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
                                @if(!empty($del[0]['status']))
                                    <tr>
                                        <th class="text-center" scope="row">1</th>
                                        <td class="text-center">With delivery number</td>
                                        <td class="text-center">{{date('Y-m-d H:i:s' ,strtotime($del[0]['created_at']))}}</td>
                                        <td class="text-center">{{$delby[0]['fname']}} {{$delby[0]['mname']}}. {{$delby[0]['lname']}}</td>
                                    </tr>
                                    @else
                                    @endif
                                    @if(!empty($isApproved[0]['isForwarded']))
                                    <tr>
                                        <th class="text-center" scope="row">2</th>
                                        <td class="text-center">Forwarded transmittal to Approve</td>
                                        <td class="text-center">{{date('Y-m-d H:i:s' ,strtotime($isApproved[0]['forwardedDate']))}}</td>
                                        <td class="text-center">{{$transmittedby[0]['fname']}} {{$transmittedby[0]['mname']}}. {{$transmittedby[0]['lname']}}</td>
                                    </tr>
                                    @else
                                    @endif
                                    @if(!empty($isApproved[0]['isApproved']))
                                    <tr>
                                        <th class="text-center" scope="row">3</th>
                                        <td class="text-center">Approved Transmittal</td>
                                        <td class="text-center">{{date('Y-m-d H:i:s' ,strtotime($isApproved[0]['approvedDate']))}}</td>
                                        <td class="text-center">{{$isApprovedBy[0]['fname']}} {{$isApprovedBy[0]['mname']}}. {{$isApprovedBy[0]['lname']}}</td>
                                    </tr>
                                    @else
                                    @endif
                                    @if(!empty($isApproved[0]['isReqReceived']))
                                    <tr>
                                        <th class="text-center" scope="row">4</th>
                                        <td class="text-center">Item Received</td>
                                        <td class="text-center">{{date('Y-m-d H:i:s' ,strtotime($isApproved[0]['reqreceivedDate']))}}</td>
                                        <td class="text-center">{{$delivery[0]['fname']}} {{$delivery[0]['mname']}} {{$delivery[0]['lname']}}</td>
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