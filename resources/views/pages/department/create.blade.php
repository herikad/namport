@extends('layouts.layoutMaster')

{{-- title --}}
@section('title',  isset($department) ? 'Department Edit' : 'Department Create')

{{-- vendor styles --}}
@section('vendor-styles')
@endsection

{{-- page styles --}}
@section('page-styles')
@endsection

@section('content')
  <h5 class="py-3 breadcrumb-wrapper mb-2">
    <span class="text-muted fw-light"><a href="/"><i class="bx bx-home-alt"></i></a> / Setup / </span><a href="{{route('setup.department.index')}}">Department / </a>{{isset($department) ? 'Edit Department' : 'Add Department'}}
  </h5>
    <section>
        <div class="card">

            <div class="card-header pt-75">
                <h4 class="card-title">  {{isset($department) ? trans('pages.edit_with_attr', ['attribute' => 'Department']) : trans('pages.add_with_attr', ['attribute' => 'Department']) }}
                </h4>
            </div>

            <div class="card-body">
                <form method="POST" action="{{route('setup.department.store')}}" id="department_form_id" enctype="multipart/form-data">
                    @csrf
                    <div class="form-body">
                        <input type="hidden" name="department_id" value="{{isset($department) ? $department->department_id : ''}}">
                        <div class="row">
                            <div class="col-12 col-md-12 left-section">
                                <div class="row mr-25">

                                    <div class="col-md-4 mb-3  col-sm-12 form-group">
                                        <div class="controls">
                                            <label  class="d-block required">Department Name</span></label>
                                            <input type="text" maxlength="50" value="{{isset($department) ? $department->department_name : ''}}" name="department_name" class="form-control" required>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3  col-sm-12 form-group">
                                        <div class="controls">
                                            <label  class="d-block">Department Code</span></label>
                                            <input type="text" maxlength="50" value="{{isset($department) ? $department->department_code : $code}}" name="department_code" class="form-control" readonly>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-4 mb-3 col-sm-12">
                                      <label class="required">Client</label>
                                      <select class="select2 form-control" name="client_id" id="user_type_term" data-element-ref="select2" required>
                                          <option value="">Select Client</option>
                                          @if (count($clients))
                                              @foreach($clients as $key => $value)
                                                <option value="{{$value->client_id}}" {{isset($department) && $department->client_id == $value->client_id ? 'selected' : ''}}>{{$value->display_name}}</option>
                                              @endforeach
                                          @endif
                                      </select>
                                    </div>
                                     <div class="col-md-12 mb-3  col-sm-12 form-group">
                                        <div class="controls">
                                            <label  class="d-block">Department Short Description</span></label>
                                            <textarea name="short_description" class="form-control" id="short_description">{{isset($department) ? $department->short_description : ''}}</textarea>
                                        </div>
                                    </div>

                                    <div class="col-md-12 mb-3  col-sm-12 form-group">
                                        <div class="controls">
                                            <label  class="d-block">Department Description</span></label>
                                            <textarea id="detail_description" class="form-control" data-element-ref="ckeditor" name="detail_description" rows="6">{{isset($department) ? $department->detail_description : '' }}</textarea>
                                            <div class="ck_editor_validate_msg"></div>
                                        </div>
                                    </div>


                                </div>
                            </div>
                        </div>

                        <div class="col-md-12 text-end">
                            <input type="submit" class="btn btn-success"
                                id="saveDepartmentBtn" value="{{ trans('pages.save') }}">
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
<script src="{{ asset('extensions/ckeditor/ckeditor.js') }}" type="text/javascript"></script>

@include('scripts.department.create_js')
@endsection
