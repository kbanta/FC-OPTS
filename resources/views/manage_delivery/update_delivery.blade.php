<!-- Modal-->

<div id="updateDelivery" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
    <div role="document" class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 id="exampleModalLabel" class="modal-title">Update Delivery!</h5>
                <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true">×</span></button>
            </div>
            <form id="UpdateDelivery">
                {{csrf_field()}}
                {{method_field('POST')}}
                <div class="modal-body">
                    <input type="hidden" name="forward_no" id="forward_no" class="forward_no" value="{{$generateforwardno}}">
                    <i>
                        <input type="hidden" id="dln" name="dln" value="{{ $output[0]['delivery_no'] }}">
                        <input type="hidden" id="pr_no" name="pr_no" value="{{ $output[0]['pr_no'] }}">
                        <input type="hidden" id="staff_id" name="staff_id" value="{{$user->id}}">
                    </i>
                    <!-- <span class="badge badge-success" style="font-size:20px;">Item Details</span> -->
                    <div class="row">
                        @foreach(array_keys($gg) as $key => $outputss)
                        <div class="table">
                            <table class="table table-bordered table-sm">
                                <thead>
                                    <tr>
                                        <th style="display:none ;">Staff_id</th>
                                        <th width="15%">Supplier</th>
                                        <th width="15%">Description</th>
                                        <th width="15%">Brand</th>
                                        <th width="15%">Unit</th>
                                        <th width="15%">Delivered</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                    $total=0;
                                    @endphp

                                    @foreach($gg[$outputss] as $outputs)
                                    <tr>
                                        <td style="display:none ;"><input type="hidden" id="id[]" name="id[]" value="{{$outputs['id']}}"></td>
                                        <td class=""><input type="hidden" id="item_desc[]" name="item_desc[]" value="{{$outputs['business_name']}}"> {{$outputs['business_name']}}</td>
                                        <td class=""><input type="hidden" id="item_desc[]" name="item_desc[]" value="{{$outputs['item_desc']}}"> {{$outputs['item_desc']}}</td>
                                        <td class=""><input type="hidden" id="item_brand[]" name="item_brand[]" value="{{$outputs['item_brand']}}">{{$outputs['item_brand']}}</td>
                                        <td class=""><input type="hidden" id="item_unit[]" name="item_unit[]" value="{{$outputs['item_unit']}}">{{$outputs['item_unit']}}</td>
                                        <td class=""><input type="number" min="0" class="form-control text-center" id="item_quantity[]" name="item_quantity[]" value="{{$outputs['item_quantity']}}" max="{{$outputs['item_quantity']}}"></td>
                                    </tr>
                                    @endforeach
                                    <tr>
                                        <td colspan="7" class="text-center" style="color: yellow;">
                                            *****Nothing follows*****
                                        </td>
                                    </tr>
                                </tbody>

                            </table>
                            <br>
                            @endforeach

                        </div>
                    </div>
                    <div class="modal-footer">
                        <!-- <button type="button" class="btn btn-primary save_po">Save</button> -->
                        <input type="button" class="btn btn-primary update_delivery" value="Save">

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
        $('.update_delivery').on('click', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Update Delivery?',
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
                    supplier = new Array();
                    $('input[name="supplier[]"]').each(function() {
                        supplier.push($(this).val());
                    });
                    // alert(dln);
                    $.ajax({
                        type: "POST",
                        url: po_no + "/update_delivery/" + dln,
                        data: $('#UpdateDelivery').serialize(),
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
                                    //   "http://127.0.0.1:8000/processor/order_po";
                                }, 3000);
                            }
                        },
                    });
                }
            });
        });

    });
</script>