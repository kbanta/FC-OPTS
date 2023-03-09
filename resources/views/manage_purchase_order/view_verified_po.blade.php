@extends('manage_purchase_order.view_prepared_po')

@section('approve')
@if(!empty($verifiedBy[0]['verifiedBy']))
<div class="modal-footer">
    <button type="submit" class="btn btn-success app_approve_btn" id="submit-btn"><i class="fa fa-check"> Approve</i></button>
    <!-- <a href="" class="btn btn-success app_approve_btn">
        <i class="fa fa-check">Approve</i>
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
        $('.app_approve_btn').on('click', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Approve Purchase Order?',
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
                            url: "approve/" + po_no,
                            data: $('#purchase_orderForm').serialize(),
                            success: function(response) {

                                if (response.success) {
                                    //alert("data updated");
                                    Swal.fire({
                                        position: 'center',
                                        icon: 'success',
                                        title: 'Purchase Order has been Approved!',
                                        showConfirmButton: false,
                                        timer: 1500
                                    })
                                    setTimeout(function() {
                                        // location.reload();
                                        location.href =
                                            "http://127.0.0.1:8000/approver/po_for_approval";
                                    }, 1500);
                                }
                            }
                        })
                }

            });
        });
    });
</script>
@endsection

@section('approveCT')
<a href="" class="btn btn-success appCT_approve_btn">
    <i class="fa fa-check">ApproveCT</i>
</a>
<script type="text/javascript">
    $().ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $('.appCT_approve_btn').on('click', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Approved Purchase Order?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, send it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    po_no = jQuery('#po_no').val(),
                        $.ajax({
                            type: "PATCH",
                            url: "approveCT/" + po_no,
                            data: $('#purchase_orderForm').serialize(),
                            success: function(response) {

                                if (response.success) {
                                    //alert("data updated");
                                    Swal.fire({
                                        position: 'center',
                                        icon: 'success',
                                        title: 'Purchase Order has been Approved!',
                                        showConfirmButton: false,
                                        timer: 1500
                                    })
                                    setTimeout(function() {
                                        // location.reload();
                                        location.href =
                                            "http://127.0.0.1:8000/approver/po_for_approval";
                                    }, 1500);
                                }
                            }
                        })
                }

            });
        });

    });
</script>
@endsection

@section('approveCEO')
<div class="modal-footer">
    <a href="" class="btn btn-success appCEO_approve_btn">
        <i class="fa fa-check">ApproveCEO</i>
    </a>
</div>
<script type="text/javascript">
    $().ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $('.appCEO_approve_btn').on('click', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Approved Purchase Order?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, send it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    po_no = jQuery('#po_no').val(),
                        $.ajax({
                            type: "PATCH",
                            url: "approveCEO/" + po_no,
                            data: $('#purchase_orderForm').serialize(),
                            success: function(response) {

                                if (response.success) {
                                    //alert("data updated");
                                    Swal.fire({
                                        position: 'center',
                                        icon: 'success',
                                        title: 'Purchase Order has been Approved!',
                                        showConfirmButton: false,
                                        timer: 1500
                                    })
                                    setTimeout(function() {
                                        // location.reload();
                                        location.href =
                                            "http://127.0.0.1:8000/approver/po_for_approval";
                                    }, 1500);
                                }
                            }
                        })
                }

            });
        });

    });
</script>
@endsection