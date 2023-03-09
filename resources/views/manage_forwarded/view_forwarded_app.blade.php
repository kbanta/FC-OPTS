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
            <section class="mt-4 pl-4">
                <span class="badge badge-danger" style="font-size: 20px; float:right;">
                    @if(!empty($forwarded[0]['status']))
                    {{ $forwarded[0]['status'] }}
                    @else
                    {{ $delivery[0]['status'] }}
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
                                <p class="mb-0">E. Aquende Bldg. III Rizal cor. Elizondo St. Legazpi City</p>
                                <div class="text-muted"><small>4500, Philippines</small></div>
                            </div>
                        </div>
                    </div>
                </div>
                <form id="forwardForm">
                    {{csrf_field()}}
                    {{method_field('PUT')}}
                    <input type="hidden" name="fwn" value="{{$forwarded[0]['forward_no']}}">
                    <input type="hidden" name="dln" value="{{$forwarded[0]['delivery_no']}}">
                    <input type="hidden" name="po_no" value="{{$delivery[0]['po_no']}}">
                    <input type="hidden" name="staff_id" value="{{$forwarded[0]['staff_id']}}">
                    <input type="hidden" name="user" value="{{$user['id']}}">

                    <div>
                        <div class="">
                            <span class="badge badge-success" style="font-size: 20px;">Transmittal</span>
                            <hr class="divider" style="background-color: currentColor;">
                            <p>To: <span>{{$delivery[0]['fname']}} {{$delivery[0]['mname']}} {{$delivery[0]['lname']}}</span></p>
                            <p>Re: <span>{{$delivery[0]['purpose']}}</span></p>
                            <p>Date: <span>{{date('Y-m-d' ,strtotime($delivery[0]['created_at']))}}</span> </p>
                            <hr class="divider" style="background-color: currentColor;">
                            <table class="table table-bordered table-sm">
                                <thead>
                                    <tr>
                                        <th width="5%"></th>
                                        <th class="text-center">Quantity</th>
                                        <th class="text-center">Particulars</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($forwarded as $forward)
                                    <tr>
                                        <td><a href="#"> <span class="badge badge-danger report_btn">Report</span> </a></td>
                                        <td class="text-center" style="display: none;">{{$forward['id']}}</td>
                                        <td class="text-center">{{$forward['item_quantity']}}</td>
                                        <td class="text-center">{{$forward['item_desc']}} {{$forward['item_brand']}}</td>
                                        @endforeach
                                    </tr>
                                </tbody>
                            </table>
                            <br>
                            <br>
                            <div class="row">
                                <div class="col-xs-2 col-sm-4 col-md-4 col-lg-4 col-xl-4">
                                    <div class="" style="text-align: center;">
                                        <p>Transmitted By:</p>
                                        <br>
                                        <h5> {{$transmittedby[0]['fname']}} {{$transmittedby[0]['mname']}}. {{$transmittedby[0]['lname']}}<i class="fa fa-check" style="color: green;"></i></h5>
                                        <p>{{$transmittedby[0]['position']}}</p>
                                    </div>
                                </div>
                                <div class="col-xs-2 col-sm-4 col-md-4 col-lg-4 col-xl-4">
                                    <div class="" style="text-align: center;">
                                        <p>Noted By:</p>
                                        <br>
                                        @if(!empty($isApproved[0]['isApproved']))
                                        <h5> {{$isApproved[0]['fname']}} {{$isApproved[0]['mname']}}. {{$isApproved[0]['lname']}}<i class="fa fa-check" style="color: green;"></i></h5>
                                        <p>{{$isApproved[0]['position']}}</p>
                                        @else
                                        <h5> Approver</i></h5>
                                        <p>waiting for approval</p>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-xs-2 col-sm-4 col-md-4 col-lg-4 col-xl-4">
                                    <div class="" style="text-align: center;">
                                        <p>Received By:</p>
                                        <br>
                                        @if(!empty($isApproved[0]['isReqReceived']))
                                        <h5> {{$delivery[0]['fname']}} {{$delivery[0]['mname']}} {{$delivery[0]['lname']}}<i class="fa fa-check" style="color: green;"></i></h5>
                                        <p>{{$user['position']}}</p>
                                        @else
                                        <h5> {{$delivery[0]['fname']}} {{$delivery[0]['mname']}} {{$delivery[0]['lname']}}</i></h5>
                                        <p>Requestor</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if(!empty($isApproved[0]['isReqReceived']))

                    @else
                    <div class="modal-footer">
                        <a href="#" class="btn btn-success fw_received_btnn"><i class="fa fa-check"> Recieved</i></a>
                    </div>
                    @endif
                </form>
            </section>
        </div>
    </div>
</div>
@include('manage_forwarded.report_item')
<script>
    $().ready(function() {
        $('.fw_received_btnn').on('click', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Received Itemm?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, send it!'
            }).then((result) => {
                if (result.isConfirmed) {
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
                                    title: 'Your work has been saved',
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
        #printPageButton {
            display: none !important;
        }
    }
</style>
@endsection