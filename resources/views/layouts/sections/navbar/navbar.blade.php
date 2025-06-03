@php
$containerNav = $containerNav ?? 'container-fluid';
@endphp

<!-- Navbar -->
<nav class="layout-navbar navbar navbar-expand-xl align-items-center bg-navbar-theme" id="layout-navbar">
  <div class="{{$containerNav}}">

    <!--  Brand demo (display only for navbar-full and hide on below xl) -->
    @if(isset($navbarFull))
    <div class="navbar-brand app-brand demo d-none d-xl-flex py-0 me-4">
      <a href="{{url('/')}}" class="app-brand-link gap-2">
        <span class="app-brand-logo demo">
          @include('_partials.macros')
        </span>
        <span class="app-brand-text demo menu-text fw-bold">{{config('variables.templateName')}}</span>
      </a>

      @if(isset($menuHorizontal))
      <!-- Display menu close icon only for horizontal-menu with navbar-full -->
      <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-xl-none">
        <i class="bx bx-x bx-sm align-middle"></i>
      </a>
      @endif
    </div>
    @endif

    <!-- ! Not required for layout-without-menu -->
    @if(!isset($navbarHideToggle))
    <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0{{ isset($menuHorizontal) ? ' d-xl-none ' : '' }} {{ isset($contentNavbar) ?' d-xl-none ' : '' }}">
      <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
        <i class="bx bx-menu bx-sm"></i>
      </a>
    </div>
    @endif

    <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">


      <ul class="navbar-nav flex-row align-items-center ms-auto">


        <!-- Style Switcher -->
        <li class="nav-item me-2 me-xl-0">
          <a class="nav-link style-switcher-toggle hide-arrow" href="javascript:void(0);">
            <i class='bx bx-sm'></i>
          </a>
        </li>
        <!--/ Style Switcher -->

        <!-- Notification -->
        <li class="nav-item dropdown-notifications navbar-dropdown dropdown me-3 me-xl-2">
          <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
            <i class="fa fa-bell fa-xl"></i>
            <span class="badge bg-danger rounded-pill badge-notifications" id="notify-badge-count" style="display:none">0</span>
          </a>

          <ul class="dropdown-menu dropdown-menu-end py-0">

            <li class="dropdown-menu-header border-bottom">
              <div class="dropdown-header d-flex align-items-center py-3">
                <h5 class="text-body mb-0 me-auto" id="notify-badge-count-title">Notifications</h5>
                <a href="javascript:void(0)" class="dropdown-notifications-all text-body" data-bs-toggle="tooltip" data-bs-placement="top" ></a>
              </div>
            </li>

              <li class="dropdown-notifications-list scrollable-container" id="unread-notifications-container">

              </li>

            <li class="dropdown-menu-footer border-top">
              <a href="{{ route('notification.all') }}" class="dropdown-item d-flex justify-content-center text-primary p-2 h-px-40">
                View All Notifications
              </a>
            </li>
          </ul>
        </li>
        <!--/ Notification -->

        <!-- User -->
        <li class="nav-item navbar-dropdown dropdown-user dropdown">
          <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
            <div class="avatar">
              <img src="{{ Auth::user()->profile_pic ? config('custom.image_base_url').Auth::user()->profile_pic : asset("/no_image.jpg") }}" style="object-fit: cover" alt class="rounded-circle" onerror="this.src='{{ asset('/no_image.jpg') }}'">
            </div>
          </a>
          <ul class="dropdown-menu dropdown-menu-end">
            <li>
              <a class="dropdown-item" href="{{ Route::has('profile.show') ? route('profile.show') : '#' }}">
                <div class="d-flex">
                  <div class="flex-shrink-0 me-3">
                    <div class="avatar">

                      <img src="{{ Auth::user()->profile_pic ? config('custom.image_base_url').Auth::user()->profile_pic : asset("/no_image.jpg") }}" alt class="rounded-circle" onerror="this.src='{{ asset('/no_image.jpg') }}'">
                    </div>
                  </div>
                  <div class="flex-grow-1">
                    <span class="fw-semibold d-block">
                      @if (Auth::check())
                      {{ Auth::user()->user_name }}
                      @else
                      Guest
                      @endif
                    </span>
                    <small class="text-muted">{{ Auth::user()->display_name }}</small>
                  </div>
                </div>
              </a>
            </li>
            <li>
              <div class="dropdown-divider"></div>
            </li>
            <li>
              <a class="dropdown-item" href="{{ Route::has('profile.show') ? route('profile.show') : '#'}}">
                <i class="bx bx-user me-2"></i>
                <span class="align-middle">My Profile</span>
              </a>
            </li>

            <li>
              <a class="dropdown-item" href="{{route('change_password')}}">
                <i class="bx bx-credit-card me-2"></i>
                <span class="align-middle">Change Password</span>
              </a>
            </li>

            <li>
              <div class="dropdown-divider"></div>
            </li>

            <li>
              <a class="dropdown-item" href="{{route('user_logout')}}">
                <i class='bx bx-power-off me-2'></i>
                <span class="align-middle">Logout</span>
              </a>
            </li>

          </ul>
        </li>
        <!--/ User -->
      </ul>
    </div>

    <!-- Search Small Screens -->
    <div class="navbar-search-wrapper search-input-wrapper {{ isset($menuHorizontal) ? $containerNav : '' }} d-none">
      <input type="text" class="form-control search-input {{ isset($menuHorizontal) ? '' : $containerNav }} border-0" placeholder="Search..." aria-label="Search...">
      <i class="bx bx-x bx-sm search-toggler cursor-pointer"></i>
    </div>
  </div>
</nav>
<!-- / Navbar -->
