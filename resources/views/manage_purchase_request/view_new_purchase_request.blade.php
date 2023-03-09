@extends('manage_purchase_request.view_purchase_requestt')

@section('check_verify')
@if(Auth::user()->hasRole('Approver'))

@endif
@endsection

@section('check_verify_items')
@if($user->position=="Corporate Treasurer")

@else

@endif
@include('manage_purchase_request.check_verify_item')
@endsection

@section('deny_message')
@if(!empty($deny[0]['deny_message']))
<div class="form-group">
    <label for="comment"><span class="badge badge-danger" style="font-size: 15px;">Denied Message:</span></label>
    <textarea class="form-control" id="comment" style="overflow:auto;resize:none" readonly>{{$deny[0]['deny_message']}}</textarea>
</div>
@else
@endif
@endsection

@section('verify_item_btn')
@if(Auth::user()->hasRole('Approver'))
@if($output[0]['action'] == 'For Canvassing')
@else
@if(!empty($deny[0]['deny_message']))
<div class="modal-footer">
    <div class="btn-group">
        <button type="submit" class="btn btn-success" id="submit-btn">Submit for Canvass</button>
    </div>
</div>
@else
<div class="modal-footer">
    <div class="btn-group">
        @if($chk_item==null)
        <!-- <a data-toggle="modal" data-target="#denyMessage" class="btn btn-danger" href="">Deny</a>
        <button type="submit" class="btn btn-success">Submit for Canvass</button> -->
        @else
        <!-- <div class="modal-footer"> -->
        <div class="btn-group">
            <a data-toggle="modal" data-target="#denyMessage" class="btn btn-danger" href="">Deny</a>
            <button type="submit" class="btn btn-success" id="submit-btn">Submit for Canvass</button>
        </div>
        <!-- </div> -->
        @endif
    </div>
</div>
@endif

@endif
@else
@endif
@include('manage_purchase_request.deny_message')
<script type="text/javascript">
    $().ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $('#viewPRForm').on('submit', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Send for Canvass?',
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
                            url: "update_new_pr/" + pr_no,
                            data: $('#viewPRForm').serialize(),
                            success: function(response) {

                                if (response.success) {
                                    //alert("data updated");
                                    Swal.fire({
                                        position: 'center',
                                        icon: 'success',
                                        title: 'PR has been sent for Canvass!',
                                        showConfirmButton: false,
                                        timer: 1500
                                    })
                                    setTimeout(function() {
                                        // location.reload();
                                        location.href =
                                            "http://127.0.0.1:8000/approver/new_pr";
                                    }, 1500);
                                }
                            }
                        })
                }

            });
        });

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
        $("#example1").DataTable({})

    });
</script>
@endsection