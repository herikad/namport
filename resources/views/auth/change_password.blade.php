@extends('layouts/layoutMaster')
{{-- page title --}}
@section('title','Change password')

@section('content')

  <!-- users edit start -->
    <section class="users-edit">
      
      <h5 class="py-3 breadcrumb-wrapper mb-2">
        <span class="text-muted fw-light"><a href="/"><i class="bx bx-home-alt"></i></a> /</span> Change Password
    </h5>
      <div class="card">
        
        <div class="card-header pt-75">
          <h4 class="card-title">Change Password</h4>
        </div>

        <div class="card-content">
          <div class="card-body">
            
                <form action="{{route('update_change_password')}}" method="POST" id="changePassForm">
                  @csrf
                    <div class="row">
                        <div class="col-12 col-sm-6">
                            <div class="form-group mb-3">
                                <div class="controls">
                                    <label class="required">Old Password</label>
                                    <input type="password" class="form-control" placeholder="Old Password"
                                        name="current_password" required
                                        data-validation-required-message="This old password field is required">

                                </div>
                            </div>
                            <div class="form-group mb-3">
                                <div class="controls">
                                    <label class="required">New Password</label>
                                    <input type="password" class="form-control" placeholder="New Password" name="new_password"
                                    required id="new_password"  data-validation-required-message="The password field is required"
                                                                    minlength="6">
                                </div>
                            </div>

                            <div class="form-group mb-3">
                                <div class="controls">
                                    <label class="required">Retype new Password</label>
                                    <input type="password" class="form-control" placeholder="Retype new Password" name="con_password"
                                    required  data-validation-match-match="new_password" data-validation-required-message="The Confirm password field is required"
                                                                    minlength="6">
                                </div>
                            </div>

                        </div>
                        <div class="col-12 d-flex flex-sm-row flex-column justify-content-end mt-1">
                            <button type="submit" id="submitPass"  class="btn btn-success me-sm-3 me-1">Save</button>
                                <a href="{{ route('dashboard') }}" class="btn btn-light">Cancel</a>
                        </div>
                    </div>
                </form>
              
          </div>
        </div>
      </div>
    </section>
  <!-- users edit ends -->

@endsection


@section('page-script')
  @include('scripts.auth.change_password_js')
@endsection
{{-- call Js --}}
