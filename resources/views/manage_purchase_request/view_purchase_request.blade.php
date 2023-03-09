<!-- Modal-->

<div id="viewPurchaseRequest" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="false" class="modal fade text-left">
    <div role="document" class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 id="exampleModalLabel" class="modal-title">View Account</h5>
                <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true">×</span></button>
            </div>
            <div class="container" style="width: 100%; height: 100%">
                <div class="forbes-logo-col" style="width:100%; height:auto">
                    <section class="mt-5 pl-4">
                        <div class="row d-flex">
                            <div class="row">
                                <div class="col-12 col-sm-auto mb-3">
                                    <div class="mx-auto" style="width: 140px;">
                                        <div class="d-flex justify-content-center align-items-center rounded">
                                            <span style="color: rgb(166, 168, 170); font: bold 8pt Arial;"> <img src="{{ asset('dist/img/forbeslogo.png')}}" alt="person" class="img-fluid "> </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col d-flex flex-column flex-sm-row justify-content-between mb-3">
                                    <div class="text-center text-sm-left mb-2 mb-sm-0">
                                        <br>
                                        <h4 class="pt-sm-2 pb-0 mb-0 text-nowrap">Forbes College Inc.</h4>
                                        <p class="mb-0">E. Aquende Bldg. III Rizal Corner Elizondo St. Legazpi City</p>
                                        <div class="text-muted"><small>4500, Philippines</small></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                    <form id="viewPR">
                        {{csrf_field()}}
                        <section class="p-2">
                            <span class="badge badge-success" style="font-size: 20px;">Purchase Requisiton Form</span>
                            <div class="custom-control custom-checkbox">
                                @if(!empty($user))
                                <input type="checkbox" class="custom-control-input" id="building" name="building" value="" checked>
                                <label class="custom-control-label" for="building">{{ $user->Building_name }}
                                    @endif
                            </div>
                            <table class="table table-bordered table-sm">
                                <tbody>
                                    <tr>
                                        <td colspan="2">
                                            Type of Requisition:
                                            <!-- Default inline 1-->
                                            <div class="custom-control custom-radio custom-control-inline">
                                                @if(!empty($output[0]['type']))
                                                <input type="radio" class="custom-control-input" id="type_of_req1" value="" name="type_of_req" checked>
                                                <label class="custom-control-label" for="type_of_req1">{{$output[0]['type']}}</label>
                                                @endif

                                            </div>
                                            <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
                                            <span class="text-danger">
                                                <strong id="type_of_req-error"></strong>
                                            </span>
                                        </td>
                                        <td>
                                            PR number:
                                            @if(!empty($output[0]['pr_no']))
                                            <input type="hidden" class="form-group" id="pr_noo" name="pr_noo" value="{{ $output[0]['pr_no'] }}">
                                            <label class="custom-control-label pr_no" for="">{{ $output[0]['pr_no'] }}</label>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th colspan="2">
                                            Requesting Department:
                                            @if(!empty($user))
                                            <input type="hidden" class="form-group" id="department" name="department" value="">
                                            <span style="font-size: 18px;">{{$user->Dept_name}}</span>
                                            @endif
                                        </th>
                                        <td>
                                            Date:
                                            @if(!empty($outputs[0]['created_at']))
                                            <span>{{date('Y-m-d H:i:s' ,strtotime($outputs[0]['created_at']))}}</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th colspan="4">
                                            <div class="form-group">
                                                <label for="exampleFormControlTextarea2">Purpose of Requisition</label>
                                                @if(!empty($output[0]['purpose']))
                                                <textarea class="form-control rounded-0" id="purpose" name="purpose" rows="3" style="overflow:auto;resize:none" readonly>{{$output[0]['purpose']}}</textarea>
                                                @endif
                                            </div>
                                        </th>
                                    </tr>
                                </tbody>
                            </table>
                            <table class="table table-bordered table-sm">
                                <tr>
                                    <th class="table2" style="width: 15%">
                                        <p>Beginning:</p>
                                        <span class='glyphicon glyphicon-envelope form-control-feedback'></span>
                                        <span class='text-danger'>
                                            <strong id='beggining-error'>
                                            </strong>
                                        </span>
                                    </th>
                                    <th class="table2" style="width: 15%">
                                        <p>Ending:</p>
                                        <span class='glyphicon glyphicon-envelope form-control-feedback'></span>
                                        <span class='text-danger'>
                                            <strong id='ending-error'>
                                            </strong>
                                        </span>
                                    </th>
                                    <th class="table2" style="width: 15%">
                                        <p>Quantity:</p>
                                        <span class='glyphicon glyphicon-envelope form-control-feedback'></span>
                                        <span class='text-danger'>
                                            <strong id='quantity-error'>
                                            </strong>
                                        </span>
                                    </th>
                                    <th class="table2" style="width: 15%">
                                        <p>Unit:</p>
                                        <span class='glyphicon glyphicon-envelope form-control-feedback'>
                                        </span><span class='text-danger'>
                                            <strong id='unit-error'>
                                            </strong>
                                        </span>
                                    </th>
                                    <th class="table2" class="request_description">
                                        <p>Item Description</p>
                                        <span class='glyphicon glyphicon-envelope form-control-feedback'></span>
                                        <span class='text-danger'>
                                            <strong id='item_desc-error'>
                                            </strong>
                                        </span>
                                    </th>
                                </tr>
                                <tbody>
                                    <tr>
                                        @if(!empty($output))
                                        @foreach($output as $outputs)
                                        <td class="pr_beggining">{{$outputs['beggining']}}</td>
                                        <td class="pr_type">{{$outputs['ending']}}</td>
                                        <td class="pr_purpose">{{$outputs['unit']}}</td>
                                        <td class="pr_remark">{{$outputs['quantity']}}</td>
                                        <td class="pr_remark">{{$outputs['item_desc']}}."\n"</td>

                                        @endforeach
                                        @endif
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td class="request_bottom" colspan="5">
                                            <p>*****nothing follows*****</p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="request_bottom" colspan="5">
                                            <p>Last request:</p>
                                        </td>
                                    </tr>
                                </tfoot>

                            </table>

                        </section>
                        <div class="modal-footer"></div>
                </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- <script type="text/javascript">
    $().ready(function() {
        $('.view_btn').on('click', function() {
            $('#viewPurchaseRequest').appendTo("body").modal('show');

            let row = $(this).closest("tr");
            let pr_no = row.find(".pr_pr_no").text();
            console.log(pr_no);
            

            $.ajax({
                type: "GET",
                url: "purchase_request/view/" + pr_no,
                data: $('#viewPR').serialize(),
            })

        });
    });
</script> -->