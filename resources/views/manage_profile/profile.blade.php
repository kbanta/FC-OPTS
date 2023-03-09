@extends('adminltelayout.layout')

@section('content')
<div class="content-header">
	<div class="container-fluid">
		<div class="row mb-2">
			<div class="col-sm-6">
				<h1 class="m-0">Manage Profile!</h1>
			</div><!-- /.col -->
			<div class="col-sm-6">
				<ol class="breadcrumb float-sm-right">
					<li class="breadcrumb-item"><a href="#">Home</a></li>
					<li class="breadcrumb-item active">Profile</li>
				</ol>
			</div><!-- /.col -->
		</div><!-- /.row -->
	</div><!-- /.container-fluid -->
</div>
<div class="container">
	<div class="row gutters">
		<div class="col-xl-3 col-lg-3 col-md-12 col-sm-12 col-12">
			<div class="card h-100">
				<div class="card-body">
					<div class="account-settings">
						<div class="user-profile">
							@if(Auth::user()->hasRole('Administrator'))
							<div class="user-avatar">
								<img class="admin-profile_pic" src="{{ Auth::user()->picture }} " alt="Maxwell Admin">
							</div>
							<div class="mt-2">
								<input type="file" name="admin-profile_pic" id="admin-profile_pic" style="opacity: 0;height:1px;display:none">
								<a href="#" class="btn btn-primary" id="change_pro_pic">
									<i class="fa fa-fw fa-camera"></i>
									<span>Change Photo</span>
								</a>
							</div>
							@endif
							@if(Auth::user()->hasRole('Requestor'))
							<div class="user-avatar">
								<img class="requestor-profile_pic" src="{{ Auth::user()->picture }} " alt="Maxwell Admin">
							</div>
							<div class="mt-2">
								<input type="file" name="requestor-profile_pic" id="requestor-profile_pic" style="opacity: 0;height:1px;display:none">
								<a href="#" class="btn btn-primary" id="change_pro_pic">
									<i class="fa fa-fw fa-camera"></i>
									<span>Change Photo</span>
								</a>
							</div>
							@endif
							@if(Auth::user()->hasRole('Validator'))
							<div class="user-avatar">
								<img class="validator-profile_pic" src="{{ Auth::user()->picture }} " alt="Maxwell Admin">
							</div>
							<div class="mt-2">
								<input type="file" name="validator-profile_pic" id="validator-profile_pic" style="opacity: 0;height:1px;display:none">
								<a href="#" class="btn btn-primary" id="change_pro_pic">
									<i class="fa fa-fw fa-camera"></i>
									<span>Change Photo</span>
								</a>
							</div>
							@endif
							@if(Auth::user()->hasRole('Approver'))
							<div class="user-avatar">
								<img class="approver-profile_pic" src="{{ Auth::user()->picture }} " alt="Maxwell Admin">
							</div>
							<div class="mt-2">
								<input type="file" name="approver-profile_pic" id="approver-profile_pic" style="opacity: 0;height:1px;display:none">
								<a href="#" class="btn btn-primary" id="change_pro_pic">
									<i class="fa fa-fw fa-camera"></i>
									<span>Change Photo</span>
								</a>
							</div>
							@endif
							@if(Auth::user()->hasRole('Processor'))
							<div class="user-avatar">
								<img class="processor-profile_pic" src="{{ Auth::user()->picture }} " alt="Maxwell Admin">
							</div>
							<div class="mt-2">
								<input type="file" name="processor-profile_pic" id="processor-profile_pic" style="opacity: 0;height:1px;display:none">
								<a href="#" class="btn btn-primary" id="change_pro_pic">
									<i class="fa fa-fw fa-camera"></i>
									<span>Change Photo</span>
								</a>
							</div>
							@endif
							<br>
							<h5 class="user-name">{{Auth::user()->name }}</h5>
							<h6 class="user-email">{{Auth::user()->email }}</h6>
							<h6 class="user-email">{{Auth::user()->id }}</h6>

						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-xl-9 col-lg-9 col-md-12 col-sm-12 col-12">
			<div class="card h-100">
				<form class="form">
					<div class="card-body">
						<div class="row gutters">
							<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
								<div class="text-right">
									<button type="button" id="submit" name="submit" class="btn btn-success update_edit_btn"><i class="fa fa-edit"> Edit Personal Details</i></button>
									<button type="button" id="submit" name="submit" class="btn btn-success update_account_btn"><i class="fas fa-key"> Edit Password</i></button>
								</div>
							</div>
						</div>
						<div class="row gutters">
							<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
								<h6 class="mb-2 text-primary">Personal Details</h6>
							</div>
							<div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
								<div class="form-group">
									<label for="fname">First Name</label>
									<input type="text" class="form-control" name="fname" id="fname" value="{{Auth::user()->name}}" readonly>
								</div>
							</div>
							<div class="col-xl-3 col-lg-3 col-md-3 col-sm-3 col-12">
								<div class="form-group">
									<label for="mname">Middle Name</label>
									@if(empty($userr->mname))
									<input type="text" class="form-control" name="mname" id="mname" value="--" readonly>
									@else
									<input type="text" class="form-control" name="mname" id="mname" value="{{$userr->mname}}" readonly>
									@endif
								</div>
							</div>
							<div class="col-xl-3 col-lg-3 col-md-3 col-sm-3 col-12">
								<div class="form-group">
									<label for="lname">Last Name</label>
									@if(empty($userr->lname))
									<input type="text" class="form-control" name="lname" id="lname" value="--" readonly>
									@else
									<input type="text" class="form-control" name="lname" id="lname" value="{{$userr->lname}}" readonly>
									@endif
								</div>
							</div>
							<div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
								<div class="form-group">
									<label for="sex">Sex</label>
									@if(empty($userr->sex))
									<input type="text" class="form-control" name="sex" id="sex" value="--" readonly>
									@else
									<input type="text" class="form-control" name="sex" id="sex" value="{{$userr->sex}}" readonly>
									@endif
								</div>
							</div>
							<div class="col-xl-8 col-lg-8 col-md-8 col-sm-8 col-12">
								<div class="form-group">
									<label for="contact_no">Contact Number</label>
									@if(empty($userr->contact_no))
									<div class="input-group mb-3">
										<div class="input-group-prepend">
											<span class="input-group-text" id="inputGroup-sizing-default">+63</span>
										</div>
										<input type="text" class="form-control" name="contact_no" id="contact_no" value="--" readonly>
									</div>
									@else
									<div class="input-group mb-3">
										<div class="input-group-prepend">
											<span class="input-group-text" id="inputGroup-sizing-default">+63</span>
										</div>
										<input type="text" class="form-control" name="contact_no" id="contact_no" value="{{$userr->contact_no}}" readonly>
									</div>
									@endif
								</div>
							</div>
						</div>
						<div class="row gutters">
							<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
								<h6 class="mt-3 mb-2 text-primary">Address</h6>
							</div>
							<div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
								<div class="form-group">
									<label for="barangay">Barangay</label>
									@if(empty($userr->barangay))
									<input type="text" class="form-control" name="barangay" id="barangay" value="--" readonly>
									@else
									<input type="text" class="form-control" name="barangay" id="barangay" value="{{$userr->barangay}}" readonly>
									@endif
								</div>
							</div>
							<div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
								<div class="form-group">
									<label for="municipality">Municipality</label>
									@if(empty($userr->municipality))
									<input type="text" class="form-control" name="barangay" id="municipality" value="--" readonly>
									@else
									<input type="text" class="form-control" name="barangay" id="municipality" value="{{$userr->municipality}}" readonly>
									@endif
								</div>
							</div>
							<div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
								<div class="form-group">
									<label for="city">City</label>
									@if(empty($userr->city))
									<input type="text" class="form-control" name="city" id="city" value="--" readonly>
									@else
									<input type="text" class="form-control" name="city" id="city" value="{{$userr->city}}" readonly>
									@endif
								</div>
							</div>
							<div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
								<div class="form-group">
									<label for="province">Province</label>
									@if(empty($userr->province))
									<input type="text" class="form-control" name="province" id="province" value="--" readonly>
									@else
									<input type="text" class="form-control" name="province" id="province" value="{{$userr->province}}" readonly>
									@endif
								</div>
							</div>
							<div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12" readonly>
								<div class="form-group">
									<label for="zipcode">Zip Code</label>
									@if(empty($userr->zipcode))
									<input type="text" class="form-control" name="zipcode" id="zipcode" value="--" readonly>
									@else
									<input type="text" class="form-control" name="zipcode" id="zipcode" value="{{$userr->zipcode}}" readonly>
									@endif
								</div>
							</div>
						</div>

						<!-- accout settings -->
						<!-- <div class="row gutters">
							<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
								<div class="text-right">
									<button type="button" id="submit" name="submit" class="btn btn-success update_account_btn"><i class="fa fa-edit">Edit Account Details</i></button>
								</div>
							</div>
						</div> -->
						<div class="row gutters">
							<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
								<h6 class="mt-3 mb-2 text-primary">Account</h6>
							</div>
							<div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
								<div class="form-group">
									<label for="Dept_name">Department</label>
									@if(empty($userr->Dept_name))
									<input type="text" class="form-control" name="Dept_name" id="Dept_name" value="--" readonly>
									@else
									<input type="text" class="form-control" name="Dept_name" id="Dept_name" value="{{$userr->Dept_name}}" readonly>
									@endif
								</div>
							</div>
							<div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
								<div class="form-group">
									<label for="position">Position</label>
									<input type="text" class="form-control" name="position" id="position" value="{{Auth::user()->position}}" readonly>
								</div>
							</div>
							<div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
								<div class="form-group">
									<label for="sTate">Role</label>
									<input type="text" class="form-control" id="sTate" value="{{$user->display_name}}" readonly>
								</div>
							</div>
							<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
								<div class="form-group">
									<label for="email">Email</label>
									<input type="email" class="form-control" name="email" id="email" value="{{Auth::user()->email}}" readonly>
								</div>
							</div>
						</div>
						@if(Auth::user()->hasRole('Administrator'))
						<input type="hidden" name="role" id="role" value="Administrator">
						@endif
						@if(Auth::user()->hasRole('Requestor'))
						<input type="hidden" name="role" id="role" value="Requestor">
						@endif
						@if(Auth::user()->hasRole('Validator'))
						<input type="hidden" name="role" id="role" value="Validator">
						@endif
						@if(Auth::user()->hasRole('Approver'))
						<input type="hidden" name="role" id="role" value="Approver">
						@endif
						@if(Auth::user()->hasRole('Processor'))
						<input type="hidden" name="role" id="role" value="Processor">
						@endif
						@if(Auth::user()->hasRole('CorporateTreasurer'))
						<input type="hidden" name="role" id="role" value="CorporateTreasurer">
						@endif
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
@include('manage_profile.update_profile')
@include('manage_profile.update_account')
<script type="text/javascript">
	$(document).on('click', '#change_pro_pic', function() {
		$('#admin-profile_pic').click();
		$('#requestor-profile_pic').click();
		$('#approver-profile_pic').click();
		$('#validator-profile_pic').click();
		$('#processor-profile_pic').click();
		var role = $('#role').val();
		console.log(role);

		//   alert(role); 
	});
	$('#admin-profile_pic').ijaboCropTool({
		preview: '.admin-profile_pic',
		setRatio: 1,
		allowedExtensions: ['jpg', 'jpeg', 'png'],
		buttonsText: ['CROP', 'QUIT'],
		buttonsColor: ['#30bf7d', '#ee5155', -15],
		processUrl: '{{ route("adminProfilePic") }}',
		withCSRF: ['_token', '{{ csrf_token() }}'],
		onSuccess: function(message, element, status) {
			//  alert(message);
			Swal.fire({
				icon: 'success',
				title: message,
				showConfirmButton: false,
				timer: 3500
			});
		},
		onError: function(message, element, status) {
			alert(message);
		}
	});
	$('#requestor-profile_pic').ijaboCropTool({
		preview: '.requestor-profile_pic',
		setRatio: 1,
		allowedExtensions: ['jpg', 'jpeg', 'png'],
		buttonsText: ['CROP', 'QUIT'],
		buttonsColor: ['#30bf7d', '#ee5155', -15],
		processUrl: '{{ route("requestorProfilePic") }}',
		withCSRF: ['_token', '{{ csrf_token() }}'],
		onSuccess: function(message, element, status) {
			//  alert(message);
			Swal.fire({
				icon: 'success',
				title: message,
				showConfirmButton: false,
				timer: 3500
			});
		},
		onError: function(message, element, status) {
			alert(message);
		}
	});
	$('#validator-profile_pic').ijaboCropTool({
		preview: '.validator-profile_pic',
		setRatio: 1,
		allowedExtensions: ['jpg', 'jpeg', 'png'],
		buttonsText: ['CROP', 'QUIT'],
		buttonsColor: ['#30bf7d', '#ee5155', -15],
		processUrl: '{{ route("validatorProfilePic") }}',
		withCSRF: ['_token', '{{ csrf_token() }}'],
		onSuccess: function(message, element, status) {
			//  alert(message);
			Swal.fire({
				icon: 'success',
				title: message,
				showConfirmButton: false,
				timer: 3500
			});
		},
		onError: function(message, element, status) {
			alert(message);
		}
	});
	$('#approver-profile_pic').ijaboCropTool({
		preview: '.approver-profile_pic',
		setRatio: 1,
		allowedExtensions: ['jpg', 'jpeg', 'png'],
		buttonsText: ['CROP', 'QUIT'],
		buttonsColor: ['#30bf7d', '#ee5155', -15],
		processUrl: '{{ route("approverProfilePic") }}',
		withCSRF: ['_token', '{{ csrf_token() }}'],
		onSuccess: function(message, element, status) {
			//  alert(message);
			Swal.fire({
				icon: 'success',
				title: message,
				showConfirmButton: false,
				timer: 3500
			});
		},
		onError: function(message, element, status) {
			alert(message);
		}
	});
	$('#processor-profile_pic').ijaboCropTool({
		preview: '.processor-profile_pic',
		setRatio: 1,
		allowedExtensions: ['jpg', 'jpeg', 'png'],
		buttonsText: ['CROP', 'QUIT'],
		buttonsColor: ['#30bf7d', '#ee5155', -15],
		processUrl: '{{ route("processorProfilePic") }}',
		withCSRF: ['_token', '{{ csrf_token() }}'],
		onSuccess: function(message, element, status) {
			//  alert(message);
			Swal.fire({
				icon: 'success',
				title: message,
				showConfirmButton: false,
				timer: 3500
			});
		},
		onError: function(message, element, status) {
			alert(message);
		}
	});
</script>
@endsection

<style>
	body {
		margin: 0;
		color: #2e323c;
		background: #f5f6fa;
		padding-bottom: 40px;
		position: relative;
		height: 100%;
	}

	.account-settings .user-profile {
		margin: 0 0 1rem 0;
		padding-bottom: 1rem;
		text-align: center;
	}

	.account-settings .user-profile .user-avatar {
		margin: 0 0 1rem 0;
	}

	.account-settings .user-profile .user-avatar img {
		width: 90px;
		height: 90px;
		-webkit-border-radius: 100px;
		-moz-border-radius: 100px;
		border-radius: 100px;
	}

	.account-settings .user-profile h5.user-name {
		margin: 0 0 0.5rem 0;
	}

	.account-settings .user-profile h6.user-email {
		margin: 0;
		font-size: 0.8rem;
		font-weight: 400;
		color: #9fa8b9;
	}

	.account-settings .about {
		margin: 2rem 0 0 0;
		text-align: center;
	}

	.account-settings .about h5 {
		margin: 0 0 15px 0;
		color: #007ae1;
	}

	.account-settings .about p {
		font-size: 0.825rem;
	}

	.form-control {
		border: 1px solid #cfd1d8;
		-webkit-border-radius: 2px;
		-moz-border-radius: 2px;
		border-radius: 2px;
		font-size: .825rem;
		background: #ffffff;
		color: #2e323c;
	}

	.card {
		background: #ffffff;
		-webkit-border-radius: 5px;
		-moz-border-radius: 5px;
		border-radius: 5px;
		border: 0;
		margin-bottom: 1rem;
	}
</style>