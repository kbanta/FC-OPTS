<!-- Modal-->

<div id="build_EditModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
  <div role="document" class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 id="exampleModalLabel" class="modal-title">Edit Building</h5>
        <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true">×</span></button>
      </div>
      <div class="modal-body">
        <form id="build_EditForm">
          {{csrf_field()}}
          {{method_field('PUT')}}
          <div class="form-group">
            <input type="hidden" id="build_edit_id" name="build_edit_id" class="form-control" />
          </div>
          <div class="form-group">
            <label>Building Name.</label>
            <input type="text" name="build_edit_name" id="build_edit_name" class="form-control">
            <span class="glyphicon glyphicon-user form-control-feedback"></span>
            <span class="text-danger">
              <strong id="build_edit_name-error"></strong>
            </span>
          </div>
          <div class="form-group">
            <label>Address</label>
            <input type="text" name="build_edit_add" id="build_edit_add" class="form-control">
            <span class="glyphicon glyphicon-user form-control-feedback"></span>
            <span class="text-danger">
              <strong id="build_edit_add-error"></strong>
            </span>
          </div>
          <div class="modal-footer">
            <button type="button" data-dismiss="modal" class="btn btn-secondary">Close</button>
            <button type="submit" class="btn btn-primary ">Save changes</button>
          </div>
      </div>
      </form>
    </div>
  </div>
</div>

<script type="text/javascript">
  $().ready(function() {
    $('.build_edit_btn').on('click', function() {
      $('#build_EditModal').modal('show');

      $tr = $(this).closest('tr');

      var data = $tr.children("td").map(function() {
        return $(this).text();
      }).get();

      console.log(data);

      $('#build_edit_id').val(data[0]);
      $('#build_edit_name').val(data[1]);
      $('#build_edit_add').val(data[2]);
    });
  });
</script>

<script type="text/javascript">
  $().ready(function() {
    $('#build_EditForm').on('submit', function(e) {

      e.preventDefault();
      var id = $("#build_edit_id").val();
      //$('#logout-form').submit() // this submits the form 
      $.ajax({
        type: "PATCH",
        url: "building/update/" + id,
        data: $('#build_EditForm').serialize(),
        success: function(response) {
          console.log(response);
          if (response.errors) {
            if (response.errors.build_edit_name) {
              $('#build_edit_name-error').html(response.errors.build_edit_name[0]);
            }
            if (response.errors.build_edit_add) {
              $('#build_edit_add-error').html(response.errors.build_edit_add[0]);
            }
          }
          if (response.success) {
            // Swal.fire({
            //   title: 'Are you sure?',
            //   text: "You won't be able to revert this!",
            //   icon: 'warning',
            //   showCancelButton: true,
            //   confirmButtonColor: '#3085d6',
            //   cancelButtonColor: '#d33',
            //   confirmButtonText: 'Yes, delete it!'
            // }).then((result) => {
            //   if (result.isConfirmed) {
                $('#build_EditModal').modal('hide');
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
          //   });

          // } else {

          // }
        }
      })


    });
  });
</script>