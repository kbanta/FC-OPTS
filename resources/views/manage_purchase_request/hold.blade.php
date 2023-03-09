<div id="hold" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="false" class="modal fade text-left">
    <div role="document" class="modal-dialog ">
        <div class="modal-content">
            <div class="modal-header">
                <h5 id="exampleModalLabel" class="modal-title">Hold Request</h5>
                <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true">×</span></button>
            </div>
            <div class="modal-body">
                <form id="holdForm">
                    {{csrf_field()}}
                    {{method_field('PUT')}}
                    <input type="hidden" id="pr_no" name="pr_no" value="{{ $output[0]['pr_no'] }}">
                    <div class="form-group">
                        <label>Message:</label>
                        <textarea name="deny_message" id="deny_message" class="form-control" style="overflow:auto;resize:none" required></textarea>
                        <span class="glyphicon glyphicon-user form-control-feedback"></span>
                        <span class="text-danger">
                            <strong id="up_item_desc-error"></strong>
                        </span>
                    </div>
                    <div class="modal-footer">
                        <input type="button" class="btn btn-success hold_btn" value="Save">
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    $('.hold_btn').on('click', function(e) {
        e.preventDefault();
        Swal.fire({
            title: 'Hold PR?',
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
                        type: "PATCH",
                        url: pr_no + "/hold_new_pr",
                        data: $('#holdForm').serialize(),
                        success: function(response) {

                            if (response.success) {
                                //alert("data updated");
                                Swal.fire({
                                    position: 'center',
                                    icon: 'success',
                                    title: 'PR have been Hold!',
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
</script>