@extends('manage_purchase_order.purchase_order_form')

@section('prepare')

<div class="modal-footer">
    <a href="#" class="btn btn-success pro_app_btn">
        <i class="fa fa-check" style="color: white;"></i> Prepare</a>
</div>
<script type="text/javascript">
    $().ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $('.pro_app_btn').on('click', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Prepare Purchase Order?',
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
                            url: "prepare/" + po_no,
                            data: $('#purchase_orderForm').serialize(),
                            success: function(response) {

                                if (response.success) {
                                    //alert("data updated");
                                    Swal.fire({
                                        position: 'center',
                                        icon: 'success',
                                        title: 'Purchase Order has been Prepared!',
                                        showConfirmButton: false,
                                        timer: 1500
                                    })
                                    setTimeout(function() {
                                        // location.reload();
                                        location.href =
                                            "http://127.0.0.1:8000/processor/approved_po";
                                    }, 1500);
                                }
                            }
                        })
                }

            });
        });
    });
    $(document).ready(function() {
        $("#example1").DataTable({})

    });

    function showHideDiv(ele) {
        var srcElement = document.getElementById(ele);
        if (srcElement != null) {
            if (srcElement.style.display == "block") {
                srcElement.style.display = 'none';
            } else {
                srcElement.style.display = 'block';
            }
            return false;
        }
    }
</script>
@endsection