@extends('adminltelayout.layout')

@section('content')

<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0">Manage Supplier & Item!</h1>
      </div><!-- /.col -->
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="#">Home</a></li>
          <li class="breadcrumb-item active">Supplier & Items</li>
        </ol>
      </div><!-- /.col -->
    </div><!-- /.row -->
  </div><!-- /.container-fluid -->
</div>

<div class="container-fluid">

  <ul id="clothing-nav" class="nav nav-tabs" role="tablist">
    <li class="nav-item">
      <a class="nav-link active" href="#items" id="items-tab" role="tab" data-toggle="tab" aria-controls="items" aria-expanded="true">Items</a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="#suppliers" role="tab" id="suppliers-tab" data-toggle="tab" aria-controls="suppliers">Suppliers</a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="#supplier_items" role="tab" id="supplier_items-tab" data-toggle="tab" aria-controls="supplier_items">Supplier Items</a>
    </li>
    </li>
  </ul>

  <!-- Content Panel -->
  <div id="clothing-nav-content" class="tab-content">
    <div role="tabpanel" class="tab-pane fade show active" id="items" aria-labelledby="items-tab">
      <br>
      <div class="col-lg-12">
        <div class="card">
          <div class="card-header">
            <a href="" style="float:right" data-toggle="modal" data-target="#addItem" class="btn btn-success">
              <i class="fa fa-plus"> Item</i>
            </a>
          </div>
          @if($item->isEmpty())
          @else
          <div class="row">
            <div class="card-body">
              <div class="table-responsive">
                <table id="example1" class="table table-striped table-bordered">
                  <thead>
                    <tr>
                      <th style="display:none;" class="text-center" width="15%">Item no.</th>
                      <th class="text-center">Description</th>
                      <th class="text-center">Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      @foreach($item as $items)
                      <input type="hidden" id="delete_item" name="delete_item" />
                      <td class="text-center dept_id" style="display:none;">{{$items->id}}</td>
                      <td class="text-center item_desc">{{$items->item_desc}}</td>
                      <td width="5%">
                        <div class="btn btn-group">
                          <a href="#" class="btn btn-primary btn-sm edit_btn"><i class="fa fa-edit">edit</i></a>
                          <a href="#" class="btn btn-danger btn-sm delete_itembtn" data-id="{{$items->id}}"><i class="fa fa-trash">delete</i></a>
                        </div>
                        <!-- <a href="#" class="btn btn-secondary btn-sm info_btn"><i class="fa fa-info"></i></a> -->

                      </td>
                    </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
            </div>
          </div>
          @endif
        </div>
      </div>
    </div>

    <div role="tabpanel" class="tab-pane fade" id="suppliers" aria-labelledby="suppliers-tab">
      <br>
      <div class="col-lg-12">
        <div class="card">
          <div class="card-header">
            <a href="" style="float:right" data-toggle="modal" data-target="#addSupplier" class="btn btn-success">
              <i class="fa fa-plus"> Supplier</i>
            </a>
          </div>
          @if($supplier->isEmpty())
          @else
          <div class="row">
            <div class="card-body">
              <div class="table-responsive" style="overflow-x:auto;">
                <table id="example2" class="table table-striped table-bordered">
                  <thead>
                    <tr>
                      <th>id</th>
                      <th>Business Name</th>
                      <th>Contact Person</th>
                      <th>Contact Number</th>
                      <th>Email</th>
                      <th>Business Address</th>
                      <th class="text-center">Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      @foreach($supplier as $suppliers)
                      <input type="hidden" id="delete_supplier_id" name="delete_supplier_id" />
                      <td class="dept_id">{{$suppliers->id}}</td>
                      <td class="build_name">{{$suppliers->business_name}}</td>
                      <td class="build_name">{{$suppliers->contact_person}}</td>
                      <td class="build_name">{{$suppliers->contact_no}}</td>
                      <td class="build_name">{{$suppliers->email}}</td>
                      <td class="build_add">{{$suppliers->business_add}}</td>

                      <td width="5%">
                        <div class="btn btn-group">
                          <!-- <a href="#" class="btn btn-secondary btn-sm build_info_btn"><i class="fa fa-info"></i></a> -->
                          <a href="#" class="btn btn-primary btn-sm build_edit_btn"><i class="fa fa-edit">edit</i></a>
                          <a href="#" class="btn btn-danger btn-sm delete_supplierbtn" data-id="{{$suppliers->id}}"><i class="fa fa-trash">delete</i></a>
                        </div>
                      </td>
                    </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
            </div>
          </div>
          @endif
        </div>
      </div>
    </div>

    <div role="tabpanel" class="tab-pane fade" id="supplier_items" aria-labelledby="supplier_items-tab">
      <br>
      <div class="col-lg-12">
        <div class="card">
          <div class="card-header">
            <a href="" style="float:right" data-toggle="modal" data-target="#addSupplierItem" class="btn btn-success">
              <i class="fa fa-plus"> Supplier Items</i>
            </a>
          </div>
          @if($supplieritem->isEmpty())
          @else
          <div class="row">
            <div class="card-body">
              <div class="table-responsive">
                <table id="example3" class="table table-striped table-bordered">
                  <thead>
                    <tr>
                      <th>Id</th>
                      <th>Item Desc</th>
                      <th>Brand</th>
                      <th>Unit</th>
                      <th>Offered Price</th>
                      <th>Business Name</th>
                      <th class="text-center">Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($supplieritem as $supplieritems)
                    <input type="hidden" id="delete_supplier_id" name="delete_supplier_id" />
                    <td class="dept_id">{{$supplieritems->id}}</td>
                    <td class="build_name">{{$supplieritems->item_desc}}</td>
                    <td class="build_name">{{$supplieritems->brand}}</td>
                    <td class="build_name">{{$supplieritems->unit}}</td>
                    <td class="build_name">{{$supplieritems->offered_price}}</td>
                    <td class="build_name">{{$supplieritems->business_name}}</td>
                    <td width="5%">
                      <div class="btn btn-group">
                        <a href="#" class="btn btn-primary btn-sm supplieritem_edit_btn"><i class="fa fa-edit">edit</i></a>
                        <a href="#" class="btn btn-danger btn-sm delete_supplieritem_btn" data-id="{{$supplieritems->id}}"><i class="fa fa-trash">delete</i></a>
                      </div>
                      <!-- <a href="#" class="btn btn-secondary btn-sm build_info_btn"><i class="fa fa-info"></i></a> -->
                    </td>
                    </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
            </div>
          </div>
          @endif
        </div>
      </div>
    </div>
  </div>

  <div role="tabpanel" class="tab-pane fade" id="dropdown-boots" aria-labelledby="dropdown-boots-tab">
    <p>A boot is a type of footwear and a specific type of shoe. Most boots mainly cover the foot and the ankle, while some also cover some part of the lower calf. Some boots extend up the leg, sometimes as far as the knee or even the hip.</p>
  </div>

</div>


</div>

@include('manage_supplier_items.add_supplier')
@include('manage_supplier_items.add_item')
@include('manage_supplier_items.update_supplier')
@include('manage_supplier_items.update_item')
@include('manage_supplier_items.delete_supplier')
@include('manage_supplier_items.delete_item')

@include('manage_supplier_items.add_supplier_items')
@include('manage_supplier_items.update_supplieritem')
@include('manage_supplier_items.delete_supplieritem')



<script>
  $(document).ready(function() {
    $("#example1").DataTable({
      order: [
        [0, 'desc']
      ],
    });

  });
  $(document).ready(function() {
    $("#example2").DataTable({
      order: [
        [0, 'desc']
      ],
    })
  });
  $(document).ready(function() {
    $("#example3").DataTable({
      order: [
        [0, 'desc']
      ],
    })
  });
</script>
@endsection