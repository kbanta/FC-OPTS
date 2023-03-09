<script type="text/javascript">
  $().ready(function() {
    // $('#example').DataTable();
    $('body').on('click', '.deletebtn', function(e) {
      var id = $(this).data("id");
      e.preventDefault();
      // alert(id);
      Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
      }).then((result) => {
        if (result.isConfirmed) {
          var data = {
            "_token": $('input[name=_token]').val(),
            "id": id,
          };
          $.ajax({
            type: "DELETE",
            url: "manageAccount/delete/" + id,
            data: data,
            success: function(response) {
              console.log(response);
              Swal.fire({
                icon: 'success',
                title: 'Data Have been deleted!',
                showConfirmButton: false,
                timer: 3500
              });
            }
          });
          setTimeout(function() {
            location.reload();
          }, 3000);
        }
      });
    });
  });
</script>