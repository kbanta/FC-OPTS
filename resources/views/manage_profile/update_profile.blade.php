<!-- Modal-->

<div id="updateProfileModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
  <div role="document" class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h5 id="exampleModalLabel" class="modal-title">Edit Profile</h5>
        <button type="button" data-dismiss="modal" aria-label="Close" class="close" id="closeButton"><span aria-hidden="true">×</span></button>
      </div>
      <div class="modal-body">
        <form id="update_ProfileForm">
          {{csrf_field()}}
          {{method_field('PUT')}}
          <div class="card-body">
            <div class="row gutters">
              <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                <h6 class="mb-2 text-primary">Personal Details</h6>
              </div>
              <input autocomplete='off' type="hidden" class="form-control" id="profile_id" name="profile_id" value="{{Auth::user()->id}}">
              <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                <div class="form-group">
                  <label for="fname">First Name</label>
                  <input autocomplete='off' type="text" class="form-control" name="fname" id="fname" value="{{Auth::user()->name}}">
                  <span class="glyphicon glyphicon-user form-control-feedback"></span>
                  <span class="text-danger">
                    <strong id="fname-error"></strong>
                  </span>
                </div>
              </div>
              <div class="col-xl-3 col-lg-3 col-md-3 col-sm-3 col-12">
                <div class="form-group">
                  <label for="mname">Middle Name</label>
                  @if(empty($userr->mname))
                  <input autocomplete='off' type="text" class="form-control" name="mname" id="mname" placeholder="--">
                  @else
                  <input autocomplete='off' type="text" class="form-control" name="mname" id="mname" value="{{$userr->mname}}">
                  @endif
                  <span class="glyphicon glyphicon-user form-control-feedback"></span>
                  <span class="text-danger">
                    <strong id="mname-error"></strong>
                  </span>
                </div>
              </div>
              <div class="col-xl-3 col-lg-3 col-md-3 col-sm-3 col-12">
                <div class="form-group">
                  <label for="lname">Last Name</label>
                  @if(empty($userr->lname))
                  <input autocomplete='off' type="text" class="form-control" name="lname" id="lname" placeholder="--">
                  @else
                  <input autocomplete='off' type="text" class="form-control" name="lname" id="lname" value="{{$userr->lname}}">
                  @endif
                  <span class="glyphicon glyphicon-user form-control-feedback"></span>
                  <span class="text-danger">
                    <strong id="lname-error"></strong>
                  </span>
                </div>
              </div>
              <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                <div class="form-group">
                  <label for="sex">Sex</label>
                  @if(empty($userr->sex))
                  <select name="sex" id="sex" class="form-control">
                    <option value="" disabled selected hidden>Select Sex</option>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                  </select>
                  @else
                  <select name="sex" id="sex" class="form-control">
                    <option value="{{$userr->sex}}" hidden>{{$userr->sex}}</option>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>

                  </select>
                  @endif
                  <span class="glyphicon glyphicon-user form-control-feedback"></span>
                  <span class="text-danger">
                    <strong id="sex-error"></strong>
                  </span>
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
                    <input type="tel" class="form-control" name="contact_no" id="contact_no" placeholder="--">
                  </div>
                  @else
                  <div class="input-group mb-3">
                    <div class="input-group-prepend">
                      <span class="input-group-text" id="inputGroup-sizing-default">+63</span>
                    </div>
                    <input autocomplete='off' type="text" class="form-control" name="contact_no" id="contact_no" value="{{$userr->contact_no}}">
                  </div>
                  @endif
                  <span class="glyphicon glyphicon-user form-control-feedback"></span>
                  <span class="text-danger">
                    <strong id="contact_no-error"></strong>
                  </span>
                </div>
              </div>
              <!-- <div class="col-xl-5 col-lg-5 col-md-5 col-sm-5 col-12">
                <div class="form-group">
                  <label for="email">Email</label>
                  <input autocomplete='off' type="email" class="form-control" name="email" id="email" value="{{Auth::user()->email}}">
                </div>
              </div> -->
            </div>
            <div class="row gutters">
              <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                <h6 class="mt-3 mb-2 text-primary">Address</h6>
              </div>
              <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                <div class="form-group">
                  <label for="barangay">Barangay</label>
                  @if(empty($userr->barangay))
                  <input autocomplete='off' type="text" class="form-control" name="barangay" id="barangay" placeholder="--">
                  @else
                  <input autocomplete='off' type="text" class="form-control" name="barangay" id="barangay" value="{{$userr->barangay}}">
                  @endif
                  <span class="glyphicon glyphicon-user form-control-feedback"></span>
                  <span class="text-danger">
                    <strong id="barangay-error"></strong>
                  </span>
                </div>
              </div>
              <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                <div class="form-group">
                  <label for="municipality">Municipality</label>
                  @if(empty($userr->municipality))
                  <input autocomplete='off' type="text" class="form-control" name="municipality" id="municipality" placeholder="--">
                  @else
                  <input autocomplete='off' type="text" class="form-control" name="municipality" id="municipality" value="{{$userr->municipality}}">
                  @endif
                  <span class="glyphicon glyphicon-user form-control-feedback"></span>
                  <span class="text-danger">
                    <strong id="municipality-error"></strong>
                  </span>
                </div>
              </div>
              <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                <div class="form-group">
                  <label for="city">City</label>
                  @if(empty($userr->city))
                  <input autocomplete='off' type="text" class="form-control" name="city" id="city" placeholder="--">
                  @else
                  <input autocomplete='off' type="text" class="form-control" name="city" id="city" value="{{$userr->city}}">
                  @endif
                  <span class="glyphicon glyphicon-user form-control-feedback"></span>
                  <span class="text-danger">
                    <strong id="city-error"></strong>
                  </span>
                </div>
              </div>
              <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                <div class="form-group">
                  <label for="province">Province</label>
                  @if(empty($userr->province))
                  <input autocomplete='off' type="text" class="form-control" name="province" id="province" placeholder="--">
                  @else
                  <input autocomplete='off' type="text" class="form-control" name="province" id="province" value="{{$userr->province}}">
                  @endif
                  <span class="glyphicon glyphicon-user form-control-feedback"></span>
                  <span class="text-danger">
                    <strong id="province-error"></strong>
                  </span>
                </div>
              </div>
              <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                <div class="form-group">
                  <label for="zipcode">Zip Code</label>
                  @if(empty($userr->zipcode))
                  <input autocomplete='off' type="number" class="form-control" name="zipcode" id="zipcode" placeholder="--">
                  @else
                  <input autocomplete='off' type="number" class="form-control" name="zipcode" id="zipcode" value="{{$userr->zipcode}}">
                  @endif
                  <span class="glyphicon glyphicon-user form-control-feedback"></span>
                  <span class="text-danger">
                    <strong id="zipcode-error"></strong>
                  </span>
                </div>
              </div>
            </div>
            <div class="row gutters">
              <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                <h6 class="mt-3 mb-2 text-primary">Account</h6>
              </div>
              @if(Auth::user()->hasRole('Administrator'))
              <div class="col-xl-12 col-lg-12 col-md-4 col-sm-4 col-12">
                <div class="form-group">
                  <label for="email">Departments</label>
                  <select name="department_id" id="department_id" class="form-control">
                    @foreach($department as $dept)
                    <option value="{{$dept->id}}">{{$dept->Dept_name}}</option>
                    @endforeach
                  </select>
                </div>
              </div>
              @else
              @endif
              <div class="col-xl-12 col-lg-12 col-md-4 col-sm-4 col-12">
                <div class="form-group">
                  <label for="email">Email</label>
                  <input autocomplete='off' type="email" class="form-control" name="email" id="email" value="{{Auth::user()->email}}">
                  <span class="glyphicon glyphicon-user form-control-feedback"></span>
                  <span class="text-danger">
                    <strong id="email-error"></strong>
                  </span>
                </div>
              </div>
            </div>
            <div class="row gutters">
              <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                <div class="text-right">
                  <!-- <button type="button" id="submit" name="submit" class="btn btn-secondary">Cancel</button> -->
                  <button type="submit" class="btn btn-primary ">Save changes</button>
                </div>
              </div>
            </div>
          </div>
      </div>
      </form>
    </div>
  </div>
</div>

<script type="text/javascript">
  $().ready(function() {
    $('.update_edit_btn').on('click', function() {
      // var fname = $('#fname').val();
      // var mname = $('#mname').val();
      $('#updateProfileModal').modal('show');
      // $('#fname').val(fname);
      // $('#mname').val(mname);

    });
  });
</script>

<script type="text/javascript">
  $().ready(function() {
    $('#update_ProfileForm').on('submit', function(e) {

      e.preventDefault();
      Swal.fire({
        title: 'Update Profile?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, send it!'
      }).then((result) => {
        if (result.isConfirmed) {
          var id = $("#profile_id").val();

          //$('#logout-form').submit() // this submits the form 
          $.ajax({
            type: "PATCH",
            url: "profile/update/" + id,
            data: $('#update_ProfileForm').serialize(),
            success: function(response) {
              console.log(response);
              if (response.errors) {
                if (response.errors.fname) {
                  $('#fname-error').html(response.errors.fname[0]);
                }
                if (response.errors.mname) {
                  $('#mname-error').html(response.errors.mname[0]);
                }
                if (response.errors.lname) {
                  $('#lname-error').html(response.errors.lname[0]);
                }
                if (response.errors.sex) {
                  $('#sex-error').html(response.errors.sex[0]);
                }
                if (response.errors.contact_no) {
                  $('#contact_no-error').html(response.errors.contact_no[0]);
                }
                if (response.errors.barangay) {
                  $('#barangay-error').html(response.errors.barangay[0]);
                }
                if (response.errors.municipality) {
                  $('#municipality-error').html(response.errors.municipality[0]);
                }
                if (response.errors.city) {
                  $('#city-error').html(response.errors.city[0]);
                }
                if (response.errors.province) {
                  $('#province-error').html(response.errors.province[0]);
                }
                if (response.errors.zipcode) {
                  $('#zipcode-error').html(response.errors.zipcode[0]);
                }
                if (response.errors.position) {
                  $('#position-error').html(response.errors.position[0]);
                }
                if (response.errors.department_id) {
                  $('#department_id-error').html(response.errors.department_id[0]);
                }
                if (response.errors.email) {
                  $('#email-error').html(response.errors.email[0]);
                }
              }
              if (response.success) {
                $('#updateProfileModal').modal('hide');
                //alert("data updated");
                Swal.fire({
                  icon: 'success',
                  title: 'Data Have been updated!',
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