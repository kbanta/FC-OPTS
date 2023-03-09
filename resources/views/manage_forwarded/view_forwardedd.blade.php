@extends('manage_forwarded.view_forwarded')
@section('receive')
@if(Auth::user()->hasRole('Approver'))
@if(!empty($isforwarded[0]['isForwarded']))
<div class="modal-footer">
    <button class="btn btn-success fw_approved_btn" id="submit-btn"><i class="fa fa-check"> Approve</i></button>
</div>
@else

@endif
@endif
@if(!empty($isApproved[0]['isApproved']))
@if(!empty($isApproved[0]['isReqReceived']))
@else
<div class="modal-footer">
    <button class="btn btn-success fw_received_btn" id="submit-btn"><i class="fa fa-check"> Recieve</i></button>
    <!-- <a href="#" class="btn btn-success fw_received_btn"><i class="fa fa-check"> Recieve</i></a> -->
</div>
@endif
@else

@endif
<script>
    $().ready(function() {
        $('.fw_received_btn').on('click', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Received Item?',
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
</script>
@endSection