@extends('adminltelayout.layout')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">View Reported Item</h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item">Home</li>
                    <li class="breadcrumb-item active">Reported Item</li>
                </ol>
            </div><!-- /.col -->
        </div><!-- /.row -->
    </div><!-- /.container-fluid -->
</div>

<div class="col-lg-12">
    <div class="card">
        <div class="forbes-logo-col" style="width:100%; height:auto">
            <section class="mt-4 pl-4 mr-4">
                <!-- <span class="badge badge-danger" style="font-size: 20px; float:right;">
                Reported Item
                </span> -->
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
                <form id="forwardForm">
                    {{csrf_field()}}
                    {{method_field('PUT')}}
                    <div>
                        <div class="">
                            <span class="badge badge-danger" style="font-size: 20px;">Reported Item</span>
                            <hr class="divider" style="background-color: currentColor;">
                            <p>Reported By : <span>{{$reported[0]['fname']}} {{$reported[0]['mname']}} {{$reported[0]['lname']}}</span></p>
                            <p>Date: <span>{{date('Y-m-d' ,strtotime($reported[0]['created_at']))}}</span> </p>
                            <!-- <hr class="divider" style="background-color: currentColor;"> -->
                            <table class="table table-bordered table-sm">
                                <thead>
                                    <tr>
                                        <th class="text-center">Quantity</th>
                                        <th class="text-center">Particulars</th>
                                        <th class="text-center">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($reported as $report)
                                    <tr>
                                        <td class="text-center" style="display: none;">{{$report['id']}}</td>
                                        <td class="text-center">{{$report['quantity']}}</td>
                                        <td class="text-center">{{$report['item_desc']}} {{$report['item_brand']}}</td>
                                        <td class="text-center">{{$report['item_status']}}</td>
                                        @endforeach
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </form>
                <br>
                <br>
            </section>
        </div>
    </div>
</div>
@endsection