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
        <div class="forbes-logo-col" style="width:100%; height:auto" id="body_color">
            <span class="" style="float:right;">
                <br>
                @if(Auth::user()->hasRole('Approver'))
                @if(Auth::user()->id == $delivery[0]['user_id'] )
                <a href="{{route('track_forwarded_app',[$dln = $delivery[0]['delivery_no'],$po_no = $delivery[0]['po_no']] )}}" class="btn btn-danger  btn-sm view_btn" id="hidebutton"><i class="fa fa-eye"></i>Track Delivery</a>
                @else
                @endif
                @elseif(Auth::user()->hasRole('Validator'))
                @if(Auth::user()->id == $delivery[0]['user_id'] )
                <a href="{{route('track_forwarded_val',[$dln = $delivery[0]['delivery_no'],$po_no = $delivery[0]['po_no'],$staff_id = $delivery[0]['user_id']] )}}" class="btn btn-danger  btn-sm view_btn" id="hidebutton"><i class="fa fa-eye"></i> Track Delivery</a>
                @else
                @endif
                @elseif(Auth::user()->hasRole('Requestor'))
                @if(Auth::user()->id == $delivery[0]['user_id'] )
                <a href="{{route('track_forwarded_req',[$dln = $delivery[0]['delivery_no'],$po_no = $delivery[0]['po_no'],$staff_id = $delivery[0]['user_id']] )}}" class="btn btn-danger  btn-sm view_btn" id="hidebutton"><i class="fa fa-eye"></i> Track Delivery</a>
                @else
                @endif
                @elseif(Auth::user()->hasRole('Processor'))
                @if(Auth::user()->id == $delivery[0]['user_id'] )
                <a href="{{route('track_forwarded_pro',[$dln = $delivery[0]['delivery_no'],$po_no = $delivery[0]['po_no'],$staff_id = $delivery[0]['user_id']] )}}" class="btn btn-danger  btn-sm view_btn" id="hidebutton"><i class="fa fa-eye"></i> Track Delivery</a>
                @else
                @endif
                @elseif(Auth::user()->hasRole('Administrator'))
                @if(Auth::user()->id == $delivery[0]['user_id'] )
                <a href="{{route('track_forwarded_add',[$dln = $delivery[0]['delivery_no'],$po_no = $delivery[0]['po_no'],$staff_id = $delivery[0]['user_id']] )}}" class="btn btn-danger  btn-sm view_btn" id="hidebutton"><i class="fa fa-eye"></i>Track Delivery</a>
                @else
                @endif
                @endif
            </span>
            <section class="mt-4 pl-4 mr-4">
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
                            @if(Auth::user()->id == $delivery[0]['user_id'] )
                            <a href="#" id="hidebutton" class="btn btn-danger  btn-sm report_btn"><i class="fa fa-pen"></i> Report Item</a>
                            @else
                            @endif
                            @else
                            @endif
                            <br>
                            <table class="table table-bordered table-sm">
                                <thead>
                                    <tr>

                                        <!-- @if(!empty($isApproved[0]['isReqReceived']))
                                        <th width="5%"></th>
                                        @else

                                        @endif -->
                                        <th class="text-center">Quantity</th>
                                        <th class="text-center">Particulars</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($forwarded as $forward)
                                    <tr>
                                        <!-- @if(!empty($isApproved[0]['isReqReceived']))
                                        <td><a href="#"> <span class="badge badge-danger report_btn">Report</span> </a></td>
                                        @else

                                        @endif -->
                                        <td class="text-center" style="display: none;">{{$forward['id']}}</td>
                                        <td class="text-center">{{$forward['item_quantity']}}</td>
                                        <td class="text-center">{{$forward['item_desc']}} {{$forward['item_brand']}}</td>
                                        @endforeach
                                    </tr>
                                </tbody>
                            </table>
                            <!-- <p>Report Item : <a href="#"> <span class="badge badge-danger report_btn">Report!</span> </a></p> -->

                            <br>
                            <br>
                            @include('manage_forwarded.signatory')
                        </div>
                    </div>
                    @php
                    $pr = $delivery[0]['pr_no'];
                    $po = $delivery[0]['po_no'];
                    $or = $delivery[0]['order_no'];
                    $dl = $delivery[0]['delivery_no'];
                    $si = $delivery[0]['supplier_id'];
                    @endphp
                    @if(Auth::user()->hasRole('Approver'))
                    @if(!empty($isApproved[0]['isApproved']))
                    @if(!empty($isApproved[0]['isReqReceived']))

                    @else
                    <div class="modal-footer">
                        <button class="btn btn-success fw_received_btn_app"><i class="fa fa-check"> Receive</i></button>
                    </div>
                    @endif
                    @else

                    @endif
                    @else
                    @if(!empty($isApproved[0]['isReqReceived']))
                    @else
                    @if(!empty($isApproved[0]['isApproved']))
                    @if(Request::path() == "processor/deliveries/approved/view/$pr/$po/$dl/$or/$si")
                    @else
                    <div class="modal-footer">
                        <button class="btn btn-success fw_received_btn" id="submit-btn"><i class="fa fa-check"> Receive</i></button>
                    </div>
                    @endif
                    @else
                    @endif
                    @endif
                    @endif
                    @yield('receive')
                    @yield('print')

                </form>
            </section>
        </div>
    </div>
</div>
@include('manage_forwarded.report_item')
<script>
    $().ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $('.fw_approved_btn').on('click', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Approve?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, send it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById("submit-btn").disabled = true;
                    po_no = $("input[name=po_no]").val();
                    dln = $("input[name=dln]").val();
                    fwn = $("input[name=fwn]").val();
                    staff_id = $("input[name=staff_id]").val();

                    $.ajax({
                        type: "PATCH",
                        url: staff_id + "/approved_forward",
                        data: $('#forwardForm').serialize(),
                        success: function(data) {
                            console.log(data);
                            if (data.errors) {
                                if (data.errors.item_id) {
                                    $('#item_id-error').html(data.errors.item_id[0]);
                                }
                            }
                            if (data.success) {
                                $('#addSupplierItem').modal('hide');
                                //alert("data updated");
                                Swal.fire({
                                    position: 'center',
                                    icon: 'success',
                                    title: 'Transmittal has been Approved!',
                                    showConfirmButton: false,
                                    timer: 3500
                                });
                                setTimeout(function() {
                                    //   location.reload();
                                    location.href =
                                        "http://127.0.0.1:8000/approver/to_transmit";
                                }, 3000);
                            }
                        },
                    });
                }
            });
        });

    });
    $().ready(function() {
        $('.fw_received_btn').on('click', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Receive Item?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, send it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById("submit-btn").disabled = true;
                    po_no = $("input[name=po_no]").val();
                    dln = $("input[name=dln]").val();
                    fwn = $("input[name=fwn]").val();
                    staff_id = $("input[name=staff_id]").val();

                    $.ajax({
                        type: "PATCH",
                        url: staff_id + "/received_items",
                        data: $('#forwardForm').serialize(),
                        success: function(data) {
                            console.log(data);
                            if (data.errors) {
                                if (data.errors.item_id) {
                                    $('#item_id-error').html(data.errors.item_id[0]);
                                }
                            }
                            if (data.success) {
                                $('#addSupplierItem').modal('hide');
                                //alert("data updated");
                                Swal.fire({
                                    position: 'center',
                                    icon: 'success',
                                    title: 'Item has been received!',
                                    showConfirmButton: false,
                                    timer: 3500
                                });
                                setTimeout(function() {
                                    location.reload();
                                    // location.href =
                                    // "http://127.0.0.1:8000/approver/to_transmit";
                                }, 3000);
                            }
                        },
                    });
                }
            });
        });

    });
    $().ready(function() {
        $('.fw_received_btn_app').on('click', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Receive Item?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, send it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById("submit-btn").disabled = true;
                    po_no = $("input[name=po_no]").val();
                    dln = $("input[name=dln]").val();
                    fwn = $("input[name=fwn]").val();
                    staff_id = $("input[name=staff_id]").val();

                    $.ajax({
                        type: "PATCH",
                        url: staff_id + "/received_items",
                        data: $('#forwardForm').serialize(),
                        success: function(data) {
                            console.log(data);
                            if (data.errors) {
                                if (data.errors.item_id) {
                                    $('#item_id-error').html(data.errors.item_id[0]);
                                }
                            }
                            if (data.success) {
                                $('#addSupplierItem').modal('hide');
                                //alert("data updated");
                                Swal.fire({
                                    position: 'center',
                                    icon: 'success',
                                    title: 'Item has been received!',
                                    showConfirmButton: false,
                                    timer: 3500
                                });
                                setTimeout(function() {
                                    // location.reload();
                                    location.href =
                                        "http://127.0.0.1:8000/approver/app_to_received";
                                }, 3000);
                            }
                        },
                    });
                }
            });
        });

    });
</script>
<script>
    $().ready(function() {
        $('.print_btn').click(function() {
            window.print();
            return false;
        });
    });
</script>
<style>
    @media print {
        #hidebutton {
            display: none;
        }
    }
</style>
@endsection