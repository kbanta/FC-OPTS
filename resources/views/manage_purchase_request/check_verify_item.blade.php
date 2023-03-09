<!-- Modal-->

<div id="CheckVerifyItem" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="false" class="modal fade text-left">
  <div role="document" class="modal-dialog ">
    <div class="modal-content">
      <div class="modal-header">
        <h5 id="exampleModalLabel" class="modal-title">Check Item</h5>
        <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true">×</span></button>
      </div>
      <div class="modal-body">
        <form id="CheckVerifyItemForm">
          {{csrf_field()}}
          {{method_field('PUT')}}
          <div class="form-group">
            <input type="hidden" id="dept_edit_id" name="dept_edit_id" class="form-control" />
            <input type="hidden" id="dept_edit_pr_no" name="dept_edit_pr_no" class="form-control" />
          </div>
          <div class="form-group">
            <label>Item Desc</label>
            <input type="text" name="up_item_desc" id="up_item_desc" class="form-control" readonly>
            <span class="glyphicon glyphicon-user form-control-feedback"></span>
            <span class="text-danger">
              <strong id="up_item_desc-error"></strong>
            </span>
          </div>
          <div class="form-group">
            <label>Check Item</label>
            <select class="form-control" name="item_id" id="item_id">
              <option value="" disabled selected>select item</option>
              @foreach($item_outputs as $item)
              <option value="{{$item['id']}}">{{$item['item_desc']}}</option>
              @endforeach
            </select>
            <span class="glyphicon glyphicon-user form-control-feedback"></span>
            <span class="text-danger">
              <strong id="item_id-error"></strong>
            </span>
            <span class="glyphicon glyphicon-user form-control-feedback"></span>
            <span class="text-danger">
              <strong id="nomatch-error"></strong>
            </span>
          </div>

          <div class="modal-footer">
            @if($output[0]['action'] == 'For Canvassing')
            <input type="button" class="btn btn-success cvi_btn" value="Update">
            @else
            <input type="button" class="btn btn-success cvi_btn" value="Verify">
            @endif
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<div id="DenyItem" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="false" class="modal fade text-left">
  <div role="document" class="modal-dialog ">
    <div class="modal-content">
      <div class="modal-header">
        <h5 id="exampleModalLabel" class="modal-title">Check Item</h5>
        <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true">×</span></button>
      </div>
      <div class="modal-body">
        <form id="CheckVerifyItemForm">
          {{csrf_field()}}
          {{method_field('PUT')}}
          <div class="form-group">
            <input type="hidden" id="dept_edit_id" name="dept_edit_id" class="form-control" />
            <input type="hidden" id="dept_edit_pr_no" name="dept_edit_pr_no" class="form-control" />
          </div>
          <div class="form-group">
            <label>Item Desc</label>
            <input type="text" name="item_desc" id="item_desc" class="form-control" readonly>
          </div>
          <div class="form-group">
            <label>Quantity</label>
            <input type="number" name="quantity" id="quantity" class="form-control" readonly>
          </div>
          <div class="form-group">
            <label>Unit</label>
            <input type="text" name="unit" id="unit" class="form-control" readonly>
          </div>

          <div class="modal-footer">
            @if($output[0]['action'] == 'For Canvassing')
            <input type="button" class="btn btn-success cvi_btn" value="Update">
            @else
            <input type="button" class="btn btn-success cvi_btn" value="Verify">
            @endif
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">
  $().ready(function() {
    $('.update_btn').on('click', function() {
      $('#CheckVerifyItem').modal('show');

      $tr = $(this).closest('tr');

      var data = $tr.children("td").map(function() {
        return $(this).text();
      }).get();
      console.log(data);
      $('#dept_edit_id').val(data[0]);
      $('#dept_edit_pr_no').val(data[1]);
      $('#up_item_desc').val(data[6]);
    });
  });

  $().ready(function() {
    $('.denyitem_btn').on('click', function() {
      // $('#DenyItem').modal('show');

      $tr = $(this).closest('tr');

      var data = $tr.children("td").map(function() {
        return $(this).text();
      }).get();
      console.log(data);
      $('#dept_edit_id').val(data[0]);
      $('#dept_edit_pr_no').val(data[1]);
      $('#unit').val(data[4]);
      $('#quantity').val(data[5]);
      $('#item_desc').val(data[6]);
    });
  })
</script>
<script type="text/javascript">
  $().ready(function() {
    $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    });

    $('.cvi_btn').on('click', function(e) {
      e.preventDefault();
      Swal.fire({
        title: 'verify?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, send it!'
      }).then((result) => {
        if (result.isConfirmed) {
          var id = $("#dept_edit_id").val();
          var pr_no = $("#dept_edit_pr_no").val();
          var req_item = $("#up_item_desc").val();
          // alert(req_item);
          // var item_id = $("input[name=item_id]").val();
          // console.log(item_id);
          $.ajax({
            type: "PATCH",
            url: pr_no + "/checkverifyitem/update/" + id,
            data: {
              id: $("#dept_edit_id").val(),
              item_id: $("#item_id").val(),
              req_item: $("#up_item_desc").val()
            },
            success: function(data) {
              if (data.errors) {
                if (data.errors.item_id) {
                  $('#item_id-error').html(data.errors.item_id[0]);
                }
              }
              if (data.error) {
                $('#nomatch-error').html(data.error);
              }
              if (data.success) {
                //alert("data updated");
                Swal.fire({
                  position: 'center',
                  icon: 'success',
                  title: 'Item has been verified!',
                  showConfirmButton: false,
                  timer: 1500
                })
                setTimeout(function() {
                  location.reload();
                  // location.href =
                  //   "http://127.0.0.1:8000/approver/new_pr";
                }, 1500);
              }
            }
          })
        }

      });
    });
  });
</script>
<script type="text/javascript">
  $().ready(function() {
    $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    });

    $('.denyitem_btn').on('click', function(e) {
      e.preventDefault();

      Swal.fire({
        title: 'Deny item?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, send it!'
      }).then((result) => {
        if (result.isConfirmed) {
          var id = $("#dept_edit_id").val();
          var pr_no = $("#dept_edit_pr_no").val();
          // var item_id = $("input[name=item_id]").val();
          // console.log(item_id);
          $.ajax({
            type: "PATCH",
            url: pr_no + "/denyitem/update/" + id,
            data: {
              id: $("#dept_edit_id").val(),
              item_id: $("#item_id").val()
            },
            success: function(data) {
              if (data.errors) {
                if (data.errors.item_id) {
                  $('#item_id-error').html(data.errors.item_id[0]);
                }
              }
              if (data.success) {
                //alert("data updated");
                Swal.fire({
                  position: 'center',
                  icon: 'success',
                  title: 'Item has been denied!',
                  showConfirmButton: false,
                  timer: 1500
                })
                setTimeout(function() {
                  location.reload();
                  // location.href =
                  //   "http://127.0.0.1:8000/approver/new_pr";
                }, 1500);
              }
            }
          })
        }

      });
    });
  });
</script>