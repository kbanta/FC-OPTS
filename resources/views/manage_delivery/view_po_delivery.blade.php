@extends('adminltelayout.layout')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">View Delivery</h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item">Home</li>
                    <li class="breadcrumb-item active">Purchase Order</li>
                </ol>
            </div><!-- /.col -->
        </div><!-- /.row -->
    </div><!-- /.container-fluid -->
</div>

<div class="col-lg-12">
    <div class="card">
        <div class="forbes-logo-col" style="width:100%; height:auto">
            <section class="mt-4 pl-4">
                @if(Auth::user()->hasRole('Approver'))
                <a href="{{route('track_forwarded_app',[$dln = $output[0]['delivery_no'],$po_no = $output[0]['po_no']] )}}" class="btn btn-danger  btn-sm view_btn" style="float: right;"><i class="fa fa-eye"></i> Track Delivery</a>
                @elseif(Auth::user()->hasRole('Processor'))
                @if(Auth::user()->id == $output[0]['user_id'])
                <a href="{{route('track_forwarded_pro',[$dln = $output[0]['delivery_no'],$po_no = $output[0]['po_no'],$staff_id = $output[0]['user_id']] )}}" class="btn btn-danger  btn-sm view_btn" style="float: right;"><i class="fa fa-eye"></i> Track Delivery</a>
                @else
                @endif
                @endif
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
                                <p class="mb-0">E. Aquende Bldg. III Rizal corp. Elizondo St. Legazpi City</p>
                                <div class="text-muted"><small>4500, Philippines</small></div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <form id="deliveryForm">
                {{csrf_field()}}
                {{method_field('PUT')}}
                <section class="p-2">
                    <!-- TABLE: LATEST ORDERS -->
                    <div class="card">
                        <div class="card-header">
                            <input type="hidden" id="po_no" name="po_no" value="{{$po_output[0]['po_no']}}">
                            <input type="hidden" id="id" name="id" value="{{$user->id}}">
                            <input type="hidden" id="position" name="position" value="{{$user->position}}">
                            <h3 class="card-title">Purchase Order # : <i style="color:green;"> <b> {{$po_output[0]['po_no']}} </b></i></h3>
                            <p style="float: right;">Created Date: {{$po_output[0]['createdDate']}}</p>
                            <br>
                            <h4 class="card-title">Purchase Request # : {{$po_output[0]['pr_no']}}</h4>
                        </div>
                    </div>

                    <div class="card col-8">
                        <div class="card-header">
                            <h3 class="card-title">Purposed of Requisition : {{$po_output[0]['purpose']}}</h3>
                        </div>
                    </div>
                    @if(Auth::user()->hasRole('Validator'))
                    <div class="card col-6">
                        <div class="card-header">
                            @if(!empty($po_output[0]['paymentTerm']))
                            <h3 class="card-title"> Payment Term : {{$po_output[0]['paymentTerm']}}</h3>
                            @else
                            <label class="text-muted">Payment Term :</label>
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <label class="input-group-text" for="gender3">Method</label>
                                </div>
                                <select required aria-required="true" class="custom-select" id="paymentTerm" name="paymentTerm">
                                    <option value="" selected disabled>Choose...</option>
                                    <option value="Bank Transfer">Bank Transfer</option>
                                    <option value="Cash">Cash</option>
                                    <option value="Cheque">Cheque</option>
                                </select>
                                <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
                                <span class="text-danger">
                                    <strong id="paymentTerm-error"></strong>
                                </span>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endif
                    @if(Auth::user()->hasRole('Validator'))

                    @else
                    @if(!empty($po_output[0]['paymentTerm']))
                    <div class="card col-6">
                        <div class="card-header">
                            <h3 class="card-title"> Payment Term : {{$po_output[0]['paymentTerm']}}</h3>
                        </div>
                    </div>
                    @endif
                    @endif

                    <div class="card col-12">
                        <div class="card-header">
                            <h3 class="card-title">Suppliers and Items</h3>
                        </div>
                        <div class="card-body p-0">
                            <div class="table">
                                <table class="table table-bordered table-sm">
                                    <thead>
                                        <tr>
                                            <!-- @if(Auth::user()->hasRole('Processor'))
                                            <center>
                                                <th style="width: 13%;">Delivery</th>
                                            </center>
                                            @endif -->
                                            <th>Item Description</th>
                                            <th>Supplier</th>
                                            <th>Brand</th>
                                            <th style="width: 10px;">Unit</th>
                                            <th style="width: 10px;">Quantity</th>
                                            <th>Price</th>
                                            <th style="width: 13%;">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                        $total=0;
                                        @endphp

                                        @foreach($po_output as $outputs)
                                        <tr>
                                            <!-- @if(Auth::user()->hasRole('Processor'))
                                            <center>
                                                <td><a class="delivery_btn" href="#">Set Delivery</a></td>
                                            </center>
                                            @endif -->
                                            <td style="display: none;">{{$outputs['id']}}</td>
                                            <td class="" name="item_desc[]" value="{{$outputs['item_desc']}}">{{$outputs['item_desc']}}</td>
                                            <td class="" name="business_name[]" value="{{$outputs['business_name']}}">{{$outputs['business_name']}}</td>
                                            <td class="" name="brand[]" value="{{$outputs['brand']}}">{{$outputs['brand']}}</td>
                                            <td class="" name="unit[]" value="{{$outputs['unit']}}">{{$outputs['unit']}}</td>
                                            <td class="" name="quantity[]" value="{{$outputs['quantity']}}">{{$outputs['quantity']}}</td>
                                            <td class="" name="price[]" value="{{$outputs['offered_price']}}">{{$outputs['offered_price']}}</td>
                                            <td class="" id="total" name="price[]" value="{{$outputs['quantity']*$outputs['offered_price']}}">{{$outputs['quantity']*$outputs['offered_price']}}</td>
                                            <td style="display: none;">{{$user->id}}</td>

                                        </tr>
                                        @php
                                        $total += $outputs['quantity']*$outputs['offered_price']
                                        @endphp
                                        @endforeach

                                        <tr>
                                            @if(Auth::user()->hasRole('Processor'))
                                            <td colspan="6">
                                                @else
                                            <td colspan="6">
                                                @endif
                                                <i>
                                                    <p style="float: right;">Total:</p>
                                                </i>
                                            </td>
                                            <td class="">
                                                <i><b>₱ {{$total}}</b></i>
                                            </td>
                                        </tr>
                                        </tr>
                                    </tbody>

                                </table>
                                <br>

                            </div>
                        </div>
                    </div>
                    @foreach(array_keys($dl) as $key => $outputs)
                    @if(!empty($deliveryDetails[0]['delivery_no']))
                    <div class="card col-12">
                        <div class="card-header">
                            <span class="" style="font-size: 20px;">Delivery #: {{$delivery_detailss[0]['delivery_no']}}</span>
                            <div class="btn btn-group" style="float: right;">
                                <!-- <a href="#" data-toggle="modal" data-target="#updateDelivery" class="btn btn-danger btn-sm view_btn" style="float: right;"><i class="fa fa-pen"></i> Update</a> -->
                                <!-- <a href="#" data-toggle="modal" data-target="#generateDeliveryNo" class="btn btn-success btn-sm supp_per_item" style="float: right;"><i class="fa fa-truck"></i> Other DL no.</a> -->
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <!-- <span class="" style="font-size: 18px;"></span> -->
                            <div class="table">
                                <table class="table table-bordered table-sm">
                                    <thead>
                                        <tr>
                                            <th>Supplier</th>
                                            <th>Item Description</th>
                                            <th>Brand</th>
                                            <th>Unit</th>
                                            <th>Delivered</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            @foreach($dl[$outputs] as $deliver)
                                            @if(!empty($deliver['item_quantity']))
                                            <td>{{$deliver['business_name']}}</td>
                                            <td>{{$deliver['item_desc']}}</td>
                                            <td>{{$deliver['item_brand']}}</td>
                                            <td>{{$deliver['item_unit']}}</td>
                                            <td>{{$deliver['item_quantity']}}</td>
                                            @else
                                            @endif
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                <div class="modal-footer">
                                    @yield('forward')
                                </div>
                                @else

                                @endif
                                @endforeach

                            </div>
                        </div>
                    </div>
        </div>
    </div>
    </section>
    </form>
</div>
</div>
</div>
@include('manage_order.generate_delivery_no')
@include('manage_delivery.forward_delivery_modal')

<!-- @include('manage_delivery.update_delivery') -->
@include('manage_delivery.received_delivery_modal')

<script>
    $().ready(function() {
        $('.delivery_btn').on('click', function() {
            $('#updateDelivery').modal('show');

            $tr = $(this).closest('tr');

            var data = $tr.children("td").map(function() {
                return $(this).text();
            }).get();

            console.log(data);

            $('#supplier_id').val(data[1]);

            $('#item_desc').val(data[2]);
            $('#item_brand').val(data[4]);
            $('#item_unit').val(data[5]);
            $('#item_quantity').val(data[6]);
            $('#staff_id').val(data[9]);

        });
    });
</script>
@endsection