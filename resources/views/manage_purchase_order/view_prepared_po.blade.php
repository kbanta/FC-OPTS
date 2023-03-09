@extends('manage_purchase_order.purchase_order_form')

@section('verify')
<center>
    <h3><b>Payment Per Supplier</b></h3>
</center>
<form id="purchase_orderForm">
    {{csrf_field()}}
    {{method_field('PUT')}}
    <section class="p-2">

        <div class="card col-12">
            <div class="card-header">
            </div>
            <div class="card-body p-0">
                @foreach(array_keys($gg) as $key => $outputs)
                <div class="table">
                    <table class="table table-bordered table-sm">
                        <thead>
                            <tr>
                                <th class="text-center" width="20%">Supplier</th>
                                <!-- <th class="text-center">Email</th> -->
                                <!-- <th class="text-center">Contact No.</th> -->
                                <th class="text-center" width="30%">Item Description</th>
                                <th class="text-center">Brand</th>
                                <th class="text-center" style="width: 10px;">Unit</th>
                                <th class="text-center" style="width: 10px;">Quantity</th>
                                <th class="text-center" width="15%">Price</th>
                                <th class="text-center" style="width: 13%;">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                            $total=0;
                            @endphp

                            @foreach($gg[$outputs] as $key)
                            @php
                            $total += $key['quantity']*$key['offered_price'];
                            $totall = $key['quantity']*$key['offered_price'];
                            $amount= number_format($totall, 2, '.', '');
                            $t= number_format($total, 2, '.', '');
                            @endphp
                            <tr>
                                @if(Auth::user()->hasRole('Processor'))
                                <td class="text-center" name="business_name[]" value="{{$key['business_name']}}"><a href="#" class="supp">{{$key['business_name']}}</a></td>
                                @else
                                <td class="text-center"><input type="hidden" name="b_id[]" id="b_id[]" value="{{$key['supplier_items_id']}}">{{$key['business_name']}}</td>
                                @endif
                                <td style="display: none;" class="text-center" name="business_name[]" value="{{$key['email']}}">{{$key['email']}}</td>
                                <td style="display: none;" class="text-center" name="business_name[]" value="{{$key['contact_no']}}">{{$key['contact_no']}}</td>
                                <td style="display: none;" class="text-center" name="business_name[]" value="{{$key['contact_no']}}">{{$key['business_add']}}</td>
                                <td class="text-center" name="item_desc[]" value="{{$key['item_desc']}}">{{$key['item_desc']}}</td>
                                <td class="text-center" name="brand[]" value="{{$key['brand']}}">{{$key['brand']}}</td>
                                <td class="text-center" name="unit[]" value="{{$key['unit']}}">{{$key['unit']}}</td>
                                <td class="text-center" name="quantity[]" value="{{$key['quantity']}}">{{$key['quantity']}}</td>
                                <td class="text-center" name="price[]" value="{{$key['offered_price']}}">{{$key['offered_price']}}</td>
                                <td class="text-center" id="total" name="price[]" value="{{$key['quantity']*$key['offered_price']}}">{{$amount}}</td>
                            </tr>

                            @endforeach

                            <tr>
                                <td colspan="6">

                                    <i>
                                        <p style="float: right;">Sub Total:</p>
                                    </i>
                                </td>
                                <td class="text-center">
                                    <i><b>₱ {{$t}}</b></i>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="7">
                                    <input type="hidden" name="supplier_id[]" value="{{$key['supplier_id']}}">
                                    @if(Auth::user()->hasRole('Validator') || Auth::user()->hasRole('Approver'))
                                    @if(!empty($key['payment_term']))
                                    <h3 class="card-title"> Payment Term : {{$key['payment_term']}}</h3>
                                    @else
                                    <label class="text-muted">
                                        <span class="">Payment Term :</span>
                                    </label>
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <label class="input-group-text" for="gender3">Method</label>
                                        </div>
                                        <select required aria-required="true" class="custom-select" id="paymentTerm" name="paymentTerm[]">
                                            <option value="" selected disabled>Choose...</option>
                                            <option value="Bank Transfer">Bank Transfer</option>
                                            <option value="Cash">Cash</option>
                                            <option value="Cheque">Cheque</option>
                                        </select>
                                        <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
                                        <span class="text-danger">
                                            <strong id="paymentTerm-error"></strong>
                                        </span>
                                    </div>
                                    @endif
                                    @endif
                                </td>
                            </tr>
                        </tbody>

                    </table>

                </div>
                @endforeach
                @php
                $pr=$key['pr_no']
                @endphp
                @if(Request::path() == "validator/po_prepared/view/$pr")
                <div class="modal-footer">
                    <input type="button" class="btn btn-success app_verify_btn" value="Verify" id="submit-btn">
                    <!-- <a href="" class="btn btn-success app_verify_btn" id="submit-btn">
                        <i class="fa fa-check"> Verify</i>
                    </a> -->
                </div>
                @else
                @endif
                <script type="text/javascript">
                    $().ready(function() {
                        $.ajaxSetup({
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            }
                        });
                        $('.app_verify_btn').on('click', function(e) {
                            e.preventDefault();
                            Swal.fire({
                                title: 'Verify Purchase Order?',
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
                                            url: "verify/" + po_no,
                                            data: $('#purchase_orderForm').serialize(),
                                            success: function(response) {
                                                if (response.errors) {
                                                    if (response.errors.paymentTerm) {
                                                        Swal.fire({
                                                            icon: 'error',
                                                            title: 'Oops...',
                                                            text: 'Payment Term is Required',
                                                        })
                                                        // $('#paymentTerm-error').html(response.errors.paymentTerm[0]);
                                                        // alert("this");
                                                    }
                                                }
                                                if (response.success) {
                                                    //alert("data updated");
                                                    Swal.fire({
                                                        position: 'center',
                                                        icon: 'success',
                                                        title: 'Purchase Order has been Verified!',
                                                        showConfirmButton: false,
                                                        timer: 1500
                                                    })
                                                    setTimeout(function() {
                                                        // location.reload();
                                                        location.href =
                                                            "http://127.0.0.1:8000/validator/po_for_approval";
                                                    }, 1500);
                                                }
                                            },
                                            error: function(xhr, status, error) {
                                                console.log(xhr.responseJSON.message);
                                                Swal.fire({
                                                    icon: 'error',
                                                    title: 'Oops...',
                                                    text: 'Payment Term is Required',
                                                })
                                            }
                                        })
                                }

                            });
                        });
                    });
                </script>
                @endsection