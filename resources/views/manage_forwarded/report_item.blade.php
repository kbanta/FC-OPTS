<!-- Modal-->

<div id="update_ItemModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
  <div role="document" class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 id="exampleModalLabel" class="modal-title">Report Item</h5>
        <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true">×</span></button>
      </div>
      <div class="modal-body">
        <form id="report_itemForm">
          {{csrf_field()}}
          {{method_field('POST')}}
          <div class="form-group">
            <input type="hidden" id="FID" name="FID" class="form-control" />
            @if(!empty($forwarded[0]['delivery_no']))
            <input type="hidden" id="dln" name="dln" value="{{$forwarded[0]['delivery_no']}}" class="form-control" />
            @endif
            <input type="hidden" id="rn" name="rn" value="{{$generatedReportNo}}" class="form-control" />
            @if(!empty($forwarded[0]['staff_id']))
            <input type="hidden" id="staff_id" name="staff_id" value="{{$forwarded[0]['staff_id']}}" class="form-control" />
            @endif
            <span class="glyphicon glyphicon-user form-control-feedback"></span>
            <span class="text-danger">
              <strong id="FID-error"></strong>
            </span>
          </div>
          <table class="table table-bordered table-sm">
            <thead>
              <tr>
                <th class="text-center">Select</th>
                <th class="text-center" width="15%">Quantity</th>
                <th class="text-center">Particulars</th>
                <th class="text-center">Item Status</th>
              </tr>
            </thead>
            <tbody>
              @foreach($forwarded as $forward)
              <tr>
                <td>
                  <center><input type="checkbox" class="custom-control custom-checkbox check" name="report_chk[]" value="{{$forward['id']}}" /></center>
                </td>
                <td class="text-center" style="display: none;">{{$forward['id']}}</td>
                <td><input class="form-control text-center quantity" name="report_quantity[]" type="number" min="1" max="{{$forward['item_quantity']}}"></td>
                <td class="text-center">{{$forward['item_desc']}} {{$forward['item_brand']}}</td>
                <td><textarea class="form-control rounded-0 text-center rm" id="report_message" name="report_message[]" rows="3" style="overflow:auto;resize:none" required="required"></textarea></td>
                @endforeach
              </tr>
            </tbody>
          </table>
          <div class="modal-footer">
            <button type="submit" class="btn btn-primary">Report</button>
          </div>
      </div>
      </form>
    </div>
  </div>
</div>

<script type="text/javascript">
  $().ready(function() {
    $('.report_btn').on('click', function() {
      $('#update_ItemModal').modal('show');

      $tr = $(this).closest('tr');

      var data = $tr.children("td").map(function() {
        return $(this).text();
      }).get();

      console.log(data);

      $('#www').val(data[0]);
      $('#FID').val(data[1]);
      $('#quantity').val(data[2]);
      $('#desc').val(data[3]);
      $('#up_price').val(data[4]);
    });
  });
  $(document).ready(function() {
    $('.quantity').attr('disabled', true);
    $('.rm').attr('disabled', true);


    $('.check').change(function() {
      //find only the quantity in the same row as the selected checkbox
      $(this).closest('tr').find('.quantity').attr('disabled', !this.checked).focus();
    });
    $('.check').change(function() {
      //find only the quantity in the same row as the selected checkbox
      $(this).closest('tr').find('.rm').attr('disabled', !this.checked).focus();
    });
  });
</script>

<script type="text/javascript">
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });
  $().ready(function() {
    $('#report_itemForm').on('submit', function(e) {

      e.preventDefault();
      var id = $("#dept_edit_id").val();
      var staff_id = $("#staff_id").val();
      Swal.fire({
        title: 'Report Item?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, send it!'
      }).then((result) => {
        if (result.isConfirmed) {
          document.getElementById("hidebutton").disabled = true;
          $.ajax({
            type: "POST",
            url: staff_id + "/report_item",
            data: $('#report_itemForm').serialize(),
            success: function(response) {
              console.log(response);
              if (response.errors) {
                if (response.errors.FID) {
                  $('#FID-error').html(response.errors.FID[0]);
                }
                if (response.errors.up_brand) {
                  $('#up_brand-error').html(response.errors.up_brand[0]);
                }
                if (response.errors.up_unit) {
                  $('#up_unit-error').html(response.errors.up_unit[0]);
                }
                if (response.errors.up_price) {
                  $('#up_price-error').html(response.errors.up_price[0]);
                }
                if (response.errors.up_supplier_id) {
                  $('#up_supplier_id-error').html(response.errors.up_supplier_id[0]);
                }
              }
              if (response.success) {
                $('#update_ItemModal').modal('hide');
                //alert("data updated");
                Swal.fire({
                  icon: 'success',
                  title: 'Item have been Reported!',
                  showConfirmButton: false,
                  timer: 3500
                });
                setTimeout(function() {
                  location.reload();
                  // location.href =
                  // "http://127.0.0.1:8000/approver/to_transmit";
                }, 3000);
              }
            },
          });
        }
      });
    });

  });
</script>