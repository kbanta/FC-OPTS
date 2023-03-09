@extends('adminltelayout.layout')

@section('content')
<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0">Manage Facility!</h1>
      </div><!-- /.col -->
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="#">Home</a></li>
          <li class="breadcrumb-item active">Building & Department</li>
        </ol>
      </div><!-- /.col -->
    </div><!-- /.row -->
  </div><!-- /.container-fluid -->
</div>

<div class="col-lg-12">
  <ul id="clothing-nav" class="nav nav-tabs" role="tablist">
    <li class="nav-item">
      <a class="nav-link active" href="#building" id="building-tab" role="tab" data-toggle="tab" aria-controls="building" aria-expanded="true">Building</a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="#department" role="tab" id="department-tab" data-toggle="tab" aria-controls="department">Department</a>
    </li>
    </li>
  </ul>
  <div id="clothing-nav-content" class="tab-content">
    <div role="tabpanel" class="tab-pane fade show active" id="building" aria-labelledby="building-tab">
      <br>
      <div class="col-lg-12">
        <div class="card">
          <div class="card-header">
            <a href="" style="float: right;" data-toggle="modal" data-target="#addBuilding" class="btn btn-success">
              <i class="fa fa-plus"> Building</i>
            </a>
          </div>
          <div class="row">
            <div class="card-body">
              <div class="table-responsive">
                <table id="example2" class="table table-striped table-bordered">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>Building</th>
                      <th>Address</th>
                      <th width="18%">Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      @foreach($building as $buildings)
                      <input type="hidden" id="delete_build_id" name="delete_build_id" />
                      <td class="dept_id">{{$buildings->id}}</td>
                      <td class="build_name">{{$buildings->Building_name}}</td>
                      <td class="build_add">{{$buildings->Address}}</td>

                      <td>
                        <div class=" btn btn-group">
                          <a href="#" class="btn btn-primary btn-sm build_edit_btn"><i class="fa fa-edit"> edit</i></a>
                          <a href="#" class="btn btn-danger btn-sm build_delete_btn"><i class="fa fa-trash"> delete </i></a>
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
        </div>
      </div>
    </div>

    <div role="tabpanel" class="tab-pane fade" id="department" aria-labelledby="department-tab">
      <br>
      <div class="col-lg-12">
        <div class="card">
          <div class="card-header">
            <a href="" style="float: right;" data-toggle="modal" data-target="#addDepartment" class="btn btn-success">
              <i class="fa fa-plus"> Departments</i>
            </a>
          </div>
          <div class="row">
            <div class="card-body">
              <div class="table-responsive">
                <table id="example1" class="table table-striped table-bordered">
                  <thead>
                    <tr>
                      <th>Room #</th>
                      <th>Department Name</th>
                      <th>Building</th>
                      <th>Address</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      @foreach($department as $departments)
                      <input type="hidden" id="delete_idd" name="delete_idd" />
                      <td width="10%" class="dept_id">{{$departments->id}}</td>
                      <td class="dept_name">{{$departments->Dept_name}}</td>
                      <td class="build_name">{{$departments->building->Building_name}}</td>
                      <td class="build_add">{{$departments->building->Address}}</td>

                      <td width="18%">
                        <div class="btn btn-group">
                          <a href="#" class="btn btn-primary btn-sm edit_btn">edit <i class="fa fa-edit"></i></a>
                          <a href="#" class="btn btn-danger btn-sm delete_btn">delete <i class="fa fa-trash"></i></a>
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
        </div>
      </div>
    </div>
  </div>
</div>
@include('manage_facility.add_department')
@include('manage_facility.add_building')
@include('manage_facility.update_department')
@include('manage_facility.update_building')
@include('manage_facility.delete_department')
@include('manage_facility.delete_building')
<script>
  $(document).ready(function() {
    $("#example1").DataTable({});

  });
  $(document).ready(function() {
    $("#example2").DataTable({})
  });
</script>
@endsection