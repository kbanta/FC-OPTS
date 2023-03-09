@extends('manage_purchase_request.view_purchase_requestt')
@section('canvass_btn')
<div class="col-lg-12">
    <div class="card">
        <div class="content-header">
            <div class="container-fluid">
                <button type="button" data-toggle="modal" data-target="#canvassRequest" class="btn btn-success btn-block">Canvass</button>
            </div>
        </div>
    </div>
</div>
<div id="canvassRequest" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
    <div role="document" class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 id="exampleModalLabel" class="modal-title">Canvass Items</h5>
                <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true">×</span></button>
            </div>
            <div class="col-lg-12">
                <div class="card">
                    <!-- <div class="content-header">
                        <div class="container-fluid">
                            <div class="row mb-2"> -->
                    <!-- <div class="col-sm-6">
                                    <button type="button" class="btn btn-success" value="Show/Hide" onClick="showHideDiv('divMsg')">Canvass</button>
                                </div>
                                <div class="col-sm-6">
                                    <ol class="breadcrumb float-sm-right">
                                        <li class="breadcrumb-item active">Canvass</li>
                                    </ol>
                                </div>/.col -->
                    <!-- </div>
                        </div>
                    </div> -->
                    <!-- <div id="divMsg" style="display: none;"> -->
                    <div>
                        <section class="p-2">
                            <span class="" style="font-size:18px;">Select 3 supplier per Item</span>
                            <br>
                            @php
                            $no_item = $item;
                            @endphp
                            @if(!empty($nn))
                            <span class="badge badge-danger" style="font-size:15px;">Warning! Item Description not yet Canvassed :[
                                @foreach($no_item as $nc)
                                {{$nc['item_desc']}} -
                                @endforeach
                                ]
                            </span>
                            <!-- <span style="float:right;font-size:15px;color:white" class="badge badge-warning">  -->
                            <a href="{{ route('pro_supplier_items') }}" style="float: right;"> Click here to input canvass </a>
                            <!-- </span> -->
                            @else

                            @endif
                            <br>
                            <form id="canvassForm">
                                {{csrf_field()}}
                                {{method_field('POST')}}
                                @if(!empty( $user_email[0]['email']))
                                <input type="hidden" id="email" name="email" value="{{ $user_email[0]['email'] }}">
                                @endif
                                @foreach(array_keys($gg) as $key => $outputs)
                                <table id="example1" class="table table-bordered table-sm table-responsive">
                                    <thead>
                                        <tr>
                                            <th class="text-center">Select</th>
                                            <th class="text-center">Item Description</th>
                                            <th class="text-center">Brand</th>
                                            <th class="text-center">Unit</th>
                                            <th class="text-center" width="15%">Price</th>
                                            <th class="text-center">Supplier</th>
                                        </tr>
                                    </thead>
                                    </tr>
                                    <tbody>
                                        @foreach($gg[$outputs] as $key)
                                        <tr>
                                            <td width="10%">
                                                <center><input type="checkbox" class="custom-control custom-checkbox check" name="checkbox_canvass[]" value="{{$key['id']}}" /></center>
                                            </td>
                                            <td class="text-center" width="30%"><input type="hidden" id="key[]" name="key[]" value="{{$key['item_desc']}}" />{{$key['item_desc']}}</td>
                                            <td class="text-center" width="20%" name="brand[]" value="{{$key['brand']}}">{{$key['brand']}}</td>
                                            <td class="text-center" width="10%" name="unit[]" value="{{$key['unit']}}">{{$key['unit']}}</td>
                                            <td class="text-center" width="15%"><input type="number" class="form-control text-center quantity" min="0" step="0.01" name="update_price[]" value="{{$key['offered_price']}}" /></td>
                                            <td class="text-center" width="20%" name="business_name[]" value="{{$key['business_name']}}">{{$key['business_name']}}</td>
                                        <tr>
                                        </tr>
                                        @endforeach
                                        </tr>
                                    </tbody>
                                    <tfoot>
                                    </tfoot>
                                </table>
                                <br>
                                @endforeach
                                <div class="modal-footer">
                                    @if(!empty($nn))
                                    @else
                                    <button type="submit" class="btn btn-success" id="submit-btn">Submit Canvass</button>
                                    @endif
                                </div>
                        </section>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $().ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $('#canvassForm').on('submit', function(e) {
            if ($('.check').filter(':checked').length < 1) {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Select Atleast One!',
                })
                return false;
            }
            e.preventDefault();
            document.getElementById("submit-btn").disabled = true;
            Swal.fire({
                title: 'Send Canvass?',
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
                            type: "POST",
                            url: "send_canvass/" + pr_no,
                            data: $('#canvassForm').serialize(),
                            success: function(response) {

                                if (response.success) {
                                    //alert("data updated");
                                    Swal.fire({
                                        position: 'center',
                                        icon: 'success',
                                        title: 'Canvassed Item for PR has been sent!',
                                        showConfirmButton: false,
                                        timer: 1500
                                    })
                                    setTimeout(function() {
                                        // location.reload();
                                        location.href =
                                            "http://127.0.0.1:8000/processor/pr_for_canvass";
                                    }, 1500);
                                }
                            },
                            error: function(xhr, status, error) {
                                console.log(xhr.responseJSON.message);
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Oops...',
                                    text: 'Canvass is Incomplete.',
                                })
                            }
                        })
                }

            });
        });

    })
    $(document).ready(function() {
        $('.quantity').attr('disabled', true);

        $('.check').change(function() {
            //find only the quantity in the same row as the selected checkbox
            $(this).closest('tr').find('.quantity').attr('disabled', !this.checked).focus();
        });
    });
</script>
@endsection