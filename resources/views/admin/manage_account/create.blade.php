<!-- Modal-->

<div id="myModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
  <div role="document" class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 id="exampleModalLabel" class="modal-title">Create Account</h5>
        <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true">×</span></button>
      </div>
      <form id="createForm">
        {{csrf_field()}}
        <div class="modal-body">
          <div class="form-group">
            <label>Name</label>
            <input autocomplete="off" type="text" name="name" value="{{ old('name') }}" class="name form-control" placeholder="Name">
            <span class="glyphicon glyphicon-user form-control-feedback"></span>
            <span class="text-danger">
              <strong id="name-error"></strong>
            </span>

          </div>

          <div class="form-group">
            <label>Email</label>
            <input autocomplete="off" type="email" name="email" value="{{ old('email') }}" class="email form-control" placeholder="Email">
            <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
            <span class="text-danger">
              <strong id="email-error"></strong>
            </span>
          </div>

          <div class="form-group">
            <label for="" class="">{{ __('Password') }}</label>
            <input autocomplete="off" type="password" name="password" class="password form-control" placeholder="Password">
            <span class="glyphicon glyphicon-lock form-control-feedback"></span>
            <span class="text-danger">
              <strong id="password-error"></strong>
            </span>
          </div>
          <div class="form-group">
            <label for="" class="">{{ __('confirm-Password') }}</label>
            <input autocomplete="off" type="password" name="password_confirmation" class="password_confirm form-control" placeholder="Retype password">
            <span class="glyphicon glyphicon-log-in form-control-feedback"></span>
            <span class="text-danger">
              <strong id="password_confirm-error"></strong>
            </span>
          </div>
          <!-- <div class="form-group">
            <label for="role_id">Roles:</label>
            <select name="role_id" class="role_id form-control">
              <option value="" disabled selected>Select Role</option>
              @foreach($roles as $role)
              <option value="{{$role->id}}">{{$role->display_name}}</option>
              @endforeach
            </select>
            <span class="text-danger">
              <strong id="role_id-error"></strong>
            </span>
          </div> -->
          <div class="form-group">
            <label for="role_id">Position:</label>
            <select name="position" class="role_id form-control">
              <option value="" disabled selected>Select Position</option>
              <option value="Department Head">Department Head</option>
              <option value="Procurement Officer">Procurement Officer</option>
              <option value="ASSD Manager">ASSD Manager</option>
              <option value="Finance Head">Finance Head</option>
              <option value="Corporate Treasurer">Corporate Treasurer</option>
              <option value="Chief Executive Officer">Chief Executive Officer</option>
              <option value="Administrator">Administrator</option>
            </select>
            <span class="text-danger">
              <strong id="position-error"></strong>
            </span>
          </div>
          <div class="form-group">
            <label for="dept_id">Department:</label>
            <select name="dept_id" class="dept_id form-control">
              <option value="" disabled selected>Select Department</option>
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
            <button type="submit" id="submitForm" class="btn btn-success">Save changes</button>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>
<script>
  $().ready(function() {
    $('#createForm').on('submit', function(e) {
      e.preventDefault();
      $.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
      });
      Swal.fire({
        title: 'Create Account?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, send it!'
      }).then((result) => {
        if (result.isConfirmed) {
          $.ajax({
            type: "POST",
            url: "{{route('addaccount')}}",
            data: $('#createForm').serialize(),
            dataType: "json",
            success: function(response) {
              console.log(response);
              if (response.errors) {
                if (response.errors.name) {
                  $('#name-error').html(response.errors.name[0]);
                }
                if (response.errors.email) {
                  $('#email-error').html(response.errors.email[0]);
                }
                if (response.errors.password) {
                  $('#password-error').html(response.errors.password[0]);
                }
                if (response.errors.password_confirm) {
                  $('#password_confirm-error').html(response.errors.password_confirm[0]);
                }
                if (response.errors.role_id) {
                  $('#role_id-error').html(response.errors.role_id[0]);
                }
                if (response.errors.position) {
                  $('#position-error').html(response.errors.position[0]);
                }
              }
              if (response.success) {
                $('#myModal').modal('hide');
                //alert("data updated");
                Swal.fire({
                  position: 'center',
                  icon: 'success',
                  title: 'Your work has been saved',
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