<script>
  var AWSURL = "{{ env('AWS_URL') }}";

  var file_System = "{{ config('filesystems.default') }}";

  var assetBaseUrl = "{{ asset('') }}";

  if(file_System == 's3'){
      assetBaseUrl = AWSURL;
  }

</script>
<!-- BEGIN: Vendor JS-->

<script src="{{ asset('assets/vendor/libs/jquery/jquery.js') }}"></script>
<script src="{{ asset('assets/vendor/libs/popper/popper.js') }}"></script>
<script src="{{ asset('assets/vendor/js/bootstrap.js') }}"></script>
<script src="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
<script src="{{ asset('assets/vendor/libs/hammer/hammer.js') }}"></script>
<script src="{{ asset('assets/vendor/libs/typeahead-js/typeahead.js') }}"></script>
<script src="{{ asset('assets/vendor/js/menu.js') }}"></script>
<script src="{{ asset('assets/vendor/libs/toastr/toastr.js') }}"></script>
<script src="{{asset('assets/vendor/libs/sweetalert2/sweetalert2.js')}}"></script>
<script src="{{asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js')}}"></script>
<script src="{{asset('assets/vendor/libs/moment/moment.js')}}"></script>
<script src="{{asset('assets/vendor/libs/flatpickr/flatpickr.js')}}"></script>
{{-- <script src="{{asset('assets/vendor/libs/formvalidation/dist/js/plugins/Bootstrap5.min.js')}}"></script> --}}
{{-- <script src="{{asset('assets/vendor/libs/formvalidation/dist/js/plugins/AutoFocus.min.js')}}"></script> --}}
<script src="{{asset('assets/vendor/libs/bootstrap-datepicker/bootstrap-datepicker.js')}}"></script>
<script src="{{asset('assets/vendor/libs/bootstrap-daterangepicker/bootstrap-daterangepicker.js')}}"></script>
<script src="{{asset('assets/vendor/libs/jquery-timepicker/jquery-timepicker.js')}}"></script>
<script src="{{asset('assets/vendor/libs/pickr/pickr.js')}}"></script>
<script src="{{asset('assets/vendor/libs/select2/select2.js')}}"></script>
<script src="{{asset('assets/vendor/libs/cleavejs/cleave.js')}}"></script>
<script src="{{asset('assets/vendor/libs/cleavejs/cleave-phone.js')}}"></script>
<script src="{{asset('assets/vendor/libs/formvalidation/dist/js/FormValidation.min.js')}}"></script>

@yield('vendor-script')
<!-- END: Page Vendor JS-->
<!-- BEGIN: Theme JS-->
{{-- <script src="{{ asset(mix('assets/js/main.js')) }}"></script> --}}
<script src="{{ asset('assets/js/main.js') }}"></script>
<script src="{{asset('assets/js/moment-timezone-with-data.min.js')}}"></script>

<script src="{{ asset('assets/js/jquery.validate.min.js') }}"></script>

<!-- END: Theme JS-->
{{-- <script src="{{asset('assets/js/extended-ui-sweetalert2.js')}}"></script>
<script src="{{asset('assets/js/tables-datatables-extensions.js')}}"></script>
<script src="{{asset('assets/js/tables-datatables-advanced.js')}}"></script> --}}
{{-- <script src="{{asset('assets/js/form-wizard-icons.js')}}"></script>
<script src="{{asset('assets/js/form-wizard-validation.js')}}"></script> --}}

<!-- BEGIN: Page JS-->
@yield('page-script')
<!-- END: Page JS-->

@include('layouts.sections.scripts_js')
@include('layouts.sections.flash-notifications')
