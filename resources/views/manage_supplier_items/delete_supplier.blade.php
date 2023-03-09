<script type="text/javascript">
  $().ready(function() {
    $('body').on('click', '.delete_supplierbtn', function(e) {
      var id = $(this).data("id");
      e.preventDefault();
      // alert(id);

      Swal.fire({
        title: 'Delete Supplier?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, send it!'
      }).then((result) => {
        if (result.value === true) {
          //$('#logout-form').submit() // this submits the form 
          var data = {
            "_token": $('input[name=_token]').val(),
            "id": id,
          };
          $.ajax({

            type: "DELETE",
            url: "supplier/delete/" + id,
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