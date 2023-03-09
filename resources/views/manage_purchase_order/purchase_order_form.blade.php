@extends('adminltelayout.layout')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">View PO</h1>
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
                <div style="float: right;" id="printPageButton">
                    @if(Auth::user()->hasRole('Administrator'))
                    @if($po_output[0]['user_id'] == Auth::user()->id)
                    <a href="{{route('ad_po_track',$pr_no = $po_output[0]['pr_no'] )}}" class="btn btn-danger btn-sm view_btn"><i class="fa fa-eye"></i> Track Purchase Order</a>
                    @else
                    @endif
                    @endif
                    @if(Auth::user()->hasRole('Requestor'))
                    @if($po_output[0]['user_id'] == Auth::user()->id)
                    <a href="{{route('req_po_track',$pr_no = $po_output[0]['pr_no'] )}}" class="btn btn-danger btn-sm view_btn"><i class="fa fa-eye"></i> Track Purchase Order</a>
                    @else
                    @endif
                    @endif
                    @if(Auth::user()->hasRole('Processor'))
                    @if($po_output[0]['user_id'] == Auth::user()->id)
                    <a href="{{route('pro_po_track',$pr_no = $po_output[0]['pr_no'] )}}" class="btn btn-danger btn-sm view_btn"><i class="fa fa-eye"></i> Track Purchase Order</a>
                    @else
                    @endif
                    @endif
                    @if(Auth::user()->hasRole('Validator'))
                    @if($po_output[0]['user_id'] == Auth::user()->id)
                    <a href="{{route('val_po_track',$pr_no = $po_output[0]['pr_no'] )}}" class="btn btn-danger btn-sm view_btn"><i class="fa fa-eye"></i> Track Purchase Order</a>
                    @else
                    @endif
                    @endif
                    @if(Auth::user()->hasRole('Approver'))
                    @if($po_output[0]['user_id'] == Auth::user()->id)
                    <a href="{{route('app_po_track',$pr_no = $po_output[0]['pr_no'] )}}" class="btn btn-danger btn-sm view_btn"><i class="fa fa-eye"></i> Track Purchase Order</a>
                    @else
                    @endif
                    @endif
                </div>
                <!-- <span class="badge badge-danger" style="font-size: 20px; float:right;">{{ $output[0]['status'] }}</span> -->
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
                                <p class="mb-0">E. Aquende Bldg. III Rizal Corner. Elizondo St. Legazpi City</p>
                                <div class="text-muted"><small>4500, Philippines</small></div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <form id="purchase_orderForm">
                {{csrf_field()}}
                {{method_field('PUT')}}
                <section class="p-2">
                    <!-- TABLE: LATEST ORDERS -->
                    <div class="card">
                        <div class="card-header">
                            <input type="hidden" id="po_no" name="po_no" value="{{$po_output[0]['po_no']}}">
                            <input type="hidden" id="id" name="id" value="{{$user->id}}">
                            <input type="hidden" id="position" name="position" value="{{$user->position}}">
                            <input type="hidden" id="pr_no" name="pr_no" value="{{$po_output[0]['pr_no']}}">
                            <h3 class="card-title">Purchase Order # : <i style="color:green;"> <b> {{$po_output[0]['po_no']}} </b></i></h3>
                            <p style="float: right;">Created Date: {{$po_output[0]['createdDate']}}</p>
                            <br>
                            <h4 class="card-title">Purchase Request # : {{$po_output[0]['pr_no']}}</h4>
                        </div>
                    </div>
                    @php
                    $pr=$po_output[0]['pr_no'];
                    @endphp
                    @if(Request::path()=='processor/po_prepared/view/$pr')
                    <div class="card col-8">
                        <div class="card-header">
                            <h3 class="card-title">Purposed of Requisition : {{$po_output[0]['purpose']}}</h3>
                        </div>
                    </div>
                    @else
                    @endif
                    <!-- @if(Auth::user()->hasRole('Validator'))
                    <div class="card col-6">
                        <div class="card-header">
                            @if(!empty($po_output[0]['paymentTerm']))
                            <h3 class="card-title"> Payment Term : {{$po_output[0]['paymentTerm']}}</h3>
                            @else
                            <label class="text-muted">
                                <span class="">Payment Term :</span>
                            </label>
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
                    @endif -->
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
                            <h3 class="card-title"> <span class="">Suppliers and Items</span></h3>
                        </div>
                        <div class="card-body p-0">
                            <div class="table">
                                <table class="table table-bordered table-sm">
                                    <thead>
                                        <tr>
                                            <th>Supplier</th>
                                            <!-- <th>Email</th> -->
                                            <!-- <th>Contact No.</th> -->
                                            <th>Item Description</th>
                                            <th>Brand</th>
                                            <th style="width: 10px;">Unit</th>
                                            <th style="width: 10px;">Quantity</th>
                                            <th>Price</th>
                                            <th style="width: 13%;">Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                        $total=0;
                                        @endphp

                                        @foreach($po_output as $outputs)
                                        @php
                                        $total += $outputs['quantity']*$outputs['offered_price'];
                                        $totall = $outputs['quantity']*$outputs['offered_price'];
                                        $amount= number_format($totall, 2, '.', '');
                                        $t= number_format($total, 2, '.', '');
                                        @endphp
                                        <tr>
                                            @if(Auth::user()->hasRole('Processor'))
                                            <td class="" name="business_name[]" value="{{$outputs['business_name']}}"><a href="#" class="supp">{{$outputs['business_name']}}</a></td>
                                            @else
                                            <td class="" name="business_name[]" value="{{$outputs['business_name']}}">{{$outputs['business_name']}}</td>
                                            @endif
                                            <td style="display: none;" class="" name="business_name[]" value="{{$outputs['email']}}">{{$outputs['email']}}</td>
                                            <td style="display: none;" class="" name="business_name[]" value="{{$outputs['contact_no']}}">{{$outputs['contact_no']}}</td>
                                            <td style="display: none;" class="" name="business_name[]" value="{{$outputs['contact_no']}}">{{$outputs['business_add']}}</td>
                                            <td class="" name="item_desc[]" value="{{$outputs['item_desc']}}">{{$outputs['item_desc']}}</td>
                                            <td class="" name="brand[]" value="{{$outputs['brand']}}">{{$outputs['brand']}}</td>
                                            <td class="" name="unit[]" value="{{$outputs['unit']}}">{{$outputs['unit']}}</td>
                                            <td class="" name="quantity[]" value="{{$outputs['quantity']}}">{{$outputs['quantity']}}</td>
                                            <td class="" name="price[]" value="{{$outputs['offered_price']}}">{{$outputs['offered_price']}}</td>
                                            <td class="" id="total" name="price[]" value="{{$outputs['quantity']*$outputs['offered_price']}}">{{$amount}}</td>
                                        </tr>
                                        @endforeach

                                        <tr>
                                            <td colspan="6">
                                                <i>
                                                    <p style="float: right;">Total:</p>
                                                </i>
                                            </td>
                                            <td class="">
                                                <i><b>₱ {{$t}}</b></i>
                                            </td>
                                        </tr>
                                        </tr>
                                    </tbody>

                                </table>
                                <br>
                                <br>
                                <br>
                                @if(!empty($approvedBy[0]['approvedBy']))
                                <div class="row">
                                    <div class="col-xs-2 col-sm-6 col-md-4 col-lg-4 col-xl-4">
                                        <div class="" style="text-align: center;">
                                            @if(!empty($preparedBy[0]['preparedBy']))
                                            <h5><i class="" style="color: green;"></i> {{$preparedBy[0]['fname']}} {{$preparedBy[0]['mname']}}. {{$preparedBy[0]['lname']}}</h5>
                                            <p>{{$preparedBy[0]['position']}}</p>
                                            @else
                                            <h5>{{$pb[0]['fname']}} {{$pb[0]['mname']}}. {{$pb[0]['lname']}}</h5>
                                            <i>
                                                <p>waiting for approval</p>
                                            </i>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-xs-2 col-sm-6 col-md-4 col-lg-4 col-xl-4">
                                        <div class="" style="text-align: center;">
                                            @if(!empty($verifiedBy[0]['verifiedBy']))
                                            <h5><i class="" style="color: green;"></i> {{$verifiedBy[0]['fname']}} {{$verifiedBy[0]['mname']}}. {{$verifiedBy[0]['lname']}}</h5>
                                            <p>{{$verifiedBy[0]['position']}}</p>
                                            @else
                                            <h5>Finance Manager</h5>
                                            <i>
                                                <p>waiting for approval</p>
                                            </i>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-xs-2 col-sm-6 col-md-4 col-lg-4 col-xl-4">
                                        <div class="" style="text-align: center;">
                                            @if(!empty($approvedBy[0]['approvedBy']))
                                            <h5><i class="" style="color: green;"></i> {{$approvedBy[0]['fname']}} {{$approvedBy[0]['mname']}}. {{$approvedBy[0]['lname']}}</h5>
                                            <p>{{$approvedBy[0]['position']}}</p>
                                            @else
                                            <h5>ASSD Manager</h5>
                                            <i>
                                                <p>waiting for approval</p>
                                            </i>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <br>
                                <br>
                                <p style="page-break-after: always;"></p>
                                @else
                                @endif
                            </div>
                        </div>
                        <!-- <div class="modal-footer"> -->
                        <!-- <button type="button" class="btn btn-primary save_po">Save</button> -->
                        @if(Auth::user()->hasRole('Processor'))
                        @if(!empty($verifiedBy[0]['verifiedBy']) or !empty($preparedBy[0]['preparedBy']) or !empty($approvedBy[0]['approvedBy']) or !empty($approvedBy[0]['approved2By']) or !empty($approvedBy[0]['approved3By']))
                        @else
                        @yield('prepare')

                        @endif
                        @endif

                        @if(Auth::user()->hasRole('Validator') || Auth::user()->hasRole('Approver'))
                        @yield('verify')
                        @endif

                        @if(!empty($approvedBy[0]['approved2By']))
                        @else
                        @if($user->position=="Corporate Treasurer")
                        <div class="modal-footer">
                            @yield('approveCT')
                        </div>
                        @endif
                        @endif

                        @if(!empty($approvedBy[0]['approved3By']))
                        <div id="printPageButton" class="modal-footer">
                            <div class="btn-group">
                            </div>
                        </div>
                        @else
                        @if($user->position=="Chief Executive Officer")
                        @yield('approveCEO')
                        @endif
                        @endif

                    </div>
                    @if(!empty($approvedBy[0]['approvedBy']))
                    @yield('print_per_supp')
                    @yield('order')
                    @yield('generate_delivery_no')
                    @else
                    @if($user->position=="ASSD Manager")
                    @yield('approve')
                    @endif
                    @endif
        </div>
        </section>
        </form>
    </div>
</div>
</div>
@include('manage_purchase_order.supplierdetails')
@endsection