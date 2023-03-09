<!-- Modal-->

<div id="addGeneratePO" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
  <div role="document" class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 id="exampleModalLabel" class="modal-title">Purchase Order!</h5>
        <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true">×</span></button>
      </div>
      <form id="addGeneratePOForm">
        {{csrf_field()}}
        {{method_field('POST')}}
        <div class="modal-body">
          <input type="hidden" name="po_no" id="po_no" class="po_no" value="{{$generatePO}}">
          <center>
            <i>
              <h2>{{$generatePO}}</h2>
            </i>
            <p>for {{ $output[0]['pr_no'] }}</p>
          </center>
          <div class="modal-footer">
            <!-- <button type="button" class="btn btn-primary save_po">Save</button> -->
            <input type="button" class="btn btn-primary save_po" value="Save" id="submit-btn">

          </div>

        </div>
      </form>
    </div>
  </div>
</div>
<script type="text/javascript">
  $().ready(function() {
    $('.save_po').on('click', function(e) {
      e.preventDefault();
      document.getElementById("submit-btn").disabled = true;
      Swal.fire({
        title: 'Save Purchase Order Number?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, send it!'
      }).then((result) => {
        if (result.isConfirmed) {
          pr_no = jQuery('#pr_no').val(),
            // po_no = jQuery('#po_no').val(),
            po_no = $("input[name=po_no]").val();
          // alert(po_no);
          $.ajax({
            type: "POST",
            url: "save_po/" + pr_no,
            data: {
              name: po_no
            },
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
                  title: 'Purchase Order has been Generated and Prepared!',
                  showConfirmButton: false,
                  timer: 3500
                });
                setTimeout(function() {
                  // location.reload();
                  location.href =
                    "http://127.0.0.1:8000/processor/pr_to_po";
                }, 3000);
              }
            },
          });
        }
      });
    });

  });
</script>