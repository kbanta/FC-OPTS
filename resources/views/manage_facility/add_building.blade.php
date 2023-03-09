<!-- Modal-->

<div id="addBuilding" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
  <div role="document" class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 id="exampleModalLabel" class="modal-title">Add Building</h5>
        <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true">×</span></button>
      </div>
      <form id="addBuildingForm">
        {{csrf_field()}}
        <div class="modal-body">
          <!-- <div class="form-group">
            <label>Building Name</label>
            <input type="text" name="build_name" class="form-control" placeholder="Building Name">
            <span class="glyphicon glyphicon-user form-control-feedback"></span>
            <span class="text-danger">
              <strong id="build_name-error"></strong>
            </span>

          </div>
          <div class="form-group">
            <label>Address</label>
            <input type="text" name="address" class="form-control" placeholder="Address">
            <span class="glyphicon glyphicon-user form-control-feedback"></span>
            <span class="text-danger">
              <strong id="address-error"></strong>
            </span>
          </div> -->

          <table class="table table-bordered table-sm" id="Table">
            <tr>
              <th class="table2" style="width: 15%">
                <p>Building Name</p>
                <span class='glyphicon glyphicon-envelope form-control-feedback'></span>
                <span class="text-danger">
                  <strong id="build_name-error"></strong>
                </span>
              </th>
              <th class="table2" style="width: 15%">
                <p>Address</p>
                <span class='glyphicon glyphicon-envelope form-control-feedback'></span>
                <span class="text-danger">
                  <strong id="address-error"></strong>
                </span>
              </th>
              <th class="action_buttons" style="width:10%">
                <button type='button' class="btn btn-success btn-block btn-sm" onclick='xx()'>
                  <i class="fas fa-plus-square">Add Building</i>
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
  const xx = () => {
    var table = document.getElementById("Table").getElementsByTagName('tbody')[0];
    var row = table.insertRow();

    function addoption() {
      $('#addoption').append('<option value="${taskArray}">${taskArrayy}</option>');
    }

    let cell1 = row.insertCell(0);
    let cell2 = row.insertCell(1);
    let cell3 = row.insertCell(2);

    cell1.innerHTML = "<p><input class='form-control request_table' type='text' name='build_name[]' autocomplete='off' required></p>";
    cell2.innerHTML = "<p><input class='form-control request_table' type='text' name='address[]' autocomplete='off' required></p>";
    cell3.innerHTML = "<button type='button' class='btn btn-danger btn-block btn-sm' onclick='yy()'><i class='fa fa-trash'></i>Remove</button>";
  }

  const yy = () => {
    var td = event.target.parentNode;
    var tr = td.parentNode;
    tr.parentNode.removeChild(tr);
  }
</script>

<script type="text/javascript">
  $().ready(function() {
    $('#addBuildingForm').on('submit', function(e) {
      e.preventDefault();
      $.ajax({
        type: "POST",
        url: "buildingsave/",
        data: $('#addBuildingForm').serialize(),
        success: function(data) {
          console.log(data);
          if (data.errors) {
            if (data.errors.build_name) {
              $('#build_name-error').html(data.errors.build_name[0]);
            }
            if (data.errors.address) {
              $('#address-error').html(data.errors.address[0]);
            }
          }
          if (data.success) {
            $('#addBuilding').modal('hide');
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