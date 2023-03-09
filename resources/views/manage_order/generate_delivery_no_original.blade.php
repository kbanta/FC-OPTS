<!-- Modal-->

<div id="generateDeliveryNo" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
  <div role="document" class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h5 id="exampleModalLabel" class="modal-title">Delivery No.</h5>
        <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true">×</span></button>
      </div>
      <form id="forwardallForm">
        {{csrf_field()}}
        {{method_field('POST')}}
        <div class="modal-body">
          <!-- <input type="hidden" name="delivery_no" id="delivery_no" class="delivery_no" value="{{$generatedeliveryno}}"> -->
          <center>
            <i>
              <input class="form form-control text-center" type="" name="delivery_no" id="delivery_no" class="delivery_no">
              <!-- <h2>{{$generatedeliveryno}}</h2> -->
            </i>
            <p>for {{ $output[0]['po_no'] }}</p>

          </center>
          <br>
          <div>
            <!-- <span class="badge badge-success" style="font-size: 15px;">Deliver Item</span> -->
            @foreach(array_keys($poo_output) as $key => $outputs)
            <table class="table table-bordered table-sm">
              <thead>
                <tr>
                  <th></th>
                  <th>Supplier</th>
                  <th>Item Description</th>
                  <th>Brand</th>
                  <th>Unit</th>
                  <th>Quantity</th>
                  <!-- <th>Price</th> -->
                </tr>
              </thead>
              <tbody>
                <tr>
                  @foreach($poo_output[$outputs] as $supplier)
                  <td class="text-center">
                    <input type="checkbox" class="custom-control custom-checkbox check" id="supplier" name="supplier[]" value="{{ $supplier['id'] }}"></p>
                  </td>
                  <td>
                    {{ $supplier['business_name'] }}
                    <input type="hidden" class="form-control" id="suppliername[]" name="suppliername[]" value="{{ $supplier['business_name'] }}" readonly></p>
                  </td>
                  <td>
                    {{ $supplier['item_desc'] }}
                    <input type="hidden" class="form-control" id="item_desc[]" name="item_desc[]" value="{{ $supplier['item_desc'] }}" readonly></p>
                  </td>
                  <td>
                    {{ $supplier['brand'] }}
                    <input type="hidden" class="form-control" id="brand[]" name="brand[]" value="{{ $supplier['brand'] }}" readonly></p>
                  </td>
                  <td>
                    {{ $supplier['unit'] }}
                    <input type="hidden" class="form-control" id="unit[]" name="unit[]" value="{{ $supplier['unit'] }}" readonly></p>
                  </td>
                  <td>
                    <input type="number" min="0" max="{{ $supplier['quantity'] }}" class="form-control text-center quantity" id="quantity[]" name="quantity[]" value=""></p>
                  </td>
                  <!-- <td>
                    <input type="text" class="form-control" id="offered_price[]" name="offered_price[]" value="{{ $supplier['offered_price'] }}" readonly></p>
                  </td> -->
                </tr>
                @endforeach
              </tbody>
            </table>
            @endforeach
          </div>
          <div class="modal-footer">
            <!-- <button type="button" class="btn btn-primary save_po">Save</button> -->
            <input type="button" class="btn btn-primary save_delivery_no" value="Save">

          </div>

        </div>
      </form>
    </div>
  </div>
</div>
<script type="text/javascript">
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
          po_no = $("input[name=po_no]").val();
          dln = $("input[name=delivery_no]").val();
          pr_no = $("input[name=pr_no]").val();
          var supplier = [];
          $('input[id="supplier"]:checked').each(function() {
            supplier.push(this.value);
          });
          // supplier = new Array();
          // $('input[name="supplier[]"]').each(function() {
          //   supplier.push($(this).val());
          // });
          quantity = new Array();
          $('input[name="quantity[]"]').each(function() {
            quantity.push($(this).val());
          });
          unit = new Array();
          $('input[name="unit[]"]').each(function() {
            unit.push($(this).val());
          });
          brand = new Array();
          $('input[name="brand[]"]').each(function() {
            brand.push($(this).val());
          });
          item_desc = new Array();
          $('input[name="item_desc[]"]').each(function() {
            item_desc.push($(this).val());
          });
          // alert(ids);
          $.ajax({
            type: "POST",
            url: "save_delivery_no/" + po_no,
            data: {
              po_no: po_no,
              pr_no: pr_no,
              dln: dln,
              supplier: supplier,
              quantity: quantity,
              unit: unit,
              brand: brand,
              item_desc: item_desc,
            },
            // data: $('#generateDeliveryNoForm').serialize(),
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
                  // location.reload();
                  location.href =
                    "http://127.0.0.1:8000/processor/deliveries";
                }, 3000);
              }
            },
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