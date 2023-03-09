<!-- Modal-->

<div id="updateAccountModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
  <div role="document" class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 id="exampleModalLabel" class="modal-title">Edit Password</h5>
        <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true">×</span></button>
      </div>
      <div class="modal-body">
        <form id="update_AccountForm">
          {{csrf_field()}}
          {{method_field('PUT')}}
          <div class="card-body">
            <div class="row gutters">
              <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                <h6 class="mt-3 mb-2 text-primary">Password</h6>
              </div>
              <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                <div class="form-group">
                  <label for="up_password">Current Password</label>
                  <input type="password" class="form-control" name="current" id="current">
                  <span class="glyphicon glyphicon-user form-control-feedback"></span>
                  <span class="text-danger">
                    <strong id="current-error"></strong>
                  </span>
                </div>
              </div>
              <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                <div class="form-group">
                  <label for="up_password">New Password</label>
                  <input type="password" class="form-control" name="up_password" id="up_password">
                  <span class="glyphicon glyphicon-user form-control-feedback"></span>
                  <span class="text-danger">
                    <strong id="up_password-error"></strong>
                  </span>
                </div>
              </div>
              <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                <div class="form-group">
                  <label for="up_confirm_password">Confirm Password</label>
                  <input type="password" class="form-control" name="up_confirm_password" id="up_confirm_password">
                  <span class="glyphicon glyphicon-user form-control-feedback"></span>
                  <span class="text-danger">
                    <strong id="up_confirm_password-error"></strong>
                  </span>
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <div class="text-right">
              <!-- <button type="button" id="submit" name="submit" class="btn btn-secondary">Cancel</button> -->
              <button type="submit" class="btn btn-primary ">Save changes</button>
            </div>
          </div>
      </div>
      </form>
    </div>
  </div>
</div>

<script type="text/javascript">
  $().ready(function() {
    $('.update_account_btn').on('click', function() {
      // var fname = $('#fname').val();
      // var mname = $('#mname').val();
      $('#updateAccountModal').modal('show');
      // $('#up_fname').val(fname);
      // $('#up_mname').val(mname);

    });
  });
</script>

<script type="text/javascript">
  $().ready(function() {
    $('#update_AccountForm').on('submit', function(e) {

      e.preventDefault();
      Swal.fire({
        title: 'Update Password?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, send it!'
      }).then((result) => {
        if (result.isConfirmed) {
          var id = $("#profile_id").val();

          //$('#logout-form').submit() // this submits the form 
          $.ajax({
            type: "PATCH",
            url: "profile/update/password/" + id,
            data: $('#update_AccountForm').serialize(),
            success: function(response) {
              console.log(response);
              if (response.errors) {
                if (response.errors.up_password) {
                  $('#up_password-error').html(response.errors.up_password[0]);
                }
                if (response.errors.up_confirm_password) {
                  $('#up_confirm_password-error').html(response.errors.up_confirm_password[0]);
                }
                if (response.errors.current) {
                  $('#current-error').html(response.errors.current[0]);
                }
              }
              if (response.success) {
                $('#updateAccountModal').modal('hide');
                //alert("data updated");
                Swal.fire({
                  icon: 'success',
                  title: 'Data Have been updated!',
                  showConfirmButton: false,
                  timer: 3500
                });
                $('#logout-form').submit()
              }
            },
          });
        }
      });
    });

  });
</script>