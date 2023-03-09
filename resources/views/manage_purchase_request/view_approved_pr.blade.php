@extends('manage_purchase_request.view_purchase_requestt')

@section('approved_btn')

<div class="col-lg-12" id="body_color">
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
                @if(!empty($isapproved2By[0]['isApproved2']))
                @include('manage_purchase_request.signatory')
                @else
                @endif
                <!-- <div class="modal-footer"> -->
                @if(Auth::user()->hasRole('Processor'))
                @yield('generate_po')
                <!-- <button type="submit" class="btn btn-danger">Cancel</button> -->
                @endif
                <!-- </div> -->
        </section>
        </form>
        <!-- @if(Auth::user()->hasRole('Processor'))
        <div class="modal-footer" id="printPageButton">
            <button class="btn btn-warning print_btn"><i class="fa fa-print"></i> Print</button>
        </div>
        @endif -->
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

        #print_to_po {
            display: none;
        }

        #body_color {
            color: black;
        }

        form {
            color: black;
        }
    }
</style>
<script>
    $().ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $('#checkForm').on('submit', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Cancel Pr?',
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
                                        location.reload();
                                        // location.href =
                                        //     "http://127.0.0.1:8000/processor/pr_for_canvass";
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