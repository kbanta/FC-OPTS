<!-- Modal-->

<div id="supplierdetails" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
    <div role="document" class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 id="exampleModalLabel" class="modal-title">Supplier Details!</h5>
                <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true">×</span></button>
            </div>
            <form id="">
                {{csrf_field()}}
                {{method_field('POST')}}
                <div class="modal-body">
                    <input type="text" class="form-control" readonly id="business_name">
                    <input type="text" class="form-control" readonly id="email">
                    <input type="text" class="form-control" readonly id="contact_no">
                    <input type="text" class="form-control" readonly id="business_add">
                    <div class="modal-footer">
                        <!-- <button type="button" class="btn btn-primary save_po">Save</button> -->
                        <!-- <input type="button" class="btn btn-primary save_po" value="Save"> -->

                    </div>

                </div>
            </form>
        </div>
    </div>
</div>
<script type="text/javascript">
    $().ready(function() {
        $('.supp').on('click', function() {
            $('#supplierdetails').modal('show');

            $tr = $(this).closest('tr');

            var data = $tr.children("td").map(function() {
                return $(this).text();
            }).get();

            console.log(data);

            $('#business_name').val(data[0]);
            $('#email').val(data[1]);
            $('#contact_no').val(data[2]);
            $('#business_add').val(data[3]);
        });
    });
</script>