<div id="denyMessage" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="false" class="modal fade text-left">
    <div role="document" class="modal-dialog ">
        <div class="modal-content">
            <div class="modal-header">
                <h5 id="exampleModalLabel" class="modal-title">Deny Purchase Request!</h5>
                <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true">×</span></button>
            </div>
            <div class="modal-body">
                <form id="denyMessageForm">
                    {{csrf_field()}}
                    {{method_field('PUT')}}
                    <input type="hidden" id="pr_no" name="pr_no" value="{{ $output[0]['pr_no'] }}">
                    <input type="hidden" id="email" name="email" value="{{ $user_email[0]['email'] }}">
                    <div class="form-group">
                        <label>Message:</label>
                        <textarea name="deny_message" id="deny_message" class="form-control" style="overflow:auto;resize:none" required></textarea>
                        <span class="glyphicon glyphicon-user form-control-feedback"></span>
                        <span class="text-danger">
                            <strong id="up_item_desc-error"></strong>
                        </span>
                    </div>
                    <div class="modal-footer">
                        <input type="button" class="btn btn-success deny_btn" value="Save">
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    $('.deny_btn').on('click', function(e) {
        e.preventDefault();
        Swal.fire({
            title: 'Are you sure?',
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
                        url: "deny_new_pr/" + pr_no,
                        data: $('#denyMessageForm').serialize(),
                        success: function(response) {

                            if (response.success) {
                                //alert("data updated");
                                Swal.fire({
                                    position: 'center',
                                    icon: 'success',
                                    title: 'PR has been Denied!',
                                    showConfirmButton: false,
                                    timer: 1500
                                })
                                setTimeout(function() {
                                    // location.reload();
                                    location.href =
                                        "http://127.0.0.1:8000/approver/new_pr";
                                }, 1500);
                            }
                        }
                    })
            }

        });
    });
</script>