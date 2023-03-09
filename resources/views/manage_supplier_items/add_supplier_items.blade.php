<!-- Modal-->

<div id="addSupplierItem" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
    <div role="document" class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 id="exampleModalLabel" class="modal-title">Add Supplier Item</h5>
                <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true">×</span></button>
            </div>
            <form id="addSupplierItemForm">
                {{csrf_field()}}
                <div class="modal-body">
                    <!-- <div class="form-group">
                        <label>Item Description</label>
                        <select name="item_id" class="form-control">
                            <option value="" disabled selected>Select Item</option>
                            @foreach($item as $items)
                            <option value="{{$items->id}}">{{$items->item_desc}}</option>
                            @endforeach
                        </select>
                        <span class="glyphicon glyphicon-user form-control-feedback"></span>
                        <span class="text-danger">
                            <strong id="item_id-error"></strong>
                        </span>

                    </div>
                    <div class="form-group">
                        <label>Brand</label>
                        <input type="text" name="brand" class="form-control" placeholder="Brand">
                        <span class="glyphicon glyphicon-user form-control-feedback"></span>
                        <span class="text-danger">
                            <strong id="brand-error"></strong>
                        </span>

                    </div>
                    <div class="form-group">
                        <label>Unit</label>
                        <input type="text" name="unit" class="form-control" placeholder="Unit">
                        <span class="glyphicon glyphicon-user form-control-feedback"></span>
                        <span class="text-danger">
                            <strong id="unit-error"></strong>
                        </span>
                    </div>
                    <div class="form-group">
                        <label>Price</label>
                        <input type="number" name="offered_price" class="form-control" placeholder="Price" step="any">
                        <span class="glyphicon glyphicon-user form-control-feedback"></span>
                        <span class="text-danger">
                            <strong id="offered_price-error"></strong>
                        </span>
                    </div>
                    <div class="form-group">
                        <label for="supplier_id">Supplier Name</label>
                        <select name="supplier_id" class="form-control">
                            <option value="" disabled selected>Select Supplier</option>
                            @foreach($supplier as $suppliers)
                            <option value="{{$suppliers->id}}">{{$suppliers->business_name}}</option>
                            @endforeach
                        </select>
                        <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
                        <span class="text-danger">
                            <strong id="supplier_id-error"></strong>
                        </span>
                    </div> -->
                    <table class="table table-bordered table-sm" id="myTableee">
                        <tr>
                            <th class="table2" style="width: 15%">
                                <p>Item Description</p>
                                <span class='glyphicon glyphicon-envelope form-control-feedback'></span>
                                <span class="text-danger">
                                    <strong id="item_id-error"></strong>
                                </span>
                            </th>
                            <th class="table2" style="width: 15%">
                                <p>Brand</p>
                                <span class='glyphicon glyphicon-envelope form-control-feedback'></span>
                                <span class="text-danger">
                                    <strong id="brand-error"></strong>
                                </span>
                            </th>
                            <th class="table2" style="width: 15%">
                                <p>Unit</p>
                                <span class='glyphicon glyphicon-envelope form-control-feedback'></span>
                                <span class="text-danger">
                                    <strong id="unit-error"></strong>
                                </span>
                            </th>
                            <th class="table2" style="width: 15%">
                                <p>Price</p>
                                <span class='glyphicon glyphicon-envelope form-control-feedback'></span>
                                <span class="text-danger">
                                    <strong id="offered_price-error"></strong>
                                </span>
                            </th>
                            <th class="table2" style="width: 15%">
                                <p>Supplier Name</p>
                                <span class='glyphicon glyphicon-envelope form-control-feedback'></span>
                                <span class="text-danger">
                                    <strong id="supplier_id-error"></strong>
                                </span>
                            </th>
                            <th class="action_buttons" style="width:10%">
                                <button type='button' class="btn btn-success btn-block btn-sm" onclick='xxx()'>
                                    <i class="fas fa-plus-square">Add Supplier Item</i>
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
  const xxx = () => {
    var table = document.getElementById("myTableee").getElementsByTagName('tbody')[0];
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

    cell1.innerHTML = "<p><select name='item_id[]' class='form-control'><option value='' disabled selected>Select Item</option><datalist id='item'>@foreach($item as $items)<option value='{{$items->id}}'>{{$items->item_desc}}</option>@endforeach</datalist></select></p>";
    cell2.innerHTML = "<p><input class='form-control request_table' type='text' name='brand[]' autocomplete='off' ></p>";
    cell3.innerHTML = "<p><input class='form-control request_table' type='tel' name='unit[]' autocomplete='off' ></p>";
    cell4.innerHTML = "<p><input class='form-control request_table'  type='number' min='0.00' max='10000.00' step='0.01' name='offered_price[]' autocomplete='off' ></p>";
    cell5.innerHTML = "<p><select name='supplier_id[]' class='form-control'><option value='' disabled selected>Select Supplier</option><datalist id='item'>@foreach($supplier as $suppliers)<option value='{{$suppliers->id}}'>{{$suppliers->business_name}}</option>@endforeach></datalist></select></p>";
    cell6.innerHTML = "<button type='button' class='btn btn-danger btn-block btn-sm' onclick='yyy()'><i class='fa fa-trash'></i>Remove</button>";
  }

  const yyy = () => {
    var td = event.target.parentNode;
    var tr = td.parentNode;
    tr.parentNode.removeChild(tr);
  }
</script>

<script type="text/javascript">
    $().ready(function() {
        $('#addSupplierItemForm').on('submit', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Save Item?',
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
                        url: "supplieritemsave/",
                        data: $('#addSupplierItemForm').serialize(),
                        success: function(data) {
                            console.log(data);
                            if (data.errors) {
                                if (data.errors.item_id) {
                                    $('#item_id-error').html(data.errors.item_id[0]);
                                }
                                if (data.errors.brand) {
                                    $('#brand-error').html(data.errors.brand[0]);
                                }
                                if (data.errors.unit) {
                                    $('#unit-error').html(data.errors.unit[0]);
                                }
                                if (data.errors.offered_price) {
                                    $('#offered_price-error').html(data.errors.offered_price[0]);
                                }
                                if (data.errors.supplier_id) {
                                    $('#supplier_id-error').html(data.errors.supplier_id[0]);
                                }
                            }
                            if (data.success) {
                                $('#addSupplierItem').modal('hide');
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
                }
            });
        });

    });
</script>