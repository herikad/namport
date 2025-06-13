@extends('layouts.layoutMaster')

{{-- title --}}
@section('title', 'Mail Create')

{{-- vendor styles --}}
@section('vendor-styles')
@endsection

{{-- page styles --}}
@section('page-styles')
@endsection

@section('content')
<section>
    <h5 class="py-3 breadcrumb-wrapper mb-2">
        <span class="text-muted fw-light"><a href="/"><i class="bx bx-home-alt"></i></a> / {{ trans('pages.attribute_list.setup') }} / {{ trans('pages.attribute_list.configuration') }} / </span><a href="{{route('setup.config.email_config.index')}}">{{ trans('pages.mail_list.email_config') }}</a> / {{ trans('pages.add_with_attr', ['attribute' => 'Email Configuration']) }}
    </h5>
    <div class="card">

        <div class="card-header pt-75 pb-75">
            <h4 class="card-title">{{ trans('pages.add_with_attr', ['attribute' => 'Email Configuration']) }}</h4>
        </div>

        <div class="card-body">
            <form method="POST" action="{{route('setup.config.email_config.store')}}" class="mail_form" id="mail_form_id">
                @csrf
                <div class="form-body">
              
                    <div class="row">
                        <div class="col-12 col-md-12 left-section">
                            <div class="row mr-25">

                                <div class="col-md-6 mb-3 form-group">
                                    <div class="controls">
                                        <label class="d-block required">{{ trans('pages.mail_list.email_id') }}</span></label>
                                        <input type="text" name="email_id" class="form-control" required>
                                    </div>
                                </div>

                                <div class="col-md-6 mb-3 form-group">
                                    <div class="controls">
                                        <label class="d-block required">{{ trans('pages.mail_list.from_name') }}</span></label>
                                        <input type="text" name="from_name" class="form-control" required>
                                    </div>
                                </div>

                                <div class="col-md-6 mb-3 form-group">
                                    <div class="controls">
                                        <label class="d-block required">{{ trans('pages.mail_list.email_protocol') }}</span></label>
                                        <input type="text" name="email_protocol" class="form-control" required>
                                    </div>
                                </div>

                                <div class="col-md-6 mb-3 form-group">
                                    <div class="controls">
                                        <label class="d-block required">{{ trans('pages.mail_list.smtp_host') }}</span></label>
                                        <input type="text" name="smtp_host" class="form-control" required>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3 form-group">
                                    <div class="controls">
                                        <label class="d-block required">{{ trans('pages.mail_list.smtp_port') }}</span></label>
                                        <input type="text" name="port" class="form-control" required>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3 form-group">
                                    <div class="controls">
                                        <label class="d-block required">{{ trans('pages.mail_list.smtp_crypto') }}</span></label>
                                        <input type="text" name="smtp_crypto" class="form-control" required>
                                    </div>
                                </div>
                                
                                <div class="col-md-6 mb-3 form-group">
                                    <div class="controls">
                                        <label class="d-block required">{{ trans('pages.mail_list.smtp_password') }}</span></label>
                                        <input type="text" name="password" class="form-control" required>
                                    </div>
                                </div>

                            </div>
                        </div>

                    </div>

                    <div class="col-md-12 text-end">
                        <input type="submit" class="btn btn-success" id="saveMailSettingBtn" value="{{ trans('pages.save') }}">
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
    @include('scripts.setup.configuration.email_config.mail_create_js')
@endsection
