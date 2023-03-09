@extends('manage_purchase_request.view_purchase_requestt')

@section('check_btn')

<div class="col-lg-12">
    <div class="card">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <!-- <a href="{{url('processor/pr_for_canvass/view/generate_canvass', $pr_no=$output[0]['pr_no'])}}">
                            <button type="button" class="btn btn-success">Generate Canvass</button>
                        </a> -->
                        <span class="" style="font-size: 20px;">
                            <b>
                                Canvassed Items and Supplier!
                            </b>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <section class="p-2">
            <form id="checkForm">
                {{csrf_field()}}
                {{method_field('POST')}}
                <input type="hidden" id="id" name="id" value="{{$user->id}}">
                <table class="table table-bordered table-sm">
                    <thead>
                        <tr>
                            <th class="text-center">Item Description</th>
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
                <table class="table table-bordered table-sm">
                    <span class="badge badge-danger" style="float: right;font-size:15px;">Amount Per Supplier</span>
                    @foreach($perSupp as $toPay)
                    <tr>
                        <td colspan="6" class="text-center" name="business_name[]" value="{{$toPay['business_name']}}"> <i>
                                <p style="float: right;"> {{$toPay['business_name']}}</p>
                            </i></td>
                        <td width="15%" class="text-center" name="business_name[]" value=""> <i> ₱ {{$toPay['count']}}</i></td>
                    </tr>
                    @endforeach
                </table>
                @if(!empty($deny_pr[0]['deny_message']))
                <div class="form-group">
                    <label for="comment"><span class="badge badge-danger" style="font-size: 15px;">Hold Message:</span></label>
                    <textarea class="form-control" id="comment" style="overflow:auto;resize:none" readonly>{{$deny_pr[0]['deny_message']}}</textarea>
                </div>
                @endif
                <!-- @include('manage_purchase_request.signatory') -->
                @if(Auth::user()->hasRole('Validator'))
                @if(!empty($ischeckfundBy[0]['isCheckfund']))
                @else
                <div class="modal-footer">
                    <div class="btn btn-group">
                        @if(!empty($hold[0]['isHold']))
                        @else
                        <button type="button" data-toggle="modal" data-target="#hold" class="btn btn-danger ">Hold</button>
                        @endif
                        <button type="submit" class="btn btn-success" id="submit-btn">Approve</button>
                    </div>
                </div>
                @endif
                @endif
        </section>
        </form>
    </div>
</div>
</div>
@include('manage_purchase_request.hold')
<script>
    $().ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $('#checkForm').on('submit', function(e) {
            e.preventDefault();
            document.getElementById("submit-btn").disabled = true;
            Swal.fire({
                title: 'Check Pr?',
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
                            url: "check/" + pr_no,
                            data: $('#checkForm').serialize(),
                            success: function(response) {

                                if (response.success) {
                                    //alert("data updated");
                                    Swal.fire({
                                        position: 'center',
                                        icon: 'success',
                                        title: 'Pr has been Checked!',
                                        showConfirmButton: false,
                                        timer: 1500
                                    })
                                    setTimeout(function() {
                                        // location.reload();
                                        location.href =
                                            "http://127.0.0.1:8000/validator/pr_check_fund";
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