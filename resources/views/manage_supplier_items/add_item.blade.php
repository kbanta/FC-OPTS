<!-- Modal-->

<div id="addItem" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
  <div role="document" class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 id="exampleModalLabel" class="modal-title">Add Item</h5>
        <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true">×</span></button>
      </div>
      <form id="addItemForm">
        {{csrf_field()}}
        <div class="modal-body">
          <!-- <div class="form-group">
            <label>Item Description</label>
            <input type="text" name="item_desc" class="form-control item_desc" placeholder="Item Description">
            <span class="glyphicon glyphicon-user form-control-feedback"></span>
            <span class="text-danger">
              <strong id="item_desc-error"></strong>
            </span>

          </div> -->
          <table class="table table-bordered table-sm" id="myTable">
            <tr>
              <th class="table2" style="width: 15%">
                <p>Item Description</p>
                <span class='glyphicon glyphicon-envelope form-control-feedback'></span>
              </th>
              <th class="action_buttons" style="width:3%">
                <button type='button' class="btn btn-success btn-block btn-sm" onclick='x()'>
                  <i class="fas fa-plus-square">Add Item</i>
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

    cell1.innerHTML = "<p><input class='form-control request_table' type='text' id='item_desc' name='item_desc[]' autocomplete='off' ></p>";
    cell2.innerHTML = "<button type='button' class='btn btn-danger btn-block btn-sm' onclick='y()'><i class='fa fa-trash'></i>Remove</button>";
  }

  const y = () => {
    var td = event.target.parentNode;
    var tr = td.parentNode;
    tr.parentNode.removeChild(tr);
  }
</script>
<script type="text/javascript">
  $().ready(function() {
    $('#addItemForm').on('submit', function(e) {
      e.preventDefault();
      $.ajax({
        type: "POST",
        url: "itemsave/",
        data: $('#addItemForm').serialize(),
        success: function(data) {
          console.log(data);
          if (data.errors) {
            if (data.errors.item_desc) {
              Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Item Desc has already been taken.',
              })
            }
            if (data.errors.brand) {
              $('#brand-error').html(data.errors.brand[0]);
            }
            if (data.errors.unit) {
              $('#unit-error').html(data.errors.unit[0]);
            }
            if (data.errors.price) {
              $('#price-error').html(data.errors.price[0]);
            }
            if (data.errors.supplier_id) {
              $('#supplier_id-error').html(data.errors.supplier_id[0]);
            }
          }
          if (data.success) {
            $('#addItem').modal('hide');
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
            text: 'Item Desc Required',
          })
        }
      });
    });
  });
</script>
<style>
  .item_desc {
    text-transform: capitalize;
  }
</style>