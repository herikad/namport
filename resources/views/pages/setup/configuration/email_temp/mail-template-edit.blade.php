@extends('layouts.layoutMaster')

{{-- title --}}
@section('title', 'Mail template Edit')

{{-- vendor styles --}}
@section('vendor-styles')
@endsection

{{-- page styles --}}
@section('page-styles')
@endsection
<style>
    label#email_setting_id-error {
        position: relative;
        top: 60px;
        left: -59px;
    }
    .left-section .form-group {
        float: left;
        margin-bottom: 20px !important;
    }
</style>

@section('content')
    <section>
        
        <h5 class="py-3 breadcrumb-wrapper mb-2">
            <span class="text-muted fw-light"><a href="/"><i class="bx bx-home-alt"></i></a> /{{ trans('pages.attribute_list.setup') }} /</span> <a
            href="{{ route('setup.config.email_temp.index') }}">{{ trans('pages.mail_template_list.mail') }}</a>/{{ trans('pages.edit_with_attr', ['attribute' => 'Mail Template']) }}
        </h5>
        <div class="card">

            <div class="card-header pt-75">
                <h4 class="card-title">{{ trans('pages.edit_with_attr', ['attribute' => 'Mail Template']) }}
                </h4>
            </div>

            <div class="card-body">
                <form method="POST" action="{{ route('setup.config.email_temp.store') }}" class="mail_form"
                    id="mail_template_form_id">
                    @csrf
                    <div class="form-body">

                        <div class="row">


                            <div class="col-12 col-md-12 left-section">
                                <div class="row mr-25">

                                    <div class="col-md-6 mb-3 col-sm-12 form-group">
                                        <div class="controls">
                                            <label  class="d-block required">{{ trans('pages.mail_template_list.title') }}</span></label>
                                            <input type="text" name="title" value="{{ $mail_temp_details->title ? $mail_temp_details->title :''  }}" class="form-control" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3 col-sm-12 form-group">
                                        <div class="controls">
                                            <label  class="d-block required">{{ trans('pages.mail_template_list.mail_type') }}</span></label>
                                            <input type="text" name="mail_type"  value="{{ $mail_temp_details->mail_type ? $mail_temp_details->mail_type :''  }}" class="form-control" required>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-6 mb-3 col-sm-12">
                                        <label>{{ trans('pages.mail_template_list.email_id') }}</label>
                                        <select class="custom-select form-control email_setting_id_select" name="email_setting_id">
                                            <option value="">Select From Mail </option>
                                            @foreach ($mails as $mail)
                                                <option value="{{ $mail->email_setting_id }}" {{ $mail_temp_details->email_setting_id  ==  $mail->email_setting_id ? 'selected' :''  }} >{{ $mail->email_id }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-12 form-group" id="body-tags"></div>
                                    <div class="form-group col-12 col-sm-12 mb-3">
                                        <label>{{ trans('pages.mail_template_list.body') }}</label>

                                        <textarea id="mail-body-textarea" class="form-control" data-element-ref="ckeditor" name="body">{{ $mail_temp_details->body ? $mail_temp_details->body :''  }}</textarea>
                                        <div class="ck_editor_validate_msg"></div>
                                    </div>

                                </div>
                            </div>

                        </div>
                        <input  type="hidden" id="tag_name" name="tag_name"/>
                        <input type="hidden" id="edit-mail-id" name="email_template_id" value="{{ $mail_temp_details->email_template_id }}"/>
                        <div class="col-md-12 text-end">
                            <button type="submit" class="btn btn-success"
                                id="saveMailTempSettingBtn">{{ trans('pages.save') }}</button>
                            <!-- <button type="button" class="btn btn-secondary ms-2">{{ trans('pages.cancel') }}</button>  -->
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
    @include('scripts.setup.configuration.email_temp.mail_temp_create_js')

@endsection
