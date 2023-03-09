<!-- Modal-->

<div id="addPurchaseRequest" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
    <div role="document" class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 id="exampleModalLabel" class="modal-title">Add Requisition</h5>
                <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true">×</span></button>
            </div>
            <div class="container" style="width: 100%; height: 100%">
                <div class="forbes-logo-col" style="width:100%; height:auto">
                    <section class="mt-5 pl-4">
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
                    <form id="addRequisitionForm">
                        {{csrf_field()}}
                        <section class="p-2">
                            <span class="badge badge-success" style="font-size: 20px;">Purchase Requisiton Form</span>
                            <div class="custom-control custom-checkbox">
                                @if(empty($user->Building_name))
                                <div class="alert alert-danger">
                                    <span>Error!! Update Profile First!</span>
                                </div>
                                @else
                                <input type="checkbox" class="custom-control-input" id="building" name="building" value="{{$user->id}}" checked="true">
                                <label class="custom-control-label" for="building">{{$user->Building_name}}</label>
                                @endif
                            </div>
                            <table class="table table-bordered table-sm">
                                <tbody>
                                    <tr>
                                        <td colspan="2">
                                            <p>Type of Requisition:</p>
                                            <!-- Default inline 1-->
                                            <div class="custom-control custom-radio custom-control-inline">
                                                <input type="radio" class="custom-control-input" id="type_of_req1" value="Goods/Supplies" name="type_of_req">
                                                <label class="custom-control-label" for="type_of_req1">Goods/Supplies</label>
                                            </div>

                                            <!-- Default inline 2-->
                                            <div class="custom-control custom-radio custom-control-inline">
                                                <input type="radio" class="custom-control-input" id="type_of_req2" value="School Equipment" name="type_of_req">
                                                <label class="custom-control-label" for="type_of_req2">School Equipment</label>
                                            </div>

                                            <!-- Default inline 3-->
                                            <div class="custom-control custom-radio custom-control-inline">
                                                <input type="radio" class="custom-control-input" id="type_of_req3" value="Services" name="type_of_req">
                                                <label class="custom-control-label" for="type_of_req3">Services</label>
                                            </div>
                                            <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
                                            <span class="text-danger">
                                                <strong id="type_of_req-error"></strong>
                                            </span>
                                        </td>
                                        <td style="display:none;"><small>PR number:</small>
                                            <input type="hidden" class="form-group" id="pr_no" name="pr_no" value="{{$generatePR}}">
                                            <p>{{$generatePR}}</p>
                                        </td>
                                        <td>
                                            <small>Date:</small><br>
                                            <span>{{ now(); }}</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th colspan="3">
                                            <p>Requesting Department:</p>
                                            @if(empty($userr->id))
                                            <input type="hidden" class="form-group" id="department" name="department">
                                            <span style="font-size: 18px;"></span>
                                            @else
                                            <input type="hidden" class="form-group" id="department" name="department" value="{{$userr->id}}">
                                            <span style="font-size: 18px;">{{$userr->Dept_name}}</span>
                                            @endif

                                        </th>
                                        <!-- <td>
                                            <small>Date:</small><br>
                                            <span>{{ now(); }}</span>
                                        </td> -->
                                    </tr>
                                    <tr>
                                        <th colspan="4">
                                            <div class="form-group">
                                                <label for="exampleFormControlTextarea2">Purpose of Requisition</label>
                                                <textarea class="form-control rounded-0" id="purpose" name="purpose" rows="3" style="overflow:auto;resize:none"></textarea>
                                                <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
                                                <span class="text-danger">
                                                    <strong id="purpose-error"></strong>
                                                </span>
                                            </div>
                                        </th>
                                    </tr>
                                </tbody>
                            </table>
                            <table class="table table-bordered table-sm" id="myTable">
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
                                    <th class="action_buttons" style="width:10%">
                                        <button type='button' class="btn btn-success btn-block btn-sm" onclick='x()'>
                                            <i class="fas fa-plus-square">Add item</i>
                                        </button>
                                    </th>
                                </tr>
                                <tbody>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td class="request_bottom" colspan="5">
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
                        </section>
                        <div class="modal-footer">
                            <button type="button" data-dismiss="modal" class="btn btn-secondary">Close</button>
                            <button type="submit" class="btn btn-primary" id="submit-btn">Submit Request</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        // alert(data);
        const x = () => {
            var table = document.getElementById("myTable").getElementsByTagName('tbody')[0];
            var row = table.insertRow();

            function addoption() {
                $('#addoption').append('<option value="${taskArray}">${taskArrayy}</option>');
            }

            let cell1 = row.insertCell(0);
            let cell2 = row.insertCell(1);
            let cell3 = row.insertCell(2);
            let cell4 = row.insertCell(3);
            let cell5 = row.insertCell(4);
            let cell6 = row.insertCell(5);

            cell1.innerHTML = "<p><input class='form-control request_table' type='text' name='beggining[]'></p>";
            cell2.innerHTML = "<p><input class='form-control request_table' type='text' name='ending[]'></p>";
            cell3.innerHTML = "<p><input class='form-control request_table' type='number' min='1' name='quantity[]' required></p>";
            cell4.innerHTML = "<p><input class='form-control request_table' type='text' name='unit[]' required></p>";
            // cell5.innerHTML = "<p><input class='form-control request_table' type='text' name='item_desc[]' required></p>";
            // cell5.innerHTML = "<p><input class='form-control request_table' type='search' name='item_desc[]' @foreach($items as $item) placeholder='{{$item['item_desc']}}' @endforeach required></p>";
            cell5.innerHTML = "<p><input class='form-control item_desc' list='item' type='search' name='item_desc[]' required><datalist id='item'>@foreach($items as $item)<option value='{{$item['item_desc']}}'>@endforeach</datalist></p>";
            cell6.innerHTML = "<button type='button' class='btn btn-danger btn-block btn-sm' onclick='y()'><i class='fa fa-trash'></i>Remove</button>";
        }

        const y = () => {
            var td = event.target.parentNode;
            var tr = td.parentNode;
            tr.parentNode.removeChild(tr);
        }
    </script>
    <script>
        $().ready(function() {
            $('#addRequisitionForm').on('submit', function(e) {
                e.preventDefault();
                document.getElementById("submit-btn").disabled = true;
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
                        $.ajax({
                            type: "POST",
                            url: "requisitionsave/",
                            data: $('#addRequisitionForm').serialize(),
                            success: function(response) {
                                console.log(response);
                                if (response.errors) {
                                    if (response.errors.type_of_req) {
                                        $('#type_of_req-error').html(response.errors.type_of_req[0]);
                                    }
                                    if (response.errors.purpose) {
                                        $('#purpose-error').html(response.errors.purpose[0]);
                                    }
                                    if (response.errors.beggining) {
                                        $('#beggining-error').html(response.errors.beggining[0]);
                                    }
                                    if (response.errors.ending) {
                                        $('#ending-error').html(response.errors.ending[0]);
                                    }
                                    if (response.errors.quantity) {
                                        $('#quantity-error').html(response.errors.quantity[0]);
                                    }
                                    if (response.errors.unit) {
                                        $('#unit-error').html(response.errors.unit[0]);
                                    }
                                    if (response.errors.item_desc) {
                                        $('#item_desc-error').html(response.errors.item_desc[0]);
                                    }
                                }
                                if (response.success) {
                                    $('#addRequisitionForm').modal('hide');
                                    //alert("data updated");
                                    Swal.fire({
                                        position: 'center',
                                        icon: 'success',
                                        title: 'Your work has been saved',
                                        showConfirmButton: false,
                                        timer: 3500
                                    });
                                    setTimeout(function() {
                                        location.reload();
                                    }, 3000);
                                }
                            },
                        });
                    }
                });
            });

        });
    </script>
    <style>
        .item_desc {
            text-transform: capitalize;
        }
    </style>