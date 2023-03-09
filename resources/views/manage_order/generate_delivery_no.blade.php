<div id="generateDeliveryNo" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
  <div role="document" class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 id="exampleModalLabel" class="modal-title">Delivery No.</h5>
        <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true">×</span></button>
      </div>
      @foreach(array_keys($gg) as $key => $outputs)
      <form id="generateDeliveryNoForm">
        {{csrf_field()}}
        {{method_field('PUT')}}
        <div class="modal-body">
          <center>
            <i>
              <input class="form form-control text-center" type="" name="delivery_no" id="delivery_no" class="delivery_no" autocomplete="off">
            </i>
            <p>for {{ $order_info[0]['order_no'] }}</p>
            <input type="hidden" name="po_no" value="{{ $order_info[0]['po_no'] }}">
            <input type="hidden" name="order_no" value="{{ $order_info[0]['order_no'] }}">
            <input type="hidden" name="sid" value="{{ $order_info[0]['supplier_id'] }}">
          </center>
          <br>
          <div>
            <!-- <span class="badge badge-success" style="font-size: 15px;">Deliver Item</span> -->
            <table class="table table-bordered table-sm">
              <thead>
                <tr>
                  <!-- <th></th> -->
                  <th>Item Description</th>
                  <th>Brand</th>
                  <th>Unit</th>
                  <th width="10%">Quantity</th>
                  <th width="20%">Deliver</th>
                  <th width="15%">To Deliver</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  @foreach($gg[$outputs] as $supplier)
                  @if($supplier['delivered'] != $supplier['quantity'])
                  <td class="text-center" style="display: none;">
                    <input type="hidden" class="custom-control custom-checkbox check" id="iid" name="iid[]" value="{{ $supplier['id'] }}"></p>
                    <input type="hidden" class="custom-control custom-checkbox check" id="supplier" name="supplier[]" value="{{ $supplier['supplier_id'] }}"></p>
                  </td>
                  <td>
                    {{ $supplier['item_desc'] }}
                    <input type="hidden" class="form-control" id="item_desc[]" name="item_desc[]" value="{{ $supplier['item_desc'] }}"></p>
                  </td>
                  <td>
                    {{ $supplier['brand'] }}
                    <input type="hidden" class="form-control" id="brand[]" name="brand[]" value="{{ $supplier['brand'] }}"></p>
                  </td>
                  <td>
                    {{ $supplier['unit'] }}
                    <input type="hidden" class="form-control" id="unit[]" name="unit[]" value="{{ $supplier['unit'] }}"></p>
                  </td>
                  <td>
                    {{ $supplier['quantity'] }}
                    <input type="hidden" min="0" max="{{ $supplier['quantity'] }}" class="form-control text-center"></p>
                  </td>
                  <input type="hidden" name="max_quan[]" value="{{ $supplier['quantity']-$supplier['delivered'] }}" class="form-control text-center"></p>
                  <td>
                    <!-- <input type="hidden" class="form-control" id="delivered[]" name="delivered[]" value="{{ $supplier['delivered'] }}"></p> -->
                    <input type="text" min="0" max="{{ $supplier['quantity']-$supplier['delivered'] }}" class="form-control text-center" id="quantity[]" name="quantity[]" required></p>
                  </td>
                  <td>
                    {{ $supplier['quantity']-$supplier['delivered'] }}
                  </td>
                  @else
                  @endif
                </tr>
                @endforeach
                <tr>
                  <td colspan="6">
                    <p> Supplier Name : <b> {{ $supplier['business_name'] }} </b></p>
                    <input type="hidden" class="form-control" id="suppliername[]" name="suppliername[]" value="{{ $supplier['business_name'] }}"></p>
                  </td>
                </tr>
              </tbody>
            </table>
            @endforeach
          </div>
          <div class="modal-footer">
            <!-- <button type="submit" class="btn btn-primary">Save</button> -->
            <input type="button" class="btn btn-primary save_delivery_no" value="Save" id="submit-btn">

          </div>

        </div>
      </form>
    </div>
  </div>
</div>
<script type="text/javascript">
  $(function() {
    $("input[name='quantity[]']").on('input', function(e) {
      $(this).val($(this).val().replace(/[^0-9]/g, ''));
    });
  })
  $().ready(function() {
    $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    });
    $('.save_delivery_no').on('click', function(e) {
      e.preventDefault();
      Swal.fire({
        title: 'Save Delivery Number?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, send it!'
      }).then((result) => {
        if (result.isConfirmed) {
          document.getElementById("submit-btn").disabled = true;
          po_no = $("input[name=po_no]").val();
          dln = $("input[name=delivery_no]").val();
          pr_no = $("input[name=pr_no]").val();
          sid = $("input[name=sid]").val();
          order_no = $("input[name=order_no]").val();
          $.ajax({
            type: "PUT",
            url: sid + "/save_delivery_no/" + po_no,
            data: $('#generateDeliveryNoForm').serialize(),
            success: function(data) {
              console.log(data);
              if (data.errors) {
                if (data.errors.item_id) {
                  $('#item_id-error').html(data.errors.item_id[0]);
                }
              }
              if (data.success) {
                $('#addSupplierItem').modal('hide');
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
                })
                //   location.href =
                //     "http://127.0.0.1:8000/processor/deliveries";
                // }, 3000);
              }
            },
            error: function(xhr, status, error) {
              console.log(xhr.responseJSON.message);
              Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Input quantity is greaterthan max value',
              })
            }
          });
        }
      });
    });

  });
  $(document).ready(function() {
    $('.quantity').attr('disabled', true);

    $('.check').change(function() {
      //find only the quantity in the same row as the selected checkbox
      $(this).closest('tr').find('.quantity').attr('disabled', !this.checked).focus();
    });
  });
</script>