@extends('adminltelayout.layout')
@section('print_per_supp')
<form id="send_orderForm">
    @foreach(array_keys($gg) as $key => $outputs)
    <section class="mt-4 pl-4">
        <br>
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
    </section>
    <div class="card">
        <div class="card-header">
            <input type="hidden" id="po_no" name="po_no" value="{{$po_output[0]['po_no']}}">
            <input type="hidden" id="id" name="id" value="{{$user->id}}">
            <input type="hidden" id="position" name="position" value="{{$user->position}}">
            @if(count($exten)>1)
            <h3 class="card-title">Purchase Order # : <i style="color:green;"> <b> {{$po_output[0]['po_no']}}-0{{$exten[$outputs]['id']}}</b></i></h3>
            <input type="hidden" name="ordered_po[]" id="ordered_po[]" value="{{$po_output[0]['po_no']}}-0{{$exten[$outputs]['id']}}">
            @else
            <h3 class="card-title">Purchase Order # : <i style="color:green;"> <b> {{$po_output[0]['po_no']}}</b></i></h3>
            <input type="hidden" name="ordered_po[]" id="ordered_po[]" value="{{$po_output[0]['po_no']}}">
            @endif
            <p style="float: right;">Created Date: {{$po_output[0]['createdDate']}}</p>
            <br>
            <h4 class="card-title">Purchase Request # : {{$po_output[0]['pr_no']}}</h4>
            <input type="hidden" name="pr_no" value="{{$po_output[0]['pr_no']}}">
        </div>
    </div>

    <div class="card col-12">
        <div class="card-header">
            <h3 class="card-title"> <span class="">Item per Supplier</span></h3>
        </div>
        <div class="card-body p-0">
            <div class="table">
                <table class="table table-bordered table-sm">
                    <thead>
                        <tr>
                            <!-- <th>Supplier</th> -->
                            <th>Item Description</th>
                            <th>Brand</th>
                            <th style="width: 10px;">Unit</th>
                            <th style="width: 10px;">Quantity</th>
                            <th>Price</th>
                            <th style="width: 13%;">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                        $total=0;
                        @endphp
                        <tr>
                            @foreach($gg[$outputs] as $key)
                            @php
                            $total += $key['quantity']*$key['offered_price'];
                            $totall = $key['quantity']*$key['offered_price'];
                            $amount= number_format($totall, 2, '.', '');
                            $t= number_format($total, 2, '.', '');
                            @endphp
                            <td class=""><input type="hidden" name="item_desc[]" value="{{$key['item_desc']}}">{{$key['item_desc']}}</td>
                            <td class=""><input type="hidden" name="brand[]" value="{{$key['brand']}}">{{$key['brand']}}</td>
                            <td class=""><input type="hidden" name="unit[]" value="{{$key['unit']}}">{{$key['unit']}}</td>
                            <td class=""><input type="hidden" name="quantity[]" value="{{$key['quantity']}}">{{$key['quantity']}}</td>
                            <td class=""><input type="hidden" name="price[]" value="{{$key['offered_price']}}">{{$key['offered_price']}}</td>
                            <td class="">{{$amount}}</td>
                            <td style="display: none;"><input type="hidden" name="sid[]" value="{{$key['supplier_id']}}"></td>
                        </tr>
                        @endforeach
                        <tr>
                            <td colspan="5">
                                <i>
                                    <p style="float: right;">Total:</p>
                                </i>
                            </td>
                            <td class="">
                                <i><b>₱ {{$t}}</b></i>
                            </td>
                        </tr>
                        <tr>
                            <td><a href="#" class="supp" style="text-decoration: none;">{{$key['business_name']}}</a></td>
                            <td style="display: none;">{{$key['contact_no']}}</td>
                            <td style="display: none;">{{$key['email']}}</td>
                            <td style="display: none;">{{$key['business_add']}}</td>
                            <td colspan="6">
                                <p>Payment Term : {{$key['payment_term']}} </p>
                                <input type="hidden" name="supplier_id[]" id="supplier_id[]" value="{{$key['supplier_id']}}">
                            </td>
                        </tr>
                        </tr>
                    </tbody>
                </table>
            </div>
            <br>
            <br>
            <br>
</form>
@include('manage_order.po_signatory')
<p style="page-break-after: always;"></p>
@endforeach
<div class="modal-footer" id="printPageButton">
    <div class="btn-group" role="group" aria-label="...">
        <button class="btn btn-warning print_btn"><i class="fa fa-print"></i> Print</button>
        <button class="btn btn-success order_btn" id="submit-btn"><i class="fa fa-shopping-cart"> Order</i></button>
    </div>
</div>
<script>
    $('.print_btn').click(function() {
        window.print();
        return false;
    });
</script>
<style>
    @media print {
        #printPageButton {
            display: none;
        }

        #po_print {
            display: none;
        }

        div {
            color: black;
        }
    }
</style>
<script type="text/javascript">
    $().ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $('.order_btn').on('click', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Order P.O. ?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, send it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById("submit-btn").disabled = true;
                    po_no = jQuery('#po_no').val(),
                        $.ajax({
                            type: "PATCH",
                            url: "order/" + po_no,
                            data: $('#send_orderForm').serialize(),
                            success: function(response) {

                                if (response.success) {
                                    //alert("data updated");
                                    Swal.fire({
                                        position: 'center',
                                        icon: 'success',
                                        title: 'Purchase Order has been Ordered!',
                                        showConfirmButton: false,
                                        timer: 1500
                                    })
                                    setTimeout(function() {
                                        // location.reload();
                                        location.href =
                                            "http://127.0.0.1:8000/processor/order_po";
                                    }, 1500);
                                }
                            }
                        })
                }

            });
        });
    });
</script>
</div>
<!-- Modal-->

<div id="supplierdetails" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
    <div role="document" class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 id="exampleModalLabel" class="modal-title">Supplier Details!</h5>
                <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true">×</span></button>
            </div>
            <form id="">
                {{csrf_field()}}
                {{method_field('POST')}}
                <div class="modal-body">
                    <input type="text" class="form-control" readonly id="business_name">
                    <input type="text" class="form-control" readonly id="email">
                    <input type="text" class="form-control" readonly id="contact_no">
                    <input type="text" class="form-control" readonly id="business_add">
                    <div class="modal-footer">
                        <!-- <button type="button" class="btn btn-primary save_po">Save</button> -->
                        <!-- <input type="button" class="btn btn-primary save_po" value="Save"> -->

                    </div>

                </div>
            </form>
        </div>
    </div>
</div>
<script type="text/javascript">
    $().ready(function() {
        $('.supp').on('click', function() {
            $('#supplierdetails').modal('show');

            $tr = $(this).closest('tr');

            var data = $tr.children("td").map(function() {
                return $(this).text();
            }).get();

            console.log(data);

            $('#business_name').val(data[0]);
            $('#email').val(data[1]);
            $('#contact_no').val(data[2]);
            $('#business_add').val(data[3]);
        });
    });
</script>
@endsection