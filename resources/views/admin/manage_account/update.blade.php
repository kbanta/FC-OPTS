<!-- Modal-->

<div id="userEditModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
  <div role="document" class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 id="exampleModalLabel" class="modal-title">Edit Account</h5>
        <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true">×</span></button>
      </div>
      <div class="modal-body">
        <form id="editForm">
          {{csrf_field()}}
          {{method_field('PUT')}}
          <div class="form-group">
            <input type="hidden" id="uid" name="uid" class="form-control" />
          </div>
          <div class="form-group">
            <label>Name</label>
            <input type="text" name="name" id="name" class="form-control">
            <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
            <span class="text-danger">
              <strong id="uname-error"></strong>
            </span>
          </div>
          <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" id="email" class="form-control">
            <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
            <span class="text-danger">
              <strong id="uemail-error"></strong>
            </span>
          </div>
          <!-- <div class="form-group">
            <label>new-Password</label>
            <input type="password" name="newpassword" id="newpassword" class="form-control">
          </div> -->
          <!-- <div class="form-group">
            <label for="urole">Update Role:</label>
            <select id="urole_id" name="urole_id" class="form-control">
              <option value="" disabled selected>Select Role</option>
              @foreach($roles as $role)
              <option value="{{$role->id}}">{{$role->display_name}}</option>
              @endforeach
            </select>
          </div> -->
          <div class="form-group">
            <label for="isActive">Status:</label>
            <select id="isActive" name="isActive" class="form-control">
              <!-- <option value="" disabled selected>Select Status</option> -->
              <option value="1">Activate</option>
              <option value="2">Deactivate</option>
            </select>
          </div>
          <div class="form-group">
            <label for="position">Position</label>
            <select name="position" id="position" class="form-control">
              <!-- <option value="" disabled selected></option> -->
              <option value="Department Head">Department Head</option>
              <option value="Procurement Officer">Procurement Officer</option>
              <option value="ASSD Manager">ASSD Manager</option>
              <option value="Finance Head">Finance Head</option>
              <option value="Corporate Treasurer">Corporate Treasurer</option>
              <option value="Chief Executive Officer">Chief Executive Officer</option>
              <option value="Administrator">Administrator</option>
            </select>
          </div>
          <div class="form-group">
            <label for="dept_id">Department:</label>
            <select name="dept_id" id="dept_id" class="dept_id form-control">
              <!-- <option value="" disabled selected>Select Department</option> -->
              @foreach($department as $dept)
              <option value="{{$dept->id}}">{{$dept->Dept_name}}</option>
              @endforeach
            </select>
            <span class="text-danger">
              <strong id="dept_id-error"></strong>
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
    $('.editbtn').on('click', function() {
      $('#userEditModal').modal('show');

      $tr = $(this).closest('tr');

      var data = $tr.children("td").map(function() {
        return $(this).text();
      }).get();

      console.log(data);

      $('#uid').val(data[0]);
      $('#name').val(data[1]);
      $('#email').val(data[2]);
      $('#urole').val(data[4]);
      $('#position option').each(function() {
        if ($(this).text() == data[4]) {
          $(this).prop('selected', true);
          return false;
        }
      });
      $('#dept_id option').each(function() {
        if ($(this).text() == data[5]) {
          $(this).prop('selected', true);
          return false;
        }
      });
      $('#isActive option').each(function() {
        if ($(this).text() == data[6]) {
          $(this).prop('selected', true);
          return false;
        }
      });
    });
  });
</script>

<script type="text/javascript">
  $().ready(function() {
    $('#editForm').on('submit', function(e) {

      e.preventDefault();
      var id = $("#uid").val();
      Swal.fire({
        title: 'Update Account?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, send it!'
      }).then((result) => {
        if (result.isConfirmed) {
          $.ajax({
            type: "PATCH",
            url: "manageAccount/update/" + id,
            data: $('#editForm').serialize(),
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
            error: function(xhr, status, error) {
          console.log(xhr.responseJSON.message);
          Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: 'Cant Deactivate Administrator',
          })
        }
          });
        }
      });
    });

  });
</script>