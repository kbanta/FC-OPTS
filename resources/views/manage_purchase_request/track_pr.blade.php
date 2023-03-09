@extends('adminltelayout.layout')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Track Purchase Request!</h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                    <li class="breadcrumb-item active">Purchase Request</li>
                </ol>
            </div><!-- /.col -->
        </div><!-- /.row -->
    </div><!-- /.container-fluid -->
</div>
<div class="col-12">
    <div class="card mb-3">
        <div class="p-4 text-center text-white text-lg bg-dark rounded-top"><span class="text-uppercase">Purchase Request - </span><span class="text-medium">{{$pr[0]['pr_no']}}</span></div>
        <div class="d-flex flex-wrap flex-sm-nowrap justify-content-between py-3 px-2 bg-secondary">
            <div class="w-100 text-center py-1 px-2"><span class="text-medium">Department:</span> {{$pr[0]['Dept_name']}}</div>

            <div class="w-100 text-center py-1 px-2"><span class="text-medium">Request Date:</span> {{date('Y-m-d' ,strtotime($pr[0]['created_at']))}}</div>
        </div>
        <div class="card-body">
            <div class="steps d-flex flex-wrap flex-sm-nowrap justify-content-between padding-top-2x padding-bottom-1x">
                <div class="step completed">
                    <div class="step-icon-wrap">
                        <div class="step-icon"><i class="pe-7s-cart"></i></div>
                    </div>
                    <h4 class="step-title">Send PR</h4>
                </div>
                @if(!empty($reqdenied[0]['action']))
                <div class="step completed">
                    <div class="step-icon-wrap">
                        <div class="step-icon"><i class="pe-7s-close-circle"></i></div>
                    </div>
                    <h4 class="step-title">Request Denied</h4>
                </div>
                @else
                @if(!empty($ischeckitemBy[0]['item_id']))
                <div class="step completed">
                    @else
                    <div class="step">
                        @endif
                        <div class="step-icon-wrap" style="color: red;">
                            <div class="step-icon"><i class="pe-7s-bookmarks"></i></div>
                        </div>
                        <h4 class="step-title">ASSD Check Items</h4>
                    </div>
                    @if(!empty($canvassed[0]['canvass_no']))
                    <div class="step completed">
                        @else
                        <div class="step">
                            @endif
                            <div class="step-icon-wrap">
                                <div class="step-icon"><i class="pe-7s-bookmarks"></i></div>
                            </div>
                            <h4 class="step-title">Procurement Officer Canvassed PR</h4>
                        </div>
                        @if(!empty($isverifyBy[0]['isVerified']))
                        <div class="step completed">
                            @else
                            <div class="step">
                                @endif
                                <div class="step-icon-wrap">
                                    <div class="step-icon"><i class="pe-7s-note2"></i></div>
                                </div>
                                <h4 class="step-title">ASSD Verified PR</h4>
                            </div>
                            @if(!empty($hold[0]['isHold']))
                            <div class="step completed">
                                <div class="step-icon-wrap">
                                    <div class="step-icon"><i class="pe-7s-note2"></i></div>
                                </div>
                                <h4 class="step-title">Hold PR</h4>
                            </div>
                            @else
                            @if(!empty($ischeckfundBy[0]['isCheckfund']))
                            <div class="step completed">
                                @else
                                <div class="step">
                                    @endif
                                    <div class="step-icon-wrap">
                                        <div class="step-icon"><i class="pe-7s-wallet"></i></div>
                                    </div>
                                    <h4 class="step-title">Finance check for fund</h4>
                                </div>
                                @if(!empty($isapprovedBy[0]['isApproved']))
                                <div class="step completed">
                                    @else
                                    <div class="step">
                                        @endif
                                        <div class="step-icon-wrap">
                                            <div class="step-icon"><i class="pe-7s-next-2"></i></div>
                                        </div>
                                        <h4 class="step-title">Corporate Treasurer Approval</h4>
                                    </div>
                                    @if(!empty($isapproved2By[0]['isApproved2']))
                                    <div class="step completed">
                                        @else
                                        <div class="step">
                                            @endif
                                            <div class="step-icon-wrap">
                                                <div class="step-icon"><i class="pe-7s-like2"></i></div>
                                            </div>
                                            <h4 class="step-title">CEO Approval</h4>
                                        </div>
                                    </div>
                                </div>
                                @endif
                                @endif
                            </div>
                            <div class="d-flex flex-wrap flex-md-nowrap justify-content-center justify-content-sm-between align-items-center">
                                <div class="text-left text-sm-right"> <span class="badge badge-secondary" style="font-size:20px;"> Activities</span> </div>
                            </div>
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
                                            @if(!empty($user[0]['id']))
                                            <tr>
                                                <th class="text-center" scope="row">1</th>
                                                <td class="text-center">Send Purchase Request</td>
                                                <td class="text-center">{{date('Y-m-d H:i:s' ,strtotime($pr[0]['created_at']))}}</td>
                                                <td class="text-center">{{$user[0]['fname']}} {{$user[0]['mname']}} {{$user[0]['lname']}}</td>
                                            </tr>
                                            @else
                                            @endif
                                            @if(!empty($reqdenied[0]['action']))
                                            <tr>
                                                <th class="text-center" scope="row">2</th>
                                                <td class="text-center">Request Denied</td>
                                                <td class="text-center">{{date('Y-m-d H:i:s' ,strtotime($reqdenied[0]['updated_at']))}}</td>
                                                <td class="text-center">{{$deniedby[0]['fname']}} {{$deniedby[0]['mname']}} {{$deniedby[0]['lname']}}</td>
                                            </tr>
                                            @else
                                            @if(!empty($ischeckitemBy[0]['item_id']))
                                            <tr>
                                                <th class="text-center" scope="row">2</th>
                                                <td class="text-center">Check Items</td>
                                                <td class="text-center">{{date('Y-m-d H:i:s' ,strtotime($ischeckitemBy[0]['updated_at']))}}</td>
                                                @if(!empty($checkitemby[0]['fname']))
                                                <td class="text-center">{{$checkitemby[0]['fname']}} {{$checkitemby[0]['mname']}} {{$checkitemby[0]['lname']}}</td>
                                                @else
                                                @endif
                                            </tr>
                                            @else
                                            @endif
                                            @if(!empty($canvassed[0]['canvass_no']))
                                            <tr>
                                                <th class="text-center" scope="row">3</th>
                                                <td class="text-center">Canvassed Request</td>
                                                <td class="text-center">{{date('Y-m-d H:i:s' ,strtotime($canvassed[0]['created_at']))}}</td>
                                                <td class="text-center">{{$processor[0]['fname']}} {{$processor[0]['mname']}} {{$processor[0]['lname']}}</td>
                                            </tr>
                                            @else
                                            @endif
                                            @if(!empty($isverifyBy[0]['isVerified']))
                                            <tr>
                                                <th class="text-center" scope="row">4</th>
                                                <td class="text-center">Verified Request</td>
                                                <td class="text-center">{{$isverifyBy[0]['dateVerified']}}</td>
                                                <td class="text-center">{{$isverifyBy[0]['fname']}} {{$isverifyBy[0]['mname']}} {{$isverifyBy[0]['lname']}}</td>
                                            </tr>
                                            @else
                                            @endif
                                            @if(!empty($hold[0]['isHold']))
                                            <tr>
                                                <th class="text-center" scope="row">5</th>
                                                <td class="text-center">Hold PR</td>
                                                <td class="text-center">{{$hold[0]['dateHold']}}</td>
                                                <td class="text-center">{{$ishold[0]['fname']}} {{$ishold[0]['mname']}} {{$ishold[0]['lname']}}</td>
                                            </tr>
                                            @else
                                            @endif
                                            @if(!empty($ischeckfundBy[0]['isCheckfund']))
                                            <tr>
                                                <th class="text-center" scope="row">5</th>
                                                <td class="text-center">Checked Fund</td>
                                                <td class="text-center">{{$ischeckfundBy[0]['dateCheckfund']}}</td>
                                                <td class="text-center">{{$ischeckfundBy[0]['fname']}} {{$ischeckfundBy[0]['mname']}} {{$ischeckfundBy[0]['lname']}}</td>
                                            </tr>
                                            @else
                                            @endif
                                            @if(!empty($isapprovedBy[0]['isApproved']))
                                            <tr>
                                                <th class="text-center" scope="row">6</th>
                                                <td class="text-center">Approved Request</td>
                                                <td class="text-center">{{$isapprovedBy[0]['dateApproved']}}</td>
                                                <td class="text-center">{{$isapprovedBy[0]['fname']}} {{$isapprovedBy[0]['mname']}} {{$isapprovedBy[0]['lname']}}</td>
                                            </tr>
                                            @else
                                            @endif
                                            @if(!empty($isapproved2By[0]['isApproved2']))
                                            <tr>
                                                <th class="text-center" scope="row">7</th>
                                                <td class="text-center">Approved Request</td>
                                                <td class="text-center">{{$isapproved2By[0]['dateApproved2']}}</td>
                                                <td class="text-center">{{$isapproved2By[0]['fname']}} {{$isapproved2By[0]['mname']}} {{$isapproved2By[0]['lname']}}</td>
                                            </tr>
                                            @else
                                            @endif
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