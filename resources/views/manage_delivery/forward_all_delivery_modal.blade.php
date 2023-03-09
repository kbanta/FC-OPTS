<!-- Modal-->

<div id="forwardall" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
    <div role="document" class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 id="exampleModalLabel" class="modal-title">Forward Deliveries!</h5>
                <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true">×</span></button>
            </div>
            <form id="forwardallForm">
                {{csrf_field()}}
                {{method_field('POST')}}
                <div class="modal-body">
                    <input type="hidden" name="forward_no" id="forward_no" class="forward_no" value="{{$generateforwardno}}">
                    <i>
                        <h2>{{$generateforwardno}}</h2>
                    </i>
                    <p>for {{ $order_info[0]['order_no'] }}</p>
                    <input type="hidden" name="order" id="order" value="{{ $order_info[0]['order_no'] }}">
                    @foreach(array_keys($dl_noo) as $key => $outputss)
                    <div class="card-body p-0">
                        <!-- <span class="" style="font-size: 18px;"></span> -->
                        <div class="table">
                            <table class="table table-bordered table-sm">
                                <thead>
                                    <tr>
                                        <th width="40%">Item Description</th>
                                        <th>Brand</th>
                                        <th>Unit</th>
                                        <th>Delivered</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($dl_noo[$outputss] as $deliver)
                                    <tr>
                                        @if(!empty($deliver['item_quantity']))
                                        <input type="hidden" id="sid" name="sid" value="{{$deliver['supplier_id']}}" />
                                        <td class=""><input type="hidden" id="item_desc[]" name="item_desc[]" value="{{$deliver['item_desc']}}"> {{$deliver['item_desc']}}</td>
                                        <td class=""><input type="hidden" id="item_brand[]" name="item_brand[]" value="{{$deliver['item_brand']}}">{{$deliver['item_brand']}}</td>
                                        <td class=""><input type="hidden" id="item_unit[]" name="item_unit[]" value="{{$deliver['item_unit']}}">{{$deliver['item_unit']}}</td>
                                        <td class=""><input type="hidden" id="item_quantity[]" name="item_quantity[]" value="{{$deliver['item_quantity']}}">{{$deliver['item_quantity']}}</td>
                                        <td class="" style="display: none;"><input type="hidden" id="dln[]" name="dln[]" value="{{$deliver['delivery_no']}}"></td>
                                        @else
                                        @endif
                                    </tr>
                                    @endforeach
                                    <tr>
                                        <td colspan="1">
                                            <input type="hidden" id="dln_pg[]" name="dln_pg[]" value="{{$deliver['delivery_no']}}">
                                            <span> <i> <b> Delivery No. </b> : {{$deliver['delivery_no']}} </i></span>
                                        </td>
                                        <td colspan="5">
                                            <span><i>Delivered Date : {{date('Y-m-d H:i:s' ,strtotime($deliver['created_at']))}}</i></span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            @endforeach
                        </div>
                    </div>
                    <div class="modal-footer">
                        <!-- <button type="button" class="btn btn-primary save_po">Save</button> -->
                        <input type="button" class="btn btn-primary forward_all_delivery_no" value="Save">

                    </div>

                </div>
            </form>
        </div>
    </div>
</div>
<script type="text/javascript">
    $().ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $('.forward_all_delivery_no').on('click', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Send for Approval?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, send it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    sid = $("input[name=sid]").val();
                    $.ajax({
                        type: "POST",
                        url: sid + "/forward_delivery",
                        data: $('#forwardallForm').serialize(),
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
                                    title: 'Order has been forwarded',
                                    showConfirmButton: false,
                                    timer: 3500
                                });
                                setTimeout(function() {
                                    // location.reload();
                                    location.href =
                                        "http://127.0.0.1:8000/processor/order_po";
                                }, 3000);
                            }
                        },
                    });
                }
            });
        });

    });
</script>