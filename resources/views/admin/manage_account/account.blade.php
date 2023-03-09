@extends('adminltelayout.layout')

@section('content')

<!-- Content Header (Page header) -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Manage Acounts!</h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                    <li class="breadcrumb-item active">Accounts</li>
                </ol>
            </div><!-- /.col -->
        </div><!-- /.row -->
    </div><!-- /.container-fluid -->
</div>
<!-- /.content-header -->

<div class="col-lg-12">
    <div class="card">
        <div class="card-header">
            <a href="#" style="float: right;" data-toggle="modal" data-target="#myModal" class="btn btn-success">
                <i class="fa fa-user-plus"> Create User</i>
            </a>
        </div>
        @if($users->isEmpty())
        @else
        <div class="row">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="table1" class="table table-responsive table-striped table-bordered">
                        <thead>
                            <tr>
                                <th style="display: none;">#</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Position</th>
                                <th>Department</th>
                                <th>Status</th>
                                <th width="15%">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                @foreach($users as $users)
                                <input type="hidden" id="delete_id" name="delete_id" />
                                <td class="class_id" style="display: none;">{{$users->id}}</td>
                                <td class="class_name">{{$users->name}}</td>
                                <td class="class_email">{{$users->email}}</td>
                                @foreach($users->roles as $role)
                                <td class="class_role">{{$role->display_name}}</td>
                                @endforeach
                                <td class="class_email">{{$users->position}}</td>
                                <td class="class_email">{{$users->Dept_name}}</td>
                                @if($users->isActive == 1)
                                <td class="class_email"><span class="btn-block badge badge-success" style="font-size: 15px;">Active</span></td>
                                @else
                                <td class="class_email"><span class="btn-block badge badge-secondary" style="font-size: 15px;">Deactivate</span></td>
                                @endif
                                <td>
                                    <div class="btn-group">
                                        <!-- <a href="#" class="btn btn-secondary btn-sm viewbtn"><i class="fa fa-info"></i></a> -->
                                        <a href="#" class="btn btn-primary btn-sm editbtn"><i class="fa fa-edit"> Update</i></a>
                                        <a href="#" class="btn btn-danger btn-sm deletebtn" data-id="{{$users->id}}"><i class="fa fa-trash"> Delete</i></a>
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
@include('admin.manage_account.create')
@include('admin.manage_account.view')
@include('admin.manage_account.update')
@include('admin.manage_account.delete')
<script>
    $(document).ready(function() {
        $("#table1").DataTable({
            order: [
                [0, 'desc']
            ],
            // responsive: true,
            // pageLength: 200
        });
    });
</script>
@endsection