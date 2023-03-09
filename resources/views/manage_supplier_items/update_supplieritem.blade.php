<!-- Modal-->

<div id="updateSupplierItemModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
    <div role="document" class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 id="exampleModalLabel" class="modal-title">Update Item</h5>
                <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true">×</span></button>
            </div>
            <form id="updateSupplierItemForm">
                {{csrf_field()}}
                {{method_field('PUT')}}
                <div class="modal-body">
                    <div class="form-group">
                        <input type="hidden" id="build_edit_id" name="build_edit_id" class="form-control" />
                    </div>
                    <div class="form-group">
                        <label>Item Description</label>
                        <input type="text" name="item_id" id="item_id" class="form-control" readonly>
                        <!-- <select name="item_id" class="form-control">
                            <option value="" item_id disabled>Select Item</option>
                            @foreach($item as $items)
                            <option value="{{$items->id}}">{{$items->item_desc}}</option>
                            @endforeach
                        </select> -->
                        <span class="glyphicon glyphicon-user form-control-feedback"></span>
                        <span class="text-danger">
                            <strong id="item_id-error"></strong>
                        </span>

                    </div>
                    <div class="form-group">
                        <label>Brand</label>
                        <input type="text" name="brand" id="brand" class="form-control" placeholder="Brand">
                        <span class="glyphicon glyphicon-user form-control-feedback"></span>
                        <span class="text-danger">
                            <strong id="brand-error"></strong>
                        </span>

                    </div>
                    <div class="form-group">
                        <label>Unit</label>
                        <input type="text" name="unit" id="unit" class="form-control" placeholder="Unit">
                        <span class="glyphicon glyphicon-user form-control-feedback"></span>
                        <span class="text-danger">
                            <strong id="unit-error"></strong>
                        </span>
                    </div>
                    <div class="form-group">
                        <label>Price</label>
                        <input type="text" name="offered_price" id="offered_price" class="form-control" placeholder="Price">
                        <span class="glyphicon glyphicon-user form-control-feedback"></span>
                        <span class="text-danger">
                            <strong id="price-error"></strong>
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
                    </div>

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
    $().ready(function() {
        $('.supplieritem_edit_btn').on('click', function() {
            $('#updateSupplierItemModal').modal('show');

            $tr = $(this).closest('tr');

            var data = $tr.children("td").map(function() {
                return $(this).text();
            }).get();

            console.log(data);

            $('#build_edit_id').val(data[0]);
            $('#item_id').val(data[1]);
            $('#brand').val(data[2]);
            $('#unit').val(data[3]);
            $('#offered_price').val(data[4]);
            $('#supplier_id').val(data[5]);
        });
    });
</script>
<script type="text/javascript">
    $().ready(function() {
        $('#updateSupplierItemForm').on('submit', function(e) {
            e.preventDefault();
            var id = $("#build_edit_id").val();
            Swal.fire({
                title: 'Update Item?',
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
                        url: "supplieritem/update/" + id,
                        data: $('#updateSupplierItemForm').serialize(),
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
                                if (data.errors.price) {
                                    $('#price-error').html(data.errors.price[0]);
                                }
                                if (data.errors.supplier_id) {
                                    $('#supplier_id-error').html(data.errors.supplier_id[0]);
                                }
                            }
                            if (data.success) {
                                $('#updateSupplierItem').modal('hide');
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
                }
            });
        });

    });
</script>