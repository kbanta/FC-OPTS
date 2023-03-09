
<!-- Modal-->

<div  id="update_ItemModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
  <div role="document" class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 id="exampleModalLabel" class="modal-title">Update Item</h5>
          <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true">×</span></button>
      </div>
      <div class="modal-body">
      <form id="update_ItemForm">
     {{csrf_field()}}
     {{method_field('PUT')}}
     <div class="form-group">
        <input type="hidden" id="dept_edit_id" name="dept_edit_id" class="form-control" />
          </div>
          <div class="form-group">
            <label>Item Desc</label>
              <input type="text" name="up_item_desc" id="up_item_desc" class="form-control">
              <span class="glyphicon glyphicon-user form-control-feedback"></span>
            <span class="text-danger">
              <strong id="up_item_desc-error"></strong>
            </span>
          </div>
          <!-- <div class="form-group">
            <label>Brand</label>
              <input type="text" name="up_brand" id="up_brand" class="form-control">
              <span class="glyphicon glyphicon-user form-control-feedback"></span>
            <span class="text-danger">
              <strong id="up_brand-error"></strong>
            </span>
          </div>
          <div class="form-group">
            <label>Unit</label>
              <input type="text" name="up_unit" id="up_unit" class="form-control">
              <span class="glyphicon glyphicon-user form-control-feedback"></span>
            <span class="text-danger">
              <strong id="up_unit-error"></strong>
            </span>
          </div>
          <div class="form-group">
            <label>Price</label>
              <input type="text" name="up_price" id="up_price" class="form-control">
              <span class="glyphicon glyphicon-user form-control-feedback"></span>
            <span class="text-danger">
              <strong id="up_price-error"></strong>
            </span>
          </div>
          <div class="form-group">
            <label for="up_supplier_id">Update Building:</label>
            <select id="up_supplier_id" name="up_supplier_id" class="form-control">
              <option value="" disabled selected>Select Building</option>
              @foreach($supplier as $suppliers)
              <option value="{{$suppliers->id}}">{{$suppliers->business_name}}</option>
              @endforeach
            </select>
            <span class="glyphicon glyphicon-user form-control-feedback"></span>
            <span class="text-danger">
              <strong id="up_supplier_id-error"></strong>
            </span>
          </div> -->
           <div class="modal-footer">
               <button type="button" data-dismiss="modal" class="btn btn-secondary">Close</button>
               <button type="submit" class="btn btn-primary ">Save changes</button>
           </div>
        </div>
        </form>                  
    </div>
  </div>
</div>

<script type="text/javascript">
    $().ready(function(){
      $('.edit_btn').on('click',function(){
        $('#update_ItemModal').modal('show');

        $tr = $(this).closest('tr');
        
        var data = $tr.children("td").map(function(){
          return $(this).text();
        }).get();

        console.log(data);

        $('#dept_edit_id').val(data[0]);
        $('#up_item_desc').val(data[1]);
        $('#up_brand').val(data[2]);
        $('#up_unit').val(data[3]);
        $('#up_price').val(data[4]);
      });
    });
</script>

<script type="text/javascript">
$().ready(function(){
$('#update_ItemForm').on('submit',function(e){
  
  e.preventDefault();
     var id = $("#dept_edit_id").val();
     
          //$('#logout-form').submit() // this submits the form 
          $.ajax({
          type: "PATCH",
          url: "item/update/"+id,
          data: $('#update_ItemForm').serialize(),
          success:function(response){
            console.log(response);
            if (response.errors) {
            if (response.errors.up_item_desc) {
              $('#up_item_desc-error').html(response.errors.up_item_desc[0]);
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
                title: 'Data Have been updated!',
                showConfirmButton: false,
                timer: 3500
              });
              setTimeout(function() {
                location.reload();
              }, 3000);
          }
          }
        });    
});
}); 
</script>