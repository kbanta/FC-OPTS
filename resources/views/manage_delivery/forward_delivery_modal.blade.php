<!-- Modal-->

<div id="forwardDelivery" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
  <div role="document" class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h5 id="exampleModalLabel" class="modal-title">Forward Deliveries!</h5>
        <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true">×</span></button>
      </div>
      <form id="forwardDeliveryForm">
        {{csrf_field()}}
        {{method_field('POST')}}
        <div class="modal-body">
          <input type="hidden" name="forward_no" id="forward_no" class="forward_no" value="{{$generateforwardno}}">
          <i>
            <h2>{{$generateforwardno}}</h2>
          </i>
          <p>for {{ $order_info[0]['order_no'] }}</p>
          <!-- <span class="badge badge-success" style="font-size:20px;">Item Details</span> -->
          <div class="row">
            <div class="table">
              @foreach(array_keys($dl_noo) as $key => $tf)
              <table class="table table-bordered table-sm">
                <thead>
                  <tr>
                    <th style="display:none ;">Staff_id</th>
                    <th>Description</th>
                    <th>Brand</th>
                    <th>Unit</th>
                    <th>Quantity</th>
                  </tr>
                </thead>
                <tbody>
                  @php
                  $total=0;
                  @endphp
                  @foreach($dl_noo[$tf] as $outputs)
                  <tr>
                    @if(empty($outputs['item_quantity']))
                    @else
                    <td style="display:none ;">{{$outputs['id']}}</td>
                    <td style="display:none ;"><input type="hidden" id="dln" name="dln" value="{{$outputs['delivery_no']}}"></td>
                    <td style="display:none ;"><input type="hidden" id="pr_no" name="pr_no" value="{{$outputs['pr_no']}}"></td>
                    <td class=""><input type="hidden" id="item_desc[]" name="item_desc[]" value="{{$outputs['item_desc']}}"> {{$outputs['item_desc']}}</td>
                    <td class=""><input type="hidden" id="item_brand[]" name="item_brand[]" value="{{$outputs['item_brand']}}">{{$outputs['item_brand']}}</td>
                    <td class=""><input type="hidden" id="item_unit[]" name="item_unit[]" value="{{$outputs['item_unit']}}">{{$outputs['item_unit']}}</td>
                    <td class=""><input type="hidden" id="item_quantity[]" name="item_quantity[]" value="{{$outputs['item_quantity']}}">{{$outputs['item_quantity']}}</td>
                    @endif
                  </tr>
                  @endforeach
                  <input type="hidden" id="dln_pg[]" name="dln_pg[]" value="{{$outputs['delivery_no']}}">
                  @endforeach
                  <tr>
                    <td colspan="7" class="text-center" style="color: yellow;">
                      *****Nothing follows*****
                    </td>
                  </tr>
                </tbody>

              </table>
              <br>

            </div>
          </div>
          <div class="modal-footer">
            <!-- <button type="button" class="btn btn-primary save_po">Save</button> -->
            <input type="button" class="btn btn-primary forward_delivery_no" value="Save" id="submit-btn">

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
    $('.forward_delivery_no').on('click', function(e) {
      e.preventDefault();
      Swal.fire({
        title: 'Send for Approval?',
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
          dln = $("input[name=dln]").val();
          supplier = new Array();
          $('input[name="supplier[]"]').each(function() {
            supplier.push($(this).val());
          });
          // alert(dln);
          $.ajax({
            type: "POST",
            url: sid + "/forward_delivery",
            data: $('#forwardDeliveryForm').serialize(),
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
</script>