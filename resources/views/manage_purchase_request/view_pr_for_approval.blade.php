@extends('manage_purchase_request.view_purchase_requestt')

@section('check_btn')

<div class="col-lg-12">
    <div class="card">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <span class="" style="font-size: 20px;">
                            Canvassed Items and Supplier!
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <section class="p-2">
            <form id="approvedForm">
                {{csrf_field()}}
                {{method_field('POST')}}
                <input type="hidden" id="id" name="id" value="{{$user->id}}">
                <input type="hidden" id="position" name="position" value="{{$user->position}}">
                <table class="table table-bordered table-sm">
                    <thead>
                        <tr>
                            <th class="text-center">Item Desc</th>
                            <th class="text-center">Supplier</th>
                            <th class="text-center">Brand</th>
                            <th class="text-center">Unit</th>
                            <th class="text-center">Quantity</th>
                            <th class="text-center">Price</th>
                            <th class="text-center">Amount</th>
                        </tr>
                    </thead>
                    </tr>
                    <tbody>
                        @php
                        $total=0;
                        @endphp

                        @foreach($canvassed_item as $canvass_item)
                        @php
                        $total += $canvass_item['quantity']*$canvass_item['offered_price'];
                        $totall = $canvass_item['quantity']*$canvass_item['offered_price'];
                        $amount= number_format($totall, 2, '.', '');
                        $t= number_format($total, 2, '.', '');
                        @endphp
                        <tr>
                            <td class="text-center" name="item_desc[]" value="{{$canvass_item['item_desc']}}">{{$canvass_item['item_desc']}}</td>
                            <td class="text-center" name="business_name[]" value="{{$canvass_item['business_name']}}">{{$canvass_item['business_name']}}</td>
                            <td class="text-center" name="brand[]" value="{{$canvass_item['brand']}}">{{$canvass_item['brand']}}</td>
                            <td class="text-center" name="unit[]" value="{{$canvass_item['unit']}}">{{$canvass_item['unit']}}</td>
                            <td class="text-center" name="quantity[]" value="{{$canvass_item['quantity']}}">{{$canvass_item['quantity']}}</td>
                            <td class="text-center" name="price[]" value="{{$canvass_item['offered_price']}}">{{$canvass_item['offered_price']}}</td>
                            <td class="text-center" id="total" name="price[]" value="{{$canvass_item['quantity']*$canvass_item['offered_price']}}">{{$amount}}</td>
                        <tr>
                        </tr>

                        @endforeach

                        <tr>
                            <td colspan="6">
                                <i>
                                    <p style="float: right;">Total:</p>
                                </i>
                            </td>
                            <td class="text-center">
                                <i><b>₱ {{$t}}</b></i>
                            </td>
                        </tr>
                        </tr>
                    </tbody>
                    <tfoot>
                    </tfoot>
                </table>
                <!-- @include('manage_purchase_request.signatory') -->

                <div class="modal-footer">
                    @if(!empty($isapprovedBy[0]['isApproved']))
                    @if($user->position=="Chief Executive Officer")
                    <button type="submit" class="btn btn-success" id="submit-btn">Approve</button>
                    @endif
                    @else
                    @if($user->position=="Corporate Treasurer")
                    <button type="submit" class="btn btn-success" id="submit-btn">Approve</button>
                    @endif
                    @endif
                </div>
        </section>
        </form>
    </div>
</div>
</div>
<script>
    $().ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $('#approvedForm').on('submit', function(e) {
            e.preventDefault();
            document.getElementById("submit-btn").disabled = true;
            Swal.fire({
                title: 'Approve Pr?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, send it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    pr_no = jQuery('#pr_no').val(),
                        $.ajax({
                            type: "POST",
                            url: "approve/" + pr_no,
                            data: $('#approvedForm').serialize(),
                            success: function(response) {

                                if (response.success) {
                                    //alert("data updated");
                                    Swal.fire({
                                        position: 'center',
                                        icon: 'success',
                                        title: 'Pr has been Approved!',
                                        showConfirmButton: false,
                                        timer: 1500
                                    })
                                    setTimeout(function() {
                                        // location.reload();
                                        location.href =
                                            "http://127.0.0.1:8000/approver/pr_for_approval";
                                    }, 1500);
                                }
                            }
                        })
                }

            });
        });

    })
</script>
@endsection