<!-- Modal-->

<div id="addSupplier" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
  <div role="document" class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 id="exampleModalLabel" class="modal-title">Add Supplier</h5>
        <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true">×</span></button>
      </div>
      <form id="addSupplierForm">
        {{csrf_field()}}
        <div class="modal-body">
          <table class="table table-bordered table-sm" id="myTablee">
            <tr>
              <th class="table2" style="width: 15%">
                <p>Business Name</p>
                <span class='glyphicon glyphicon-envelope form-control-feedback'></span>
                <span class="text-danger">
                  <strong id="business_name-error"></strong>
                </span>
              </th>
              <th class="table2" style="width: 15%">
                <p>Contact Person</p>
                <span class='glyphicon glyphicon-envelope form-control-feedback'></span>
                <span class="text-danger">
                  <strong id="contact_person-error"></strong>
                </span>
              </th>
              <th class="table2" style="width: 15%">
                <p>Contact Number</p>
                <span class='glyphicon glyphicon-envelope form-control-feedback'></span>
                <span class="text-danger">
                  <strong id="contact_no-error"></strong>
                </span>
              </th>
              <th class="table2" style="width: 15%">
                <p>Email</p>
                <span class='glyphicon glyphicon-envelope form-control-feedback'></span>
                <span class="text-danger">
                  <strong id="email-error"></strong>
                </span>
              </th>
              <th class="table2" style="width: 15%">
                <p>Business Address</p>
                <span class='glyphicon glyphicon-envelope form-control-feedback'></span>
                <span class="text-danger">
                  <strong id="business_add-error"></strong>
                </span>
              </th>
              <th class="action_buttons" style="width:10%">
                <button type='button' class="btn btn-success btn-block btn-sm" onclick='xx()'>
                  <i class="fas fa-plus-square">Add Supplier</i>
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
    var table = document.getElementById("myTablee").getElementsByTagName('tbody')[0];
    var row = table.insertRow();

    function addoption() {
      $('#addoption').append('<option value="${taskArray}">${taskArrayy}</option>');
    }

    let cell1 = row.insertCell(0);
    let cell2 = row.insertCell(1);
    let cell3 = row.insertCell(2);
    let cell4 = row.insertCell(3);
    let cell5 = row.insertCell(4);
    let cell6 = row.insertCell(5);

    cell1.innerHTML = "<p><input class='form-control request_table' type='text' name='business_name[]' autocomplete='off' ></p>";
    cell2.innerHTML = "<p><input class='form-control request_table' type='text' name='contact_person[]' autocomplete='off' ></p>";
    cell3.innerHTML = "<p><input class='form-control request_table' type='tel' name='contact_no[]' autocomplete='off' ></p>";
    cell4.innerHTML = "<p><input class='form-control request_table' type='email' name='email[]' autocomplete='off' ></p>";
    cell5.innerHTML = "<p><input class='form-control request_table' type='text' name='business_add[]' autocomplete='off' ></p>";
    cell6.innerHTML = "<button type='button' class='btn btn-danger btn-block btn-sm' onclick='yy()'><i class='fa fa-trash'></i>Remove</button>";
  }

  const yy = () => {
    var td = event.target.parentNode;
    var tr = td.parentNode;
    tr.parentNode.removeChild(tr);
  }
</script>
<script type="text/javascript">
  $().ready(function() {
    $('#addSupplierForm').on('submit', function(e) {

      e.preventDefault();
      $.ajax({
        type: "POST",
        url: "suppliersave/",
        data: $('#addSupplierForm').serialize(),
        success: function(data) {
          console.log(data);
          if (data.errors) {
            if (data.errors.business_name) {
              $('#business_name-error').html(data.errors.business_name[0]);
              
            }
            if (data.errors.contact_person) {
              $('#contact_person-error').html(data.errors.contact_person[0]);
            }
            if (data.errors.contact_no) {
              $('#contact_no-error').html(data.errors.contact_no[0]);
            }
            if (data.errors.email) {
              $('#email-error').html(data.errors.email[0]);
            }
            if (data.errors.business_add) {
              $('#business_add-error').html(data.errors.business_add[0]);
            }
          }
          if (data.success) {
            $('#addSupplier').modal('hide');
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
        error: function(xhr, status, error) {
          console.log(xhr.responseJSON.message);
          Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: 'Field is Required',
          })
        }

      });
    });
  });
</script>