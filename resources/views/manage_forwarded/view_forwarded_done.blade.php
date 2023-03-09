@extends('adminltelayout.layout')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">View PO</h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item">Home</li>
                    <li class="breadcrumb-item active">Purchase Order</li>
                </ol>
            </div><!-- /.col -->
        </div><!-- /.row -->
    </div><!-- /.container-fluid -->
</div>

<div class="col-lg-12">
    <div class="card">
        <div class="forbes-logo-col" style="width:100%; height:auto">
            <section class="mt-4 pl-4 mr-4">
                <span class="" style="float:right;">
                    @if(Auth::user()->hasRole('Approver'))
                    @if(Auth::user()->id == $delivery[0]['user_id'] )
                    <a href="{{route('track_forwarded_app',[$dln = $delivery[0]['delivery_no'],$po_no = $delivery[0]['po_no']] )}}" class="btn btn-danger  btn-sm view_btn"><i class="fa fa-eye"></i> Track Delivery</a>
                    @else
                    @endif
                    @elseif(Auth::user()->hasRole('Validator'))
                    <a href="{{route('track_forwarded_val',[$dln = $delivery[0]['delivery_no'],$po_no = $delivery[0]['po_no']] )}}" class="btn btn-danger  btn-sm view_btn"><i class="fa fa-eye"></i> Track Delivery</a>
                    @elseif(Auth::user()->hasRole('Requestor'))
                    <a href="{{route('track_forwarded_req',[$dln = $delivery[0]['delivery_no'],$po_no = $delivery[0]['po_no']] )}}" class="btn btn-danger  btn-sm view_btn"><i class="fa fa-eye"></i> Track Delivery</a>
                    @elseif(Auth::user()->hasRole('Processor'))
                    <a href="{{route('track_forwarded_pro',[$dln = $delivery[0]['delivery_no'],$po_no = $delivery[0]['po_no'],$staff_id = $delivery[0]['user_id']] )}}" class="btn btn-danger  btn-sm view_btn"><i class="fa fa-eye"></i> Track Delivery</a>
                    @elseif(Auth::user()->hasRole('Administrator'))
                    <a href="{{route('track_forwarded_add',[$dln = $delivery[0]['delivery_no'],$po_no = $delivery[0]['po_no']] )}}" class="btn btn-danger  btn-sm view_btn"><i class="fa fa-eye"></i>Track Delivery</a>
                    @endif
                </span>
                <div class="row d-flex">
                    <div class="row">
                        <div class="col-12 col-sm-auto mb-3">
                            <div class="mx-auto" style="width: 100px;">
                                <div class="d-flex justify-content-center align-items-center rounded">
                                    <span style="color: rgb(166, 168, 170); font: bold 8pt Arial;"> <img src="{{ asset('dist/img/forbeslogo.png')}}" alt="person" class="img-fluid "> </span>
                                </div>
                            </div>
                        </div>
                        <div class="col d-flex flex-column flex-sm-row justify-content-between mb-3">
                            <div class="text-center text-sm-left mb-2 mb-sm-0">
                                <h4 class="pt-sm-2 pb-0 mb-0 text-nowrap">Forbes College Inc.</h4>
                                <p class="mb-0">E. Aquende Bldg. III Rizal Corner Elizondo St. Legazpi City</p>
                                <div class="text-muted"><small>4500, Philippines</small></div>
                            </div>
                        </div>
                    </div>
                </div>
                <form id="forwardForm">
                    {{csrf_field()}}
                    {{method_field('PUT')}}
                    @if(!empty($forwarded[0]['delivery_no']))
                    <input type="hidden" name="fwn" value="{{$forwarded[0]['forward_no']}}">
                    <input type="hidden" name="dln" value="{{$forwarded[0]['delivery_no']}}">
                    <input type="hidden" name="po_no" value="{{$delivery[0]['po_no']}}">
                    <input type="hidden" name="staff_id" value="{{$forwarded[0]['staff_id']}}">
                    <input type="hidden" name="user" value="{{$user['id']}}">
                    @endif

                    <div>
                        <div class="">
                            <span class="" style="font-size: 20px;"><b>Transmittal</b></span>
                            <hr class="divider" style="background-color: currentColor;">
                            <p>To: <span>{{$delivery[0]['fname']}} {{$delivery[0]['mname']}} {{$delivery[0]['lname']}}</span></p>
                            <p>Re: <span>{{$delivery[0]['purpose']}}</span></p>
                            <p>Date: <span>{{date('Y-m-d' ,strtotime($delivery[0]['created_at']))}}</span> </p>
                            <p>Delivery No.: <span>{{$delivery[0]['delivery_no']}}</span> </p>
                            <p>Order No.: <span>{{$delivery[0]['order_no']}}</span> </p>
                            <hr class="divider" style="background-color: currentColor;">
                            @if(!empty($isApproved[0]['isReqReceived']))
                            <a href="#" class="btn btn-danger  btn-sm report_btn"><i class="fa fa-pen"></i> Report Item</a>
                            @else
                            @endif
                            <br>
                            <table class="table table-bordered table-sm">
                                <thead>
                                    <tr>
                                        <th class="text-center">Quantity</th>
                                        <th class="text-center">Particulars</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($forwarded as $forward)
                                    <tr>
                                        <td class="text-center" style="display: none;">{{$forward['id']}}</td>
                                        <td class="text-center">{{$forward['item_quantity']}}</td>
                                        <td class="text-center">{{$forward['item_desc']}} {{$forward['item_brand']}}</td>
                                        @endforeach
                                    </tr>
                                </tbody>
                            </table>
                            <br>
                            <br>
                            @include('manage_forwarded.signatory')
                        </div>
                    </div>
                </form>
            </section>
        </div>
    </div>
</div>
@include('manage_forwarded.report_item')
<style>
    @media print {
        #printPageButton {
            display: none !important;
        }
    }
</style>
@endsection