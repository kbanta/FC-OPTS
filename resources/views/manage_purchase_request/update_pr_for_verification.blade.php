@extends('manage_purchase_request.view_purchase_requestt')

@section('canvass_details')
@if(!empty($outputss[0]['canvass_no']))
<div>
    <span class="badge badge-success" style="font-size: 20px;">Canvass Details</span>

    <table id="example1" class="table table-bordered table-sm">
        <thead>
            <tr>
                @if(Auth::user()->hasRole('Processor'))
                <th>Canvass No.</th>
                @endif
                @if(Auth::user()->hasRole('Approver'))
                <th></th>
                @endif
                <th>Item Desc</th>
                <th>Brand</th>
                <th>Unit/Size</th>
                <!-- <th>Quantity</th> -->
                <th>Price</th>
                <th>Supplier</th>
                @if(Auth::user()->hasRole('Processor'&&'Approver'))
                <th class="text-center">Action</th>
                @endif
            </tr>
        </thead>
        </tr>
        <tbody>
            @foreach($outputss as $canvass)
            <tr>
                <input type="hidden" id="canvassed_item_id" name="canvassed_item_id" value="{{$canvass['id']}}" />
                @if(Auth::user()->hasRole('Processor'))
                <td name="item_desc[]" value="{{$canvass['id']}}">{{$canvass['id']}}</td>
                @endif
                @if(Auth::user()->hasRole('Approver'))
                <!-- <td>
                    <center><input type="checkbox" class="custom-control custom-checkbox check" id="update_canvass" name="update_canvass[]" value="" /></center>
                </td> -->
                <td name="item_desc[]" value="{{$canvass['id']}}">{{$canvass['id']}}</td>
                @endif
                <td name="item_desc[]" value="{{$canvass['item_desc']}}">{{$canvass['item_desc']}}</td>
                <td name="brand[]" value="{{$canvass['brand']}}">{{$canvass['brand']}}</td>
                <td name="unit[]" value="{{$canvass['unit']}}">{{$canvass['unit']}}</td>
                <td name="price[]" value="{{$canvass['offered_price']}}">{{$canvass['offered_price']}}</td>
                <td name="business_name[]" value="{{$canvass['business_name']}}">{{$canvass['business_name']}}</td>
                @if(Auth::user()->hasRole('Processor'))
                <td> <a href="#" class="btn btn-danger btn-block btn-sm canvass_delete_btn"><i class="fa fa-trash"> Remove</i></a>
                </td>
                @endif
                @if(Auth::user()->hasRole('Approver'))
                <td width="17%">
                    <!-- <div class="btn btn-group"> -->
                    <!-- <a href="#" class="btn btn-success btn-sm verified_update_btn"><i class="fa fa-pen"> Update</i></a> -->
                    <a href="#" class="btn btn-danger btn-block btn-sm verified_delete_btn"><i class="fa fa-trash"> Remove</i></a>
                    <!-- </div> -->
                </td>
                @endif
            <tr>
            </tr>
            @endforeach
            </tr>
        </tbody>
        <tfoot>
        </tfoot>
    </table>
</div>
<!-- @include('manage_purchase_request.signatory') -->
@include('manage_purchase_request.delete_canvassed_pr')
@include('manage_purchase_request.update_quantity')
@endif
@endsection

@if(Auth::user()->hasRole('Approver'))
@section('update_btn')
<div class="col-lg-12">
    <div class="card">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <button type="button" class="btn btn-success " value="Show/Hide" onClick="showHideDiv('divMsg')">Update Supplier and Items</button>

                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item active"></li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <div id="divMsg" style="display: none;">
            <section class="p-2">
                <form id="updateVerifiedForm">
                    {{csrf_field()}}
                    {{method_field('POST')}}
                    <table id="example1" class="table table-bordered table-sm">
                        <thead>
                            <tr>
                                <th class="text-center">Select</th>
                                <th class="text-center">Item Desc</th>
                                <th class="text-center">Brand</th>
                                <th class="text-center">Unit/Size</th>
                                <th class="text-center">Quantity</th>
                                <th class="text-center">Price</th>
                                <th class="text-center">Supplier</th>
                            </tr>
                        </thead>
                        </tr>
                        <tbody>
                            @if(!empty($canvass_output))
                            @foreach($canvass_output as $canvassed_item)
                            <tr>
                                <td>
                                    <center><input type="checkbox" class="custom-control custom-checkbox check" id="update_canvass" name="update_canvass[]" value="{{$canvassed_item['id']}}" /></center>
                                </td>
                                <td class="text-center" name="canvass_item[]" value="{{$canvassed_item['item_desc']}}">{{$canvassed_item['item_desc']}}</td>
                                <td class="text-center" name="brand[]" value="{{$canvassed_item['brand']}}">{{$canvassed_item['brand']}}</td>
                                <td class="text-center" name="unit[]" value="{{$canvassed_item['unit']}}">{{$canvassed_item['unit']}}</td>
                                <td class="text-center" name="quantity[]" width="10%"><input type="number" class="form-control text-center quantity" id="quantity" name="quan[]" min='1' required></td>
                                <td class="text-center" name="price[]" value="{{$canvassed_item['offered_price']}}">{{$canvassed_item['offered_price']}}</td>
                                <td class="text-center" name="business_name[]" value="{{$canvassed_item['business_name']}}">{{$canvassed_item['business_name']}}</td>
                            <tr>
                            </tr>
                            @endforeach
                            @endif
                            </tr>
                        </tbody>
                        <tfoot>
                        </tfoot>
                    </table>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Submit</button>
                    </div>
            </section>
        </div>
        </form>
    </div>
</div>

<script type="text/javascript">
    $().ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $('#updateVerifiedForm').on('submit', function(e) {
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
                title: 'Are you sure?',
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
                            url: "update/" + pr_no,
                            data: $('#updateVerifiedForm').serialize(),
                            success: function(response) {

                                if (response.success) {
                                    //alert("data updated");
                                    Swal.fire({
                                        position: 'center',
                                        icon: 'success',
                                        title: 'Verified PR has been updated!',
                                        showConfirmButton: false,
                                        timer: 1500
                                    })
                                    setTimeout(function() {
                                        location.reload();
                                        // location.href =
                                        //     "http://127.0.0.1:8000/approver/pr_for_verification";
                                    }, 1500);
                                }
                            }
                        })
                }

            });
        });
    });

    $(document).ready(function() {
        $("#example").DataTable({});

    });

    function showHideDiv(ele) {
        var srcElement = document.getElementById(ele);
        if (srcElement != null) {
            if (srcElement.style.display == "block") {
                srcElement.style.display = 'none';
            } else {
                srcElement.style.display = 'block';
            }
            return false;
        }
    }
    $(document).ready(function() {
        $('.quantity').attr('disabled', true);

        $('.check').change(function() {
            //find only the quantity in the same row as the selected checkbox
            $(this).closest('tr').find('.quantity').attr('disabled', !this.checked).focus();
        });
    });
</script>
@endsection
@else
@endif