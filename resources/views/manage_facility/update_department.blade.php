<!-- Modal-->

<div id="dept_EditModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
  <div role="document" class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 id="exampleModalLabel" class="modal-title">Edit Department</h5>
        <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true">×</span></button>
      </div>
      <div class="modal-body">
        <form id="dept_EditForm">
          {{csrf_field()}}
          {{method_field('PUT')}}
          <div class="form-group">
            <input type="hidden" id="dept_edit_id" name="dept_edit_id" class="form-control" />
          </div>
          <div class="form-group">
            <label>Department No.</label>
            <input type="text" name="dept_no" id="dept_no" class="form-control">
            <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
            <span class="text-danger">
              <strong id="dept_no-error"></strong>
            </span>
          </div>
          <div class="form-group">
            <label>Department Name</label>
            <input type="text" name="dept_edit_name" id="dept_edit_name" class="form-control">
            <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
            <span class="text-danger">
              <strong id="dept_edit_name-error"></strong>
            </span>
          </div>
          <div class="form-group">
            <label for="build_id">Update Building:</label>
            <select id="build_id" name="build_id" class="form-control">
              <option value="" disabled selected>Select Building</option>
              @foreach($building as $buildings)
              <option value="{{$buildings->id}}">{{$buildings->Building_name}}</option>
              @endforeach
            </select>
            <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
            <span class="text-danger">
              <strong id="build_id-error"></strong>
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
    $('.edit_btn').on('click', function() {
      $('#dept_EditModal').modal('show');

      $tr = $(this).closest('tr');

      var data = $tr.children("td").map(function() {
        return $(this).text();
      }).get();

      console.log(data);

      $('#dept_edit_id').val(data[0]);
      $('#dept_no').val(data[0]);
      $('#dept_edit_name').val(data[1]);
    });
  });
</script>

<script type="text/javascript">
  $().ready(function() {
    $('#dept_EditForm').on('submit', function(e) {

      e.preventDefault();
      var id = $("#dept_edit_id").val();
      //$('#logout-form').submit() // this submits the form 
      $.ajax({
        type: "PATCH",
        url: "department/update/" + id,
        data: $('#dept_EditForm').serialize(),
        success: function(response) {
          console.log(response);
          if (response.errors) {
            if (response.errors.dept_no) {
              $('#dept_no-error').html(response.errors.dept_no[0]);
            }
            if (response.errors.dept_edit_name) {
              $('#dept_edit_name-error').html(response.errors.dept_edit_name[0]);
            }
            if (response.errors.build_id) {
              $('#build_id-error').html(response.errors.build_id[0]);
            }
          }
          if (response.success) {

            $('#dept_EditModal').modal('hide');
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
        }
      });
    });
  });
</script>