@extends('adminltelayout.layout')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">View Pr</h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item">Home</li>
                    <li class="breadcrumb-item">Purchase Request</li>
                    <li class="breadcrumb-item active">View Purchase Request</li>
                </ol>
            </div><!-- /.col -->
        </div><!-- /.row -->
    </div><!-- /.container-fluid -->
</div>

<div class="col-lg-12">
    <div class="card">
        <div class="forbes-logo-col" style="width:100%; height:auto">
            <section class="mt-5 pl-4">
                <!-- @if(!empty($output[0]['action']))
                <span class="badge badge-danger" style="font-size: 20px; float:right;">{{ $output[0]['action'] }}</span>
                @endif -->
                <div style="float: right;">
                    @if(Auth::user()->hasRole('Requestor'))
                    <a href="{{route('req_pr_track',$pr_no = $output[0]['pr_no'] )}}" class="btn btn-danger btn-sm view_btn"><i class="fa fa-eye"></i> Track Request</a>
                    @elseif(Auth::user()->hasRole('Processor'))
                    <a href="{{route('pro_pr_track',$pr_no = $output[0]['pr_no'] )}}" class="btn btn-danger btn-sm view_btn"><i class="fa fa-eye"></i> Track Request</a>
                    @elseif(Auth::user()->hasRole('Validator'))
                    <a href="{{route('val_pr_track',$pr_no = $output[0]['pr_no'] )}}" class="btn btn-danger btn-sm view_btn"><i class="fa fa-eye"></i> Track Request</a>
                    @endif
                </div>
                <div class="row d-flex">
                    <div class="row">
                        <div class="col-12 col-sm-auto mb-3">
                            <div class="mx-auto" style="width: 100px;">
                                <div class="d-flex justify-content-center align-items-center rounded">
                                    <span style="color: rgb(166, 168, 170); font: bold 8pt Arial;"> <img src="{{ asset('dist/img/forbeslogo.png')}}" alt="person" class="img-fluid "> </span>
                                </div>
                            </div>
                        </div>
                        <div class="col d-flex flex-column flex-sm-row justify-content-between mb-3">
                            <div class="text-center text-sm-left mb-2 mb-sm-0">
                                <h4 class="pt-sm-2 pb-0 mb-0 text-nowrap">Forbes College Inc.</h4>
                                <p class="mb-0">E. Aquende Bldg. III Rizal Corner Elizondo St. Legazpi City</p>
                                <div class="text-muted"><small>4500, Philippines</small></div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <form id="viewPRForm">
                {{csrf_field()}}
                {{method_field('PUT')}}
                <section class="p-2">
                    <!-- <span class="badge badge-success" style="font-size: 20px;">Purchase Requisiton Form</span> -->
                    <!-- <span class="" style="font-size: 22px;"> </span> -->
                    <h4 class="pt-sm-2 pb-0 mb-0 text-nowrap"><b>Purchase Requisiton Form </b></h4>
                    <div>
                        Building:
                        @if(!empty($output[0]['Building_name']))
                        <input type="hidden" id="email" name="email" value="{{ $user_email[0]['email'] }}">
                        <span>{{ $output[0]['Building_name'] }}</span>
                        @endif
                    </div>
                    <table class="table table-bordered table-sm">
                        <tbody>
                            <tr>
                                <td colspan="2">
                                    Type of Requisition:
                                    @if(!empty($output[0]['type']))
                                    <span>{{$output[0]['type']}}</span>
                                    @endif
                                </td>
                                <td>
                                    PR number:
                                    @if(!empty($output[0]['pr_no']))
                                    <input type="hidden" id="pr_no" name="pr_no" value="{{ $output[0]['pr_no'] }}">
                                    <label class="" for="pr_no">{{ $output[0]['pr_no'] }}</label>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    Requesting Department:
                                    @if(!empty($output[0]['department_id']))
                                    <input type="hidden" class="form-group">
                                    <span style="font-size: 18px;">{{$output[0]['Dept_name']}}</span>
                                    @endif
                                </td>
                                <td>
                                    Date:
                                    @if(!empty($outputs[0]['created_at']))
                                    <span>{{date('Y-m-d H:i:s' ,strtotime($outputs[0]['created_at']))}}</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td colspan="4">
                                    <div class="form-group">
                                        <span>Purpose of Requisition</span>
                                        @if(!empty($output[0]['purpose']))
                                        <textarea class="form-control rounded-0" rows="3" style="overflow:auto;resize:none" readonly>{{$output[0]['purpose']}}</textarea>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <table class="table table-bordered table-sm">
                        <thead>
                            <tr>
                                <th>Beginning</th>
                                <th>Ending</th>
                                <th>Unit</th>
                                <th>Quantity</th>
                                <th>Item Desc</th>
                            </tr>
                        </thead>
                        </tr>
                        <tbody>
                            <tr>
                                @if(!empty($output))
                                @foreach($output as $outputs)
                                <td style="display: none;" class="">{{$outputs['id']}}</td>
                                <td style="display: none;" class="">{{$outputs['pr_no']}}</td>
                                <td class="pr_beggining">{{$outputs['beggining']}}</td>
                                <td class="pr_ending">{{$outputs['ending']}}</td>
                                <td class="pr_unit">{{$outputs['unit']}}</td>
                                <td class="pr_quantity">{{$outputs['quantity']}}</td>
                                <td class="pr_itemdesc">{{$outputs['item_desc']}}</td>
                                @if(Auth::user()->hasRole('Approver'))
                                @yield('check_verify_items')
                                <td class="text-center">
                                    @if(!empty($outputs['item_id']))
                                    <i style="color: green;" class="fa fa-check"></i>
                                    @elseif($outputs['item_id'] == '0')
                                    <i style="color: red;" class="fa fa-times"></i>
                                    @else
                                    <div class="btn btn-group">
                                        <a href="#" class="btn btn-success  btn-sm update_btn"><i class=""></i>Check</a>
                                        <a href="#" class="btn btn-danger  btn-sm denyitem_btn"><i class=""></i> Deny</a>
                                    </div>
                                    @endif
                                </td>
                                @if($output[0]['action'] == 'For Canvassing' and $user->position == 'ASSD Manager')
                                <td>
                                    <a href="#" class="btn btn-success btn-block btn-sm update_btn"><i class="fa fa-eye"></i> Check item</a>
                                    @else
                                </td>
                                @endif
                                @else
                                <td class="text-center">
                                    @if(!empty($outputs['item_id']))
                                    <i style="color: green;" class="fa fa-check"></i>
                                    @endif
                                    @if($output[0]['action'] == 'Verifying' or $output[0]['action'] == 'Checked' or $output[0]['action'] == 'Approved')
                                    @if($outputs['item_id'] == 0)
                                    <i style="color: red;" class="fa fa-times"></i>
                                    @endif
                                    @endif
                                </td>
                                @endif
                            <tr>
                            </tr>
                            @endforeach

                            @endif
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td class="request_bottom text-center" colspan="6">
                                    <p>*****nothing follows*****</p>
                                </td>
                            </tr>
                            <!-- <tr>
                                <td class="request_bottom" colspan="5">
                                    <p>Last request:</p>
                                </td>
                            </tr> -->
                        </tfoot>
                    </table>
                    <!-- @if(Auth::user()->hasRole('Approver'))
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Submit for Canvass</button>
                    </div>
                    @endif -->
                </section>
            </form>
        </div>
    </div>
</div>

<script type="text/javascript">
    $().ready(function() {
        $('.generate_canvass').on('click', function() {

            pr_no = jQuery('#pr_no').val(),
                console.log(pr_no);

            $.ajax({
                type: "GET",
                url: "generate_canvass/" + pr_no,
            })

        });

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $('#viewPRForm').on('submit', function(e) {
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
                            url: "verify_pr/" + pr_no,
                            data: $('#viewPRForm').serialize(),
                            success: function(response) {

                                if (response.success) {
                                    //alert("data updated");
                                    Swal.fire({
                                        position: 'center',
                                        icon: 'success',
                                        title: 'PR has been sent',
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
    $('#canvassForm').on('submit', function(e) {
        e.preventDefault();
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
                                    title: 'PR has been sent',
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
</script>
@endsection