<script type="text/javascript">
  $().ready(function() {
    $('.delete_btn').on('click', function(e) {
      $tr = $(this).closest('tr');

      var data = $tr.children("td").map(function() {
        return $(this).text();
      }).get();

      console.log(data);

      $('#delete_idd').val(data[0]);
      e.preventDefault();
      var id = $("#delete_idd").val();
      //alert(id);

      Swal.fire({
        title: 'Do you want to Delete this?',
        showDenyButton: true,
        showCancelButton: true,
        confirmButtonText: 'Yes',
        denyButtonText: `No `,
      }).then((result) => {
        if (result.value === true) {
          //$('#logout-form').submit() // this submits the form 
          var data = {
            "_token": $('input[name=_token]').val(),
            "id": id,
          };
          $.ajax({

            type: "DELETE",
            url: "department/delete/" + id,
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