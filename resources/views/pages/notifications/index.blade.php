@extends('layouts.layoutMaster')
{{-- page title --}}
@section('title','Notificaitons')
{{-- vendor styles --}}
@section('vendor-styles')
@endsection
{{-- page styles --}}
@section('page-styles')
@endsection

@section('content')

    <div class="row">
        <div class="col-xl-12 col-md-12 col-sm-12">
            <div class="card">

              <div class="card-header">
                <h4 class="text-dark">Notifications</h4>
              </div>

              <ul class="list-group list-group-flush" id="notifications-container">
                
              </ul>
            </div>
        </div>
    </div>
@endsection

{{-- vendor scripts --}}
@section('vendor-scripts')
@endsection


{{-- page scripts --}}
@section('page-script')
  @include('scripts.notifications.all_notifications_js')
@endsection
