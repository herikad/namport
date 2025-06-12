@extends('layouts.layoutMaster')

{{-- title --}}
@section('title',  isset($designation) ? 'Designation Edit' : 'Designation Create')

{{-- vendor styles --}}
@section('vendor-styles')
@endsection

{{-- page styles --}}
@section('page-styles')
@endsection

@section('content')
  <h5 class="py-3 breadcrumb-wrapper mb-2">
    <span class="text-muted fw-light"><a href="/"><i class="bx bx-home-alt"></i></a> / Setup / </span><a href="{{route('setup.designation.index')}}">Designation / </a>{{isset($designation) ? 'Edit Designation' : 'Add Designation'}}
  </h5>
    <section>
        <div class="card">

            <div class="card-header pt-75">
                <h4 class="card-title">  {{isset($designation) ? trans('pages.edit_with_attr', ['attribute' => 'Designation']) : trans('pages.add_with_attr', ['attribute' => 'Designation']) }}
                </h4>
            </div>

            <div class="card-body">
                <form method="POST" action="{{route('setup.designation.store')}}" id="designation_form_id" enctype="multipart/form-data">
                    @csrf
                    <div class="form-body">
                        <input type="hidden" name="designation_id" value="{{isset($designation) ? $designation->designation_id : ''}}">
                        <div class="row">
                            <div class="col-12 col-md-12 left-section">
                                <div class="row mr-25">

                                    <div class="col-md-6 mb-3  col-sm-12 form-group">
                                        <div class="controls">
                                            <label  class="d-block required">Designation Name</span></label>
                                            <input type="text" maxlength="50" value="{{isset($designation) ? $designation->designation_name : ''}}" name="designation_name" class="form-control" required>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-6 mb-3 col-sm-12">
                                      <label class="required">Client</label>
                                      <select class="select2 form-control" name="client_id" id="user_type_term" data-element-ref="select2" required>
                                          <option value="">Select Client</option>
                                          @if (count($clients))
                                              @foreach($clients as $key => $value)
                                                <option value="{{$value->client_id}}" {{isset($designation) && $designation->client_id == $value->client_id ? 'selected' : ''}}>{{$value->display_name}}</option>
                                              @endforeach
                                          @endif
                                      </select>
                                    </div>
                                     <div class="col-md-12 mb-3  col-sm-12 form-group">
                                        <div class="controls">
                                            <label  class="d-block">Designation Role</span></label>
                                            <textarea name="designation_role" class="form-control" id="designation_role">{{isset($designation) ? $designation->designation_role : ''}}</textarea>
                                        </div>
                                    </div>

                                    <div class="col-md-12 mb-3  col-sm-12 form-group">
                                        <div class="controls">
                                            <label  class="d-block">Designation Responsibilities</span></label>
                                            <textarea id="desig_responsibilities" class="form-control" data-element-ref="ckeditor" name="desig_responsibilities" rows="6">{{isset($designation) ? $designation->desig_responsibilities : '' }}</textarea>
                                            <div class="ck_editor_validate_msg"></div>
                                        </div>
                                    </div>


                                </div>
                            </div>
                        </div>

                        <div class="col-md-12 text-end">
                            <input type="submit" class="btn btn-success"
                                id="saveDesignationBtn" value="{{ trans('pages.save') }}">
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

@include('scripts.designation.create_js')
@endsection
