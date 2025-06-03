@extends('layouts.layoutMaster')


{{-- title --}}
@section('title', isset($role_details) ? 'Role Edit' : 'Role Create')

{{-- vendor styles --}}
@section('vendor-styles')

@endsection

{{-- page styles --}}
@section('page-styles')

@endsection

@section('content')
    <section>
      <h5 class="py-3 breadcrumb-wrapper mb-2">
        <span class="text-muted fw-light"><a href="/"><i class="bx bx-home-alt"></i></a> / Setup / User Management / </span><a href="{{route('setup.user_management.role.index')}}">Role / </a> {{isset($role_details) ? 'Role Edit' : 'Role Add'}}
      </h5>
        <form action="{{route('setup.user_management.role.store')}}" method="POST" class="user_role_form" id="user_role_Form_id">
          @csrf
            <div class="card">

                <div class="card-header pt-75">
                    <h4 class="card-title"> {{isset($role_details) ? trans('pages.edit_with_attr', ['attribute' => 'Role']) : trans('pages.add_with_attr', ['attribute' => 'Role'])}} </h4>
                </div>

                <div class="card-body">
                    <fieldset>

                        <div class="row">

                            <input type="hidden" name="id" class="form-control" id="role_id" value="{{isset($role_details) ? $role_details->role_id : ''}}">
                            <?php
                            if(isset($role_details) && $role_details->IsDefault == 1){
                              $readonly = "readonly";
                            }else{
                              $readonly = "";
                            }
                            ?>

                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="" class="required">Role Name </label>
                                    <input type="text" maxlength="25" value="{{isset($role_details) ? $role_details->role_name : ''}}" name="role_name" class="form-control" {{ $readonly }}>
                                </div>
                            </div>

                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="" class="required">Display Name </label>
                                    <input type="text" maxlength="30" value="{{isset($role_details) ? $role_details->display_name : ''}}" name="display_name" class="form-control" {{ $readonly }}>
                                </div>
                            </div>

                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label class="required">User Type</label>

                                    <select name="user_type_term" class="form-control" id="user_type_term" required style="pointer-events:{{ @$role_details->IsDefault == 1 ? 'none' : '' }}">
                                        <option value="">Select User Type</option>
                                        @if (count($user_types))
                                            @foreach($user_types as $key => $term)
                                                <option value="{{$term->term_code}}" {{isset($role_details) && $role_details->user_type_term == $term->term_code ? 'selected' : ''}}>{{$term->label}}</option>
                                            @endforeach
                                        @endif
                                    </select>

                                </div>
                            </div>

                            <div class="col-sm-12 p-3">
                                <div class="form-group">
                                    <label class="">Role Details</label>
                                    <textarea name="role_details" class="form-control" rows="3" required {{ $readonly }}>{!! isset($role_details) ? $role_details->display_name : '' !!}</textarea>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12 text-end ">
                            <button type="submit" class="btn btn-success me-2"  id="saveRoleBtn">{{ trans('pages.save') }}</button>
                            <a href="{{ url()->previous() }}" class="btn btn-secondary">{{ trans('pages.cancel') }}</a>
                        </div>

                    </fieldset>
                </div>

            </div>

            <input type="hidden" name="role_right" class="form-control" id="role_right_id" value="">

            <div id="rights_container">
            </div>

        </form>

    </section>
@endsection


{{-- vendor scripts --}}
@section('vendor-scripts')

@endsection

{{-- page scripts --}}
@section('page-script')
@include('scripts.user_management.role.role_create_js')
@endsection
