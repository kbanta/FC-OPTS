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
<div class="col-lg-12">
    <div class="card">
        <div class="row d-flex justify-content-between px-3 top ">
            <div class="d-flex">
                <h5>Purchase Request #: <span class="text-primary font-weight-bold">{{$pr[0]['pr_no']}}</span></h5>
            </div>
        </div> <!-- Add class 'active' to progress -->
        <div class="row d-flex justify-content-center">
            <div class="col-12">
                <ul id="progressbar" class="text-center">
                    <li class="active step0"></li>
                    @if(!empty($canvassed[0]['canvass_no']))
                    <li class="active step0"></li>
                    @else
                    <li class="step0"></li>
                    @endif
                    @if(!empty($isverifyBy[0]['isVerified']))
                    <li class="active step0"></li>
                    @else
                    <li class="step0"></li>
                    @endif
                    @if(!empty($ischeckfundBy[0]['isCheckfund']))
                    <li class="active step0"></li>
                    @else
                    <li class="step0"></li>
                    @endif
                    @if(!empty($isapproved2By[0]['isApproved2']))
                    <li class="active step0"></li>
                    @else
                    <li class="step0"></li>
                    @endif
                </ul>
            </div>
        </div>
        <div class="row justify-content-between top">
            <div class="row d-flex icon-content"> <img class="icon" src="{{ asset('dist/img/icon/checklist.png')}}">
                <div class="d-flex flex-column">
                    <p class="font-weight-bold">Send<br>Request</p>
                </div>
            </div>
            @if(!empty($canvassed[0]['canvass_no']))
            <div class="row d-flex icon-content"> <img class="icon" src="{{ asset('dist/img/icon/checklist.png')}}">
                <div class="d-flex flex-column">
                    <p class="font-weight-bold">Canvass<br>Request</p>
                </div>
            </div>
            @else
            <div class="row d-flex icon-content"> <img class="icon" src="{{ asset('dist/img/icon/wait.png')}}">
                <div class="d-flex flex-column">
                    <p class="font-weight-bold" style="color: grey;">Canvass<br>Request</p>
                </div>
            </div>
            @endif

            @if(!empty($isverifyBy[0]['isVerified']))
            <div class="row d-flex icon-content"> <img class="icon" src="{{ asset('dist/img/icon/checklist.png')}}">
                <div class="d-flex flex-column">
                    <p class="font-weight-bold">Verified<br>Request</p>
                </div>
            </div>
            @else
            <div class="row d-flex icon-content"> <img class="icon" src="{{ asset('dist/img/icon/wait.png')}}">
                <div class="d-flex flex-column">
                    <p class="font-weight-bold" style="color: grey;">Verified<br>Request</p>
                </div>
            </div>
            @endif
            
            @if(!empty($ischeckfundBy[0]['isCheckfund']))
            <div class="row d-flex icon-content"> <img class="icon" src="{{ asset('dist/img/icon/checklist.png')}}">
                <div class="d-flex flex-column">
                    <p class="font-weight-bold">Check for<br>Fund</p>
                </div>
            </div>
            @else
            <div class="row d-flex icon-content"> <img class="icon" src="{{ asset('dist/img/icon/wait.png')}}">
                <div class="d-flex flex-column">
                    <p class="font-weight-bold" style="color: grey;">Check for<br>Fund</p>
                </div>
            </div>
            @endif

            @if(!empty($isapproved2By[0]['isApproved2']))
            <div class="row d-flex icon-content"> <img class="icon" src="{{ asset('dist/img/icon/checklist.png')}}">
                <div class="d-flex flex-column">
                    <p class="font-weight-bold">Approved<br>Request</p>
                </div>
            </div>
            @else
            <div class="row d-flex icon-content"> <img class="icon" src="{{ asset('dist/img/icon/wait.png')}}">
                <div class="d-flex flex-column">
                    <p class="font-weight-bold" style="color: grey;">Approved<br>Request</p>
                </div>
            </div>
            @endif
        </div>
    </div>
    <div class="col-lg-12">
        <div class="">
            <br>
            <center>
                <div class="">
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
                                @if(!empty($canvassed[0]['canvass_no']))
                                <tr>
                                    <th class="text-center" scope="row">2</th>
                                    <td class="text-center">Canvassed Request</td>
                                    <td class="text-center">{{date('Y-m-d H:i:s' ,strtotime($canvassed[0]['created_at']))}}</td>
                                    <td class="text-center">{{$processor[0]['fname']}} {{$processor[0]['mname']}} {{$processor[0]['lname']}}</td>
                                </tr>
                                @else
                                @endif
                                @if(!empty($isverifyBy[0]['isVerified']))
                                <tr>
                                    <th class="text-center" scope="row">3</th>
                                    <td class="text-center">Verified Request</td>
                                    <td class="text-center">{{$isverifyBy[0]['dateVerified']}}</td>
                                    <td class="text-center">{{$isverifyBy[0]['fname']}} {{$isverifyBy[0]['mname']}} {{$isverifyBy[0]['lname']}}</td>
                                </tr>
                                @else
                                @endif
                                @if(!empty($ischeckfundBy[0]['isCheckfund']))
                                <tr>
                                    <th class="text-center" scope="row">4</th>
                                    <td class="text-center">Checked Fund</td>
                                    <td class="text-center">{{$ischeckfundBy[0]['dateCheckfund']}}</td>
                                    <td class="text-center">{{$ischeckfundBy[0]['fname']}} {{$ischeckfundBy[0]['mname']}} {{$ischeckfundBy[0]['lname']}}</td>
                                </tr>
                                @else
                                @endif
                                @if(!empty($isapproved2By[0]['isApproved2']))
                                <tr>
                                    <th class="text-center" scope="row">5</th>
                                    <td class="text-center">Approved Request</td>
                                    <td class="text-center">{{$isapproved2By[0]['dateApproved2']}}</td>
                                    <td class="text-center">{{$isapproved2By[0]['fname']}} {{$isapproved2By[0]['mname']}} {{$isapproved2By[0]['lname']}}</td>
                                </tr>
                                @else
                                @endif
                            </tbody>
                            <!--Table body-->

                        </table>
                        <!--Table-->

                    </div>
                </div>
            </center>
        </div>
    </div>
</div>
<style>
    body {
        color: #000;
        overflow-x: hidden;
        height: 100%;
        background-repeat: no-repeat
    }

    .card {
        z-index: 0;
        background-color: #ECEFF1;
        padding-bottom: 20px;
        margin-top: 10px;
        margin-bottom: 30px;
        border-radius: 10px
    }

    .top {
        padding-top: 40px;
        padding-left: 13% !important;
        padding-right: 13% !important
    }

    #progressbar {
        margin-bottom: 30px;
        overflow: hidden;
        color: #455A64;
        padding-left: 0px;
        margin-top: 30px
    }

    #progressbar li {
        list-style-type: none;
        font-size: 13px;
        width: 20%;
        float: left;
        position: relative;
        font-weight: 400
    }

    #progressbar .step0:before {
        font-family: FontAwesome;
        content: "\f10c";
        color: #fff
    }

    #progressbar li:before {
        width: 40px;
        height: 40px;
        line-height: 45px;
        display: block;
        font-size: 20px;
        background: #C5CAE9;
        border-radius: 50%;
        margin: auto;
        padding: 0px
    }

    #progressbar li:after {
        content: '';
        width: 100%;
        height: 12px;
        background: #C5CAE9;
        position: absolute;
        left: 0;
        top: 16px;
        z-index: -1
    }

    #progressbar li:last-child:after {
        border-top-right-radius: 10px;
        border-bottom-right-radius: 10px;
        position: absolute;
        left: -50%
    }

    #progressbar li:nth-child(2):after,
    #progressbar li:nth-child(3):after,
    #progressbar li:nth-child(4):after {
        left: -50%
    }

    #progressbar li:first-child:after {
        border-top-left-radius: 10px;
        border-bottom-left-radius: 10px;
        position: absolute;
        left: 50%
    }

    #progressbar li:last-child:after {
        border-top-right-radius: 10px;
        border-bottom-right-radius: 10px
    }

    #progressbar li:first-child:after {
        border-top-left-radius: 10px;
        border-bottom-left-radius: 10px
    }

    #progressbar li.active:before,
    #progressbar li.active:after {
        background: #13ad43
    }

    #progressbar li.active:before {
        font-family: FontAwesome;
        content: "\f00c"
    }

    .icon {
        width: 60px;
        height: 60px;
        margin-right: 15px
    }

    .icon-content {
        padding-bottom: 20px
    }

    @media screen and (max-width: 992px) {
        .icon-content {
            width: 50%
        }
    }
</style>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
@endsection