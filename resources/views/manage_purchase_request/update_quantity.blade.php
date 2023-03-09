<!-- Modal-->

<div id="updateQuantityModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
    <div role="document" class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 id="exampleModalLabel" class="modal-title">Update Quantity</h5>
                <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true">×</span></button>
            </div>
            <div class="modal-body">
                <form id="updateQuantityForm">
                    {{csrf_field()}}
                    {{method_field('PUT')}}
                    <div class="form-group">
                        <input type="hidden" id="id" name="id" class="form-control" />
                    </div>
                    <div class="form-group">
                        <label>Quantity:</label>
                        <input type="number" min="1" name="quantity" id="quantity" class="form-control">
                        <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
                        <span class="text-danger">
                            <strong id="quantity-error"></strong>
                        </span>
                        <br>
                        <div class="modal-footer">
                            <a href="#" class="btn btn-success btn-sm update_quantity" style="float: right;"><i class="fa fa-check"> Update</i></a>
                        </div>
                </form>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        $().ready(function() {
            $('.verified_update_btn').on('click', function() {
                $('#updateQuantityModal').modal('show');

                $tr = $(this).closest('tr');

                var data = $tr.children("td").map(function() {
                    return $(this).text();
                }).get();

                console.log(data);

                $('#id').val(data[0]);
                $('#quantity').val(data[4]);
            });
        });
        $().ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $('.update_quantity').on('click', function(e) {

                e.preventDefault();
                var id = $("#id").val();
                var quantity = $("#quantity").val();
                Swal.fire({
                    title: 'Update Quantity?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, send it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        pr_no = jQuery('#pr_no').val(),
                            // alert(quantity);
                            data = {
                                "token":$(`#csrf`).val(),
                                "id": id,
                                "quantity":quantity,
                            }
                        $.ajax({
                            type: "PATCH",
                            url: pr_no + "/update_quantity/" + id,
                            data: data,
                            success: function(response) {
                                console.log(response);
                                if (response.errors) {
                                    if (response.errors.name) {
                                        $('#uname-error').html(response.errors.name[0]);
                                    }
                                    if (response.errors.email) {
                                        $('#uemail-error').html(response.errors.email[0]);
                                    }
                                }

                                if (response.success) {
                                    // Swal.fire({
                                    //   title: 'Do you want to save the changes?',
                                    //   showDenyButton: true,
                                    //   showCancelButton: true,
                                    //   confirmButtonText: 'Yes!',
                                    //   denyButtonText: `Don't!`,
                                    // }).then((result) => {
                                    //   if (result.value === true) {
                                    $('#userEditModal').modal('hide');
                                    //alert("data updated");
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Data Have been updated!',
                                        showConfirmButton: false,
                                        timer: 3500
                                    });
                                    setTimeout(function() {
                                        location.reload();
                                    }, 3000);
                                }
                            },
                        });
                    }
                });
            });

        });
    </script>