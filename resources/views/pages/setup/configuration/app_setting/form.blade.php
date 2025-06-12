@extends('layouts.layoutMaster')

{{-- title --}}
@section('title', 'App Setting')

{{-- vendor styles --}}
@section('vendor-styles')
@endsection

@php
    $page_action = Helper::pageAction(config('pages.form_type.app_setting'));
@endphp
{{-- page styles --}}
@section('page-styles')
@endsection
<style>

</style>
@section('content')
    <section>
        <h5 class="py-3 breadcrumb-wrapper mb-2">
          <span class="text-muted fw-light"><a href="/"><i class="bx bx-home-alt"></i></a> / {{ trans('pages.attribute_list.setup') }} / Configuration / </span> App Setting
        </h5>
        <div class="card">

            <div class="card-header pt-75">
                <h4 class="card-title">App Setting</h4>
            </div>

            <div class="card-body">
                <form method="POST" action="{{ route('setup.config.app_setting.app.save') }}"  id="app-settings-frm">
                    @csrf
                    <div class="form-body">

                        <div class="row">

                            <div class="col-12 col-md-12 left-section">
                                <div class="row mr-25">
                                  <div class="col-md-6 mb-3 col-sm-12 form-group">
                                    <div class="controls">
                                        <label  class="d-block">Andriod minimum version</label>
                                        <input type="text" name="android_minimum_version" class="form-control"  value="{{ (isset($app_settings->android_minimum_version) ? $app_settings->android_minimum_version : '' ) }}" maxlength="6">
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3  col-sm-12 form-group">
                                    <div class="controls">
                                        <label  class="d-block">Android maximum version</label>
                                        <input type="text" name="android_maximum_version" class="form-control" value="{{ (isset($app_settings->android_maximum_version) ? $app_settings->android_maximum_version : '' ) }}" maxlength="6">
                                    </div>
                                </div>


                                  <div class="col-md-6 mb-3  col-sm-12 form-group">
                                    <div class="controls">
                                        <label  class="d-block">Ios minimum version</label>
                                        <input type="text" name="ios_minimum_version" class="form-control"  value="{{ (isset($app_settings->ios_minimum_version) ? $app_settings->ios_minimum_version : '' ) }}" maxlength="6">
                                    </div>
                                  </div>
                                <div class="col-md-6 mb-3  col-sm-12 form-group">
                                    <div class="controls">
                                        <label  class="d-block">Ios maximum version</label>
                                        <input type="text" name="ios_maximum_version" class="form-control" value="{{ (isset($app_settings->ios_maximum_version) ? $app_settings->ios_maximum_version : '' ) }}" maxlength="6">
                                    </div>
                                </div>

                                    <div class="col-md-3 mb-3  col-sm-12 form-group">
                                        <div class="controls">
                                            <label  class="d-block">Is undermaintenance</label>
                                            <select name="is_undermaintenance" class="select2 form-control"  id="is_undermaintenance" data-element-ref="select2" required>
                                              <option >Please select</option>
                                              <option value="0" {{ "0" == (isset($app_settings->is_undermaintenance) ? $app_settings->is_undermaintenance : '' )  ? 'selected' : '' }}>No</option>
                                              <option value="1" {{ "1" ==  (isset($app_settings->is_undermaintenance) ? $app_settings->is_undermaintenance : '' )  ? 'selected' : '' }}>Yes</option>
                                            </select>

                                        </div>
                                    </div>
                                    <div class="col-md-3 mb-3  col-sm-12 form-group">
                                        <div class="controls">
                                            <label  class="d-block">Is force update ios</label>
                                            <select name="is_force_update_ios" class="select2 form-control" data-element-ref="select2" id="is_force_update_ios" required>
                                              <option>Please select</option>
                                              <option value="0" {{ "0" == (isset($app_settings->is_force_update_ios) ? $app_settings->is_force_update_ios : '' )  ? 'selected' : '' }}>No</option>
                                              <option value="1" {{ "1" ==  (isset($app_settings->is_force_update_ios) ? $app_settings->is_force_update_ios : '' )  ? 'selected' : '' }}>Yes</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3 mb-3  col-sm-12 form-group">
                                      <div class="controls">
                                          <label  class="d-block">Is force update android</label>
                                          <select name="is_force_update_android" class="select2 form-control" data-element-ref="select2" id="is_force_update_android">
                                            <option >Please select</option>
                                            <option value="0" {{ "0" == (isset($app_settings->is_force_update_android) ? $app_settings->is_force_update_android : '' )  ? 'selected' : '' }}>No</option>
                                            <option value="1" {{ "1" ==  (isset($app_settings->is_force_update_android) ? $app_settings->is_force_update_android : '' )  ? 'selected' : '' }}>Yes</option>
                                          </select>
                                      </div>
                                    </div>
                                    <div class="col-md-3 mb-3  col-sm-12 form-group">
                                      <div class="controls">
                                          <label  class="d-block">Order Cancellation Hours</label>
                                          <input type="number" name="order_cancellation_hours" min="1" class="form-control" value="{{ (isset($app_settings->order_cancellation_hours) ? \Helper::convertSecondsToHours($app_settings->order_cancellation_hours) : 0 ) }}">
                                      </div>
                                    </div>

                                    <div class="col-md-3 mb-3 col-sm-12 form-group">
                                      <div class="controls">
                                          <label  class="d-block">Discount Type</label>
                                          <select name="discount_type" class="select2 form-control" data-element-ref="select2" id="discount_type">
                                            <option value="">Please select</option>

                                            @if($discount_types)
                                                @foreach ($discount_types as $type)
                                                    <option value="{{ $type }}" {{ isset($app_settings) && $app_settings->discount_type == $type ? 'selected' : '' }}>{{ ucfirst($type) }}</option>
                                                @endforeach
                                            @endif

                                          </select>
                                      </div>
                                    </div>
                                    <div class="col-md-3 mb-3 col-sm-12 form-group">
                                      <div class="controls">
                                          <label  class="d-block">Discount</label>
                                          <input type="number" name="discount_value" class="form-control" value="{{ (isset($app_settings->discount_value) ? $app_settings->discount_value : '' ) }}">
                                      </div>
                                    </div>
                                    {{-- In Minutes --}}
                                    <div class="col-md-3 mb-3 col-sm-12 form-group">
                                      <div class="controls">
                                          <label  class="d-block">Cart Timer Limit (In Minutes)</label>
                                          <input type="number" name="cart_timer_limit" class="form-control" value="{{ (isset($app_settings->cart_timer_limit) ? \Helper::convertSecondsToMinutes($app_settings->cart_timer_limit) : 0 ) }}">
                                      </div>
                                    </div>
                                    {{-- In days --}}
                                    <div class="col-md-3 mb-3 col-sm-12 form-group">
                                      <div class="controls">
                                          <label  class="d-block">Payout Days</label>
                                          <input type="number" name="payout_days" class="form-control" value="{{ (isset($app_settings->payout_days) ? $app_settings->payout_days : NULL ) }}">
                                      </div>
                                    </div>

                                  <div class="col-12 form-group"></div>
                                  <div class="col-md-12 mb-3 col-sm-12 form-group">
                                      <div class="controls">
                                          <label class="d-block">Term and Conditions</label>
                                          <textarea class="ckeditor form-control"  name="terms_and_conditions">{!! (isset($app_settings->terms_and_conditions) ? $app_settings->terms_and_conditions : '' ) !!}</textarea>
                                          <div class="ck_editor_validate_msg"></div>
                                      </div>
                                  </div>
                                  <div class="col-12 form-group" ></div>
                                  <div class="col-md-12 mb-3 col-sm-12 form-group">
                                      <div class="controls">
                                          <label class="d-block">Privacy Policy</label>
                                          <textarea class="ckeditor form-control"  name="privacy_policy">{!! (isset($app_settings->privacy_policy) ? $app_settings->privacy_policy : '' ) !!}</textarea>
                                          <div class="ck_editor_validate_msg"></div>
                                      </div>
                                  </div>

                                  <div class="col-12 form-group" ></div>
                                  <div class="col-md-12 mb-3 col-sm-12 form-group">
                                      <div class="controls">
                                          <label class="d-block">About Us</label>
                                          <textarea  class="ckeditor form-control" name="about_us" >{!! (isset($app_settings->about_us) ? $app_settings->about_us : '' ) !!}</textarea>
                                          <div class="ck_editor_validate_msg"></div>
                                      </div>
                                  </div>

                                  <div class="col-12 form-group" ></div>
                                  <div class="col-md-12 mb-3 col-sm-12 form-group">
                                      <div class="controls">
                                          <label class="d-block">Delete Account</label>
                                          <textarea class="ckeditor form-control"  name="delete_account">{!! (isset($app_settings->delete_account) ? $app_settings->delete_account : '' ) !!}</textarea>
                                          <div class="ck_editor_validate_msg"></div>
                                      </div>
                                  </div>


                                  <div class="col-md-3 mb-4  col-sm-12 form-group">
                                    <div class="controls">
                                        <label  class="d-block">Privacy Policy Url</span></label>
                                        <input type="text" name="privacy_policy_url" class="form-control" value="{{ (isset($app_settings->privacy_policy_url) ? $app_settings->privacy_policy_url : '' ) }}">
                                    </div>
                                  </div>

                                  <div class="col-md-3 mb-4  col-sm-12 form-group">
                                    <div class="controls">
                                        <label  class="d-block">About Us Url</span></label>
                                        <input type="text" name="about_us_url" class="form-control" value="{{ (isset($app_settings->about_us_url) ? $app_settings->about_us_url : '' ) }}">
                                    </div>
                                  </div>


                                  <div class="col-md-3 mb-4  col-sm-12 form-group">
                                    <div class="controls">
                                        <label  class="d-block">Delete Account Url</span></label>
                                        <input type="text" name="delete_account_url" class="form-control" value="{{ (isset($app_settings->delete_account_url) ? $app_settings->delete_account_url : '' ) }}">
                                    </div>
                                  </div>

                                  <div class="col-md-3 mb-4  col-sm-12 form-group">
                                    <div class="controls">
                                        <label  class="d-block">Term And Conditions Url</span></label>
                                        <input type="text" name="term_and_conditions_url" class="form-control" value="{{ (isset($app_settings->term_and_conditions_url) ? $app_settings->term_and_conditions_url : '' ) }}">
                                    </div>
                                  </div>
                                </div>
                          </div>
                        </div>
                        <input type="hidden" name="app_settings_id"  value="{{ (isset($app_settings->app_setting_id) ? $app_settings->app_setting_id : '' ) }}">
                        <div class="col-md-12 text-end">

                            @if (@$page_action->is_update == 1)
                                <button type="submit" id="app-settings-frm-btn" class="btn btn-primary">Update</button>
                            @endif
                            <a href="{{ url()->previous() }}" class="btn btn-secondary ms-2">Cancel</a>
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
    @include('scripts.setup.configuration.create_js')

@endsection
