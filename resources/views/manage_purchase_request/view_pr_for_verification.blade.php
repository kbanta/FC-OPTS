@extends('manage_purchase_request.view_purchase_requestt')

@section('verification_btn')
<div id="selsuppModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
    <div role="document" class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 id="exampleModalLabel" class="modal-title">Select Supplier and Item</h5>
                <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true">×</span></button>
            </div>
            <div class="modal-body">
                <section class="p-2">
                    <form id="selectSupplierForm">
                        {{csrf_field()}}
                        {{method_field('POST')}}
                        <input type="hidden" id="id" name="id" value="{{$user->id}}">
                        <input type="hidden" id="item_id" name="item_id">
                        <input type="hidden" id="quantity" name="quantity">
                        <input type="hidden" id="ver_item" name="ver_item">
                        <span class="badge badge-danger" style="font-size:15px;">Select 1 supplier per Item</span>
                        <input type="hidden" id="searchInput" placeholder="Search for names, email." title="Type in a name">
                        <table id="userTable" class="table table-bordered table-sm">
                            <thead>
                                <tr class="header">
                                    <th class="text-center">Select</th>
                                    <th class="text-center">Item Description</th>
                                    <th class="text-center">Brand</th>
                                    <th class="text-center">Unit</th>
                                    <th class="text-center">Supplier</th>
                                    <th class="text-center">Price</th>
                                </tr>
                            </thead>
                            </tr>
                            <tbody>
                                @foreach($canvass_output as $canvassed_item)
                                <tr>
                                    <td>
                                        <center><input type="radio" class="custom-control custom-checkbox check" id="checkbox_canvass" name="checkbox_canvass[]" value="{{$canvassed_item['id']}}" /></center>
                                    </td>
                                    <td class="text-center" name="item_desc[]" value="{{$canvassed_item['item_desc']}}">{{$canvassed_item['item_desc']}}</td>
                                    <td class="text-center" name="brand[]" value="{{$canvassed_item['brand']}}">{{$canvassed_item['brand']}}</td>
                                    <td class="text-center" name="unit[]" value="{{$canvassed_item['unit']}}">{{$canvassed_item['unit']}}</td>
                                    <td class="text-center" name="business_name[]" value="{{$canvassed_item['business_name']}}">{{$canvassed_item['business_name']}}</td>
                                    <td class="text-center" name="price[]" value="{{$canvassed_item['offered_price']}}">{{$canvassed_item['offered_price']}}</td>
                                <tr>
                                </tr>
                                @endforeach
                                </tr>
                            </tbody>
                            <tfoot>
                            </tfoot>
                        </table>
                        <div class="form-group">
                            <span class="glyphicon glyphicon-user form-control-feedback"></span>
                            <span class="text-danger">
                                <strong id="nomatch-error"></strong>
                            </span>
                        </div>
                        @if(Auth::user()->hasRole('Approver'))
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-success">Approve</button>
                        </div>
                        @else
                        @endif
                </section>
                </form>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        $().ready(function() {
            $('.sel_supp_btn').on('click', function() {
                $('#selsuppModal').modal('show');

                $tr = $(this).closest('tr');

                var data = $tr.children("td").map(function() {
                    return $(this).text();
                }).get();

                console.log(data);

                $('#item_id').val(data[0]);
                $('#up_item_desc').val(data[1]);
                $('#up_brand').val(data[2]);
                $('#up_unit').val(data[3]);
                $('#up_price').val(data[4]);
                $('#quantity').val(data[5]);
                $('#ver_item').val(data[6]);

                $('#searchInput').val(data[6]);

                var input, filter, table, tr, td, cell, i, j;
                filter = document.getElementById("searchInput").value.toLowerCase();
                table = document.getElementById("userTable");
                tr = table.getElementsByTagName("tr");
                for (i = 1; i < tr.length; i++) {
                    tr[i].style.display = "none";
                    const tdArray = tr[i].getElementsByTagName("td");
                    for (var j = 0; j < tdArray.length; j++) {
                        const cellValue = tdArray[j];
                        if (cellValue && cellValue.innerHTML.toLowerCase().indexOf(filter) > -1) {
                            tr[i].style.display = "";
                            break;
                        }
                    }
                }


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
            $('#selectSupplierForm').on('submit', function(e) {
                if ($('.check').filter(':checked').length < 1) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Select Atleast One!',
                    })
                    return false;
                }
                e.preventDefault();
                Swal.fire({
                    title: 'Select?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, send it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        pr_no = jQuery('#pr_no').val(),
                            $.ajax({
                                type: "PATCH",
                                url: "verify_pr/" + pr_no,
                                data: $('#selectSupplierForm').serialize(),
                                success: function(response) {
                                    if (response.error) {
                                        $('#nomatch-error').html(response.error);
                                    }
                                    if (response.success) {
                                        //alert("data updated");
                                        Swal.fire({
                                            position: 'center',
                                            icon: 'success',
                                            title: 'Supplier and Item has been selected!',
                                            showConfirmButton: false,
                                            timer: 1500
                                        })
                                        setTimeout(function() {
                                            location.reload();
                                            //     location.href =
                                            //         "http://127.0.0.1:8000/approver/pr_for_verification";
                                        }, 1500);
                                    }
                                }
                            })
                    }

                });
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            $('input:checkbox').click(function() {
                $('input:checkbox').not(this).prop('checked', false);
            });
        });
    </script>
    <script>
        function myFunction() {
            var input, filter, table, tr, td, cell, i, j;
            filter = document.getElementById("searchInput").value.toLowerCase();
            table = document.getElementById("userTable");
            tr = table.getElementsByTagName("tr");
            for (i = 1; i < tr.length; i++) {
                tr[i].style.display = "none";
                const tdArray = tr[i].getElementsByTagName("td");
                for (var j = 0; j < tdArray.length; j++) {
                    const cellValue = tdArray[j];
                    if (cellValue && cellValue.innerHTML.toLowerCase().indexOf(filter) > -1) {
                        tr[i].style.display = "";
                        break;
                    }
                }
            }
        }
    </script>

    @endsection

    @section('verify_approved_btn')
    <form id="selectSupplierSubmitForm">
        {{csrf_field()}}
        {{method_field('POST')}}
        <div class="modal-footer">
            <button type="button" class="btn btn-success approved_submit" id="submit-btn">Approve</button>
        </div>
    </form>

    <script type="text/javascript">
        $().ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $('.approved_submit').on('click', function(e) {
                e.preventDefault();
                document.getElementById("submit-btn").disabled = true;
                Swal.fire({
                    title: 'Approve?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, send it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        pr_no = jQuery('#pr_no').val(),
                            $.ajax({
                                type: "PATCH",
                                url: "verify_submit/" + pr_no,
                                data: $('#selectSupplierSubmitForm').serialize(),
                                success: function(response) {

                                    if (response.success) {
                                        //alert("data updated");
                                        Swal.fire({
                                            position: 'center',
                                            icon: 'success',
                                            title: 'Verification Approved!',
                                            showConfirmButton: false,
                                            timer: 1500
                                        })
                                        setTimeout(function() {
                                            // location.reload();
                                            location.href =
                                                "http://127.0.0.1:8000/approver/pr_for_verification";
                                        }, 1500);
                                    }
                                }
                            })
                    }

                });
            });
        });
    </script>
    @endsection