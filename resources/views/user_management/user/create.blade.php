@extends('layouts.layoutMaster')

{{-- title --}}
@section('title',  isset($user) ? 'User Edit' : 'User Create')

{{-- vendor styles --}}
@section('vendor-styles')
@endsection

{{-- page styles --}}
@section('page-styles')
@endsection

@section('content')
  <h5 class="py-3 breadcrumb-wrapper mb-2">
    <span class="text-muted fw-light"><a href="/"><i class="bx bx-home-alt"></i></a> / Setup / User Management / </span><a href="{{route('setup.user_management.user.index')}}">User / </a>{{isset($user) ? 'Edit User' : 'Add User'}}
  </h5>
    <section>
        <div class="card">

            <div class="card-header pt-75">
                <h4 class="card-title">  {{isset($user) ? trans('pages.edit_with_attr', ['attribute' => 'User']) : trans('pages.add_with_attr', ['attribute' => 'User']) }}
                </h4>
            </div>

            <div class="card-body">
                <form method="POST" action="{{ route('setup.user_management.user.store') }}" id="user_form_id">
                    @csrf
                    <div class="form-body">
                        <input type="hidden" name="id" value="{{isset($user) ? $user->user_id : ''}}">
                        <div class="row">
                            <div class="col-12 col-md-12 left-section">
                                <div class="row mr-25">

                                    <div class="col-md-6 mb-3  col-sm-12 form-group">
                                        <div class="controls">
                                            <label  class="d-block required">Display Name</span></label>
                                            <input type="text" maxlength="50" value="{{isset($user) ? $user->display_name : ''}}" name="display_name" class="form-control" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3  col-sm-12 form-group">
                                        <div class="controls">
                                            <label  class="d-block required">Email</span></label>
                                            <input type="email" maxlength="60" value="{{isset($user) ? $user->user_name : ''}}" name="user_name" class="form-control" required>
                                        </div>
                                    </div>
                                    @if(!isset($user))
                                    <div class="col-md-6 mb-3 col-sm-12 form-group">
                                      <div class="controls">
                                          <label  class="d-block required">Password</span></label>
                                          <input type="password" maxlength="50" value="" name="password" class="form-control" required>
                                      </div>
                                    </div>
                                    @endif

                                    <div class="form-group col-md-6 mb-3 col-sm-12">
                                      <label class="required">User Type Term</label>
                                      <select class="select2 form-control" name="user_type_term" id="user_type_term" data-element-ref="select2" required>
                                          <option value="">Select User Type Term</option>
                                          @if (count($user_types))
                                              @foreach($user_types as $key => $term)
                                                <option value="{{$term->term_code}}" {{isset($user) && $user->user_type_term == $term->term_code ? 'selected' : ''}}>{{$term->label}}</option>
                                              @endforeach
                                          @endif
                                      </select>
                                    </div>

                                    <div class="form-group col-md-6 mb-3 col-sm-12">
                                        <label class="required">Role Type</label>
                                        <select class="select2 form-select" name="role_id" data-element-ref="select2" required>
                                            <option value="">Please select role</option>

                                                @foreach ($role as $val)
                                                    @if (isset($user))
                                                        <option value="{{ $val->role_id }}" {{ in_array($val->role_id, array_column($user_roles, 'role_id')) ? 'selected' : '' }}>{{ $val->display_name }}</option>
                                                    @endif

                                                    @if($auth_user->user_type_term == config('custom.user_type_term.super_admin'))

                                                        <option value="{{ $val->role_id }}" {{ $val->display_name == config('custom.default_role.super_admin')  ? 'selected' : '' }}>{{ $val->display_name }}</option>

                                                    @elseif($auth_user->user_type_term == config('custom.user_type_term.club_user'))

                                                        <option value="{{ $val->role_id }}" {{ $val->display_name == config('custom.default_role.club_user')  ? 'selected' : '' }}>{{ $val->display_name }}</option>

                                                    @endif
                                              @endforeach

                                        </select>
                                    </div>

                                    @if($auth_user->user_type_term == config('custom.user_type_term.club_user'))

                                    <input type="hidden" name="timezone_term" value="{{ isset($club_manager) ? $club_manager->timezone_term : '' }}">
                                    <div class="col-md-6 d-flex">
                                      <div class="col-md-3">
                                          <div class="controls mb-3">
                                              <label class="d-block required">Country Code</span></label>
                                              <select name="country_code" class="select2 form-control rounded-start rounded-0" id="country_code"
                                                  data-element-ref="select2" required>
                                                  <option value="" disabled>Select Country Code</option>
                                                  @if (count($countries))
                                                  @foreach ($countries as $key => $country)
                                                      <option value="{{ $country->phonecode }}"
                                                          {{ isset($user) && $user->country_code == $country->phonecode ? 'selected' : '' }}>
                                                          +{{ $country->phonecode }} -
                                                          {{ $country->sortname }}</option>
                                                  @endforeach
                                              @endif
                                              </select>
                                          </div>
                                      </div>
                                      <div class="col-md-9">
                                          <div class="controls mb-3">
                                            <label class="d-block required">Phone No</span></label>
                                            <input type="text" name="mobile" class="form-control form-control border-start-0 rounded-end rounded-0"
                                                value="{{ isset($user) ? $user->phone : '' }}" required maxlength="13">
                                          </div>
                                      </div>
                                    </div>

                                    <div class="form-group col-md-6 mb-3 col-sm-12">
                                      <label class="required">Clubs</label>
                                      <select class="select2 form-select club_ids" name="clubs[]" data-element-ref="select2" multiple required>

                                        @if (isset($clubs) && count($clubs) > 0)
                                            @foreach ($clubs as $club)
                                                <option value="{{ $club->club_id }}">
                                                    {{ $club->club_name }}</option>
                                            @endforeach
                                        @endif

                                      </select>
                                    </div>
                                    @endif

                                </div>
                            </div>
                        </div>

                        <div class="col-md-12 text-end">
                            <input type="submit" class="btn btn-success"
                                id="saveUserBtn" value="{{ trans('pages.save') }}">
                            <a href="{{ url()->previous() }}" class="btn btn-secondary ms-2">{{ trans('pages.cancel') }}</a>
                        </div>

                    </div>

                </form>
            </div>

        </div>
    </section>
@endsection

{{-- vendor scripts --}}
@section('vendor-scripts')
@endsection

{{-- page scripts --}}
@section('page-script')

<script>
  var deptids = [];
  @if (isset($assign_clubs))
      @foreach ($assign_clubs as $club)
          deptids.push("<?php echo $club['club_id']; ?>");
      @endforeach
      $(".club_ids").val(deptids).trigger('change');
  @endif
</script>

@include('scripts.user_management.user.create_js')
@endsection
