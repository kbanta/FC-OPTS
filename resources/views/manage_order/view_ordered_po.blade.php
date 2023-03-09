@extends('adminltelayout.layout')

@section('generate_delivery_no')
<br>
<section class="mt-4 pl-4">
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
@foreach(array_keys($gg) as $key => $outputs)
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Order # : {{$order_info[0]['order_no']}} <i style="color:green;"> <b> </b></i></h3>
        <p style="float: right;">Created Date: {{$order_info[0]['orderDate']}}</p>
        <br>
        <h4 class="card-title">Purchase Request # : {{$order_info[0]['pr_no']}}</h4>
    </div>
</div>
<div class="card col-12">
    <div class="card-header">
        <h3 class="card-title"> <span class="">Item per Supplier</span></h3>

        @php
        $o=$order_info[0]['order_no'];
        $p=$order_info[0]['pr_no'];
        $s=$order_info[0]['supplier_id'];
        @endphp
        @if(Request::path() == "processor/ordered_po/view/$p/$o/$s")
        <div class="btn-group btn-group-sm" role="group" aria-label="..." style="float: right;">
            <button type="button" data-toggle="modal" id="display_none" class="btn btn-warning btn-sm print_btn"> <i class="fa fa-print"> Print</i></button>
            @if(empty($chk_dl2))
            <button type="button" data-toggle="modal" id="display_none" class="btn btn-success btn-sm supp_per_item"> <i class="fa fa-truck"> Input Delivery No.</i></button>
            @elseif($chk_dl2 != $chk_dl)
            <button type="button" data-toggle="modal" id="display_none" class="btn btn-success btn-sm supp_per_item"> <i class="fa fa-truck"> Input Delivery No.</i></button>
            @endif
        </div>
        <!-- <a href="#" style="float:right ;" data-toggle="modal" id="display_none" class="btn btn-success btn-sm supp_per_item">
            <i class="fa fa-truck"> Input Delivery No.</i>
        </a>
        <a href="#" style="float:right ;" class="btn btn-warning btn-sm print_btn" id="print">
            <i class="fa fa-print"> Print</i>
        </a> -->
        @else
        @endif
    </div>
    <div class="card-body p-0">
        <div class="table">
            <table class="table table-bordered table-sm">
                <thead>
                    <tr>
                        <th>Item Description</th>
                        <th>Brand</th>
                        <th style="width: 10px;">Unit</th>
                        <th style="width: 10px;">Quantity</th>
                        <th id="show" style="display: none;">Price</th>
                        <th id="display_none">Delivered</th>
                        <th id="display_none" style="width: 13%;">Status</th>
                        <th id="show" style="display: none;">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                    $total=0;
                    @endphp
                    <tr>
                        @foreach($gg[$outputs] as $key)
                        @php
                        $total += $key['quantity']*$key['price'];
                        $totall = $key['quantity']*$key['price'];
                        $amount= number_format($totall, 2, '.', '');
                        $t= number_format($total, 2, '.', '');
                        @endphp
                        <td class=""><input type="hidden" name="item_desc[]" value="{{$key['item_desc']}}">{{$key['item_desc']}}</td>
                        <td class=""><input type="hidden" name="brand[]" value="{{$key['brand']}}">{{$key['brand']}}</td>
                        <td class=""><input type="hidden" name="unit[]" value="{{$key['unit']}}">{{$key['unit']}}</td>
                        <td class=""><input type="hidden" name="quantity[]" value="{{$key['quantity']}}">{{$key['quantity']}}</td>
                        <td class="" id="show" style="display: none;"><input type="hidden" name="price[]" value="{{$key['price']}}">{{$key['price']}}</td>
                        @if($key['delivered'] == null )
                        <td id="display_none" class=""><input type="hidden" name="delivered[]" value="{{$key['delivered']}}">0</td>
                        @else
                        <td id="display_none" class=""><input type="hidden" name="delivered[]" value="{{$key['delivered']}}">{{$key['delivered']}}</td>
                        @endif
                        @if($key['delivered'] != $key['quantity'])
                        <td id="display_none" class="" style="color: red;"> <i>Incomplete {{$key['delivered']-$key['quantity']}}</i></td>
                        @else
                        <td id="display_none" class="" style="color: green;">Complete</td>
                        @endif
                        <td class="" id="show" style="display: none;">{{$amount}}</td>
                    </tr>
                    @endforeach
                    <tr>
                        <td id="td_col" style="display: none;" colspan="5">
                            <i>
                                <p style="float: right;">Total:</p>
                            </i>
                        </td>
                        <td class="" id="show" style="display: none;">
                            <i><b>₱ {{$t}}</b></i>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <input type="hidden" name="sid[]" value="{{$key['supplier_id']}}">
                            <p><b> Supplier : </b>{{$key['business_name']}}</p>
                        </td>

                        <td colspan="7">
                            <p> <b> Payment Term : </b>{{$key['payment_term']}} </p>
                            <input type="hidden" name="supplier_id[]" id="supplier_id[]" value="{{$key['supplier_id']}}">
                        </td>
                    </tr>
                    </tr>
                </tbody>
            </table>
            @php
            $or = $order_info[0]['order_no'];
            @endphp
            @endforeach
        </div>
        <div id="sig" style="display: none;">
            <br>
            <br>
            <br>
            <br>
            @include('manage_order.po_signatory')
        </div>
    </div>
    <div class="card col-12" id="display_none">
        <div class="card-body p-0">
            <br>
            <div class="card col-12">
                @if(empty($dl_noo))
                @else
                <div class="card-header">
                    <h3 class="card-title"> <b>Delivered by Supplier</b></h3>
                </div>
                @endif
                @foreach(array_keys($dl_noo) as $key => $outputss)
                @if($dl_no[0]['order_no'] == $or)
                <!-- <div class="card-header">
                @foreach($dl_noo[$outputss] as $dl)
                <span class="" style="font-size: 20px;">Delivery No. : {{$dl['delivery_no']}}</span>
                @endforeach -->
                <!-- </div> -->
                <div class="card-body p-0">
                    <!-- <span class="" style="font-size: 18px;"></span> -->
                    <div class="table">
                        <table class="table table-bordered table-sm">
                            <thead>
                                <tr>
                                    <th width="40%">Item Description</th>
                                    <th>Brand</th>
                                    <th>Unit</th>
                                    <th>Delivered</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($dl_noo[$outputss] as $deliver)
                                <tr>
                                    @if(!empty($deliver['item_quantity']))
                                    <td>{{$deliver['item_desc']}}</td>
                                    <td>{{$deliver['item_brand']}}</td>
                                    <td>{{$deliver['item_unit']}}</td>
                                    <td>{{$deliver['item_quantity']}}</td>
                                    @else
                                    @endif
                                </tr>
                                @endforeach
                                <tr>
                                    <td colspan="1">
                                        <span> <i> <b> Delivery No. </b> : {{$deliver['delivery_no']}} </i></span>
                                    </td>
                                    <td colspan="5">
                                        <span><i>Delivered Date : {{date('Y-m-d H:i:s' ,strtotime($deliver['created_at']))}}</i></span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        @else

                        @endif
                        @endforeach
                        <div class="modal-footer">
                            @yield('forward')
                            <!-- @if(empty($chk_dl))
                        @elseif(empty($chk_dl2))
                        @elseif( $chk_dl == $chk_dl2)
                        <a href="#" data-toggle="modal" data-target="#forwardall" class="btn btn-warning "> <i class="fa fa-share"> Send for Approval</i></a> -->
                        </div>
                        <!-- @else -->
                        <!-- @endif -->
                    </div>
                </div>
            </div>
            @include('manage_order.generate_delivery_no')
            @include('manage_delivery.forward_all_delivery_modal')

            <script type="text/javascript">
                $().ready(function() {
                    $('.supp_per_item').on('click', function() {
                        $('#generateDeliveryNo').modal('show');
                    });
                });
            </script>
            <script>
                $('.print_btn').click(function() {
                    window.print();
                    return false;
                });
                // window.onbeforeprint = function() {
                //     var cell = document.getElementById("td_col");
                //     cell.setAttribute("colspan", "5");
                // };
                // window.onafterprint = function() {
                //     var cell = document.getElementById("td_col");
                //     cell.setAttribute("colspan", "7");
                // };
            </script>
            <style>
                @media print {
                    #print {
                        display: none;
                    }

                    #display_none {
                        display: none;
                    }

                    #sig {
                        display: block !important;
                    }

                    #show {
                        display: table-cell !important;
                    }

                    #td_col {
                        display: table-cell !important;
                    }
                }
            </style>
            @endsection