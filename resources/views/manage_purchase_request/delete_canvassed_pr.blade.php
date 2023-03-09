<script type="text/javascript">
    $().ready(function() {
        $('.canvass_delete_btn').on('click', function(e) {
            $tr = $(this).closest('tr');

            var data = $tr.children("td").map(function() {
                return $(this).text();
            }).get();

            console.log(data);

            $('#canvassed_item_id').val(data[0]);
            e.preventDefault();
            var id = $("#canvassed_item_id").val();
            //   alert(id);

            Swal.fire({
                title: 'Remove?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, remove it!'
            }).then((result) => {
                if (result.value === true) {
                    //$('#logout-form').submit() // this submits the form 
                    var data = {
                        "_token": $('input[name=_token]').val(),
                        "id": id,
                    };
                    $.ajax({

                        type: "DELETE",
                        url: "delete/" + id,
                        data: data,
                        success: function(response) {
                            console.log(response);
                            //$('#userEditModal').modal('hide');
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

                    });
                }
            })



        });
    });
</script>