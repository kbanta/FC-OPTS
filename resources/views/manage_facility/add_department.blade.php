<!-- Modal-->

<div id="addDepartment" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
  <div role="document" class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 id="exampleModalLabel" class="modal-title">Add Department</h5>
        <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true">×</span></button>
      </div>
      <form id="addDepartmentForm">
        {{csrf_field()}}
        <div class="modal-body">
          <!-- <div class="form-group">
            <label>Department No.</label>
            <input type="text" name="id" class="form-control" placeholder="Department Room#">
            <span class="glyphicon glyphicon-user form-control-feedback"></span>
            <span class="text-danger">
              <strong id="id-error"></strong>
            </span>

          </div>
          <div class="form-group">
            <label>Department</label>
            <input type="text" name="dept_name" class="form-control" placeholder="Department Name">
            <span class="glyphicon glyphicon-user form-control-feedback"></span>
            <span class="text-danger">
              <strong id="dept_name-error"></strong>
            </span>
          </div>

          <div class="form-group">
            <label for="">Building Name</label>
            <select name="build_id" class="form-control">
              <option value="" disabled selected>Select Building</option>
              @foreach($building as $buildings)
              <option value="{{$buildings->id}}">{{$buildings->Building_name}}</option>
              @endforeach
            </select>
          </div> -->

          <table class="table table-bordered table-sm" id="myTable">
            <tr>
              <th class="table2" style="width: 15%">
                <p>Room No.</p>
                <span class='glyphicon glyphicon-envelope form-control-feedback'></span>
                <span class="text-danger">
                  <strong id="id-error"></strong>
                </span>
              </th>
              <th class="table2" style="width: 15%">
                <p>Department</p>
                <span class='glyphicon glyphicon-envelope form-control-feedback'></span>
                <span class="text-danger">
                  <strong id="dept_name-error"></strong>
                </span>
              </th>
              <th class="table2" style="width: 15%">
                <p>Building Name</p>
                <span class='glyphicon glyphicon-envelope form-control-feedback'></span>
                <span class="text-danger">
                  <strong id="dept_name-error"></strong>
                </span>
              </th>
              <th class="action_buttons" style="width:10%">
                <button type='button' class="btn btn-success btn-block btn-sm" onclick='x()'>
                  <i class="fas fa-plus-square">Add Department</i>
                </button>
              </th>
            </tr>
            <tbody>
            </tbody>
          </table>

          <div class="modal-footer">
            <button type="button" data-dismiss="modal" class="btn btn-secondary">Close</button>
            <button type="submit" class="btn btn-primary">Save changes</button>
          </div>

        </div>
      </form>
    </div>
  </div>
</div>

<script type="text/javascript">
  // alert(data);
  const x = () => {
    var table = document.getElementById("myTable").getElementsByTagName('tbody')[0];
    var row = table.insertRow();

    function addoption() {
      $('#addoption').append('<option value="${taskArray}">${taskArrayy}</option>');
    }

    let cell1 = row.insertCell(0);
    let cell2 = row.insertCell(1);
    let cell3 = row.insertCell(2);
    let cell4 = row.insertCell(3);

    cell1.innerHTML = "<p><input class='form-control request_table' type='text' name='id[]' autocomplete='off'></p>";
    cell2.innerHTML = "<p><input class='form-control request_table' type='text' name='dept_name[]' autocomplete='off'></p>";
    cell3.innerHTML = "<p><select name='build_id[]' class='form-control' autocomplete='off' required><datalist id='item'> @foreach($building as $buildings)<option value='{{$buildings->id}}'>{{$buildings->Building_name}}</option>@endforeach</datalist></select></p>";
    cell4.innerHTML = "<button type='button' class='btn btn-danger btn-block btn-sm' onclick='y()'><i class='fa fa-trash'></i>Remove</button>";
  }

  const y = () => {
    var td = event.target.parentNode;
    var tr = td.parentNode;
    tr.parentNode.removeChild(tr);
  }
</script>

<script type="text/javascript">
  $().ready(function() {
    $('#addDepartmentForm').on('submit', function(e) {
      e.preventDefault();
      $.ajax({
        type: "POST",
        url: "departmentsave/",
        data: $('#addDepartmentForm').serialize(),
        success: function(data) {
          console.log(data);
          if (data.errors) {
            if (data.errors.id) {
              $('#id-error').html(data.errors.id[0]);
            }
            if (data.errors.dept_name) {
              $('#dept_name-error').html(data.errors.dept_name[0]);
            }
          }
          if (data.success) {
            $('#addDepartment').modal('hide');
            //alert("data updated");
            Swal.fire({
              position: 'top-end',
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
    });
  });
</script>