@php
    $configData = Helper::appClasses();
@endphp

<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">

    <!-- ! Hide app brand if navbar-full -->
    @if (!isset($navbarFull))
        <div class="app-brand demo">
            <a href="{{ url('/') }}" class="app-brand-link">

                <img src="{{ asset('logo.png') }}" class="logo object-fit-md-contain pt-3 w-75" alt="">
                {{-- <img src="{{asset('logo.png')}}" height="60" width="220" class="logo object-fit-md-contain" alt=""> --}}

                {{-- <span class="app-brand-text demo menu-text fw-bold ms-2">{{ config('variables.templateName') }}</span> --}}
            </a>
            {{--
            <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
                <i class="bx menu-toggle-icon d-none d-xl-block fs-4 align-middle"></i>
                <i class="bx bx-x d-block d-xl-none bx-sm align-middle"></i>
            </a> --}}
        </div>
    @endif

    <!-- ! Hide menu divider if navbar-full -->
    @if (!isset($navbarFull))
        <div class="menu-divider mt-0 ">
        </div>
    @endif
    @php
        $menuData = Helper::user_rights_by_modual();

    @endphp

    <div class="menu-inner-shadow"></div>

    <ul class="menu-inner py-1">
        @php
            // dd($menuData);
        @endphp
        @if (!empty($menuData) && isset($menuData))

            @foreach ($menuData as $menu)
                <li
                class="menu-item {{ Route::currentRouteName() == $menu['sidebar_slug'] ? 'active open' : (Request::segment(1) == $menu['sidebar_slug'] ? 'active open' : (Request::segment(2) == $menu['sidebar_slug'] ? 'active open' : (Request::segment(3) == $menu['sidebar_slug'] ? 'active open' : ''))) }}">

                    <a href="{{ isset($menu['sidebar_url']) ? url($menu['sidebar_url']) : 'javascript:void(0);' }}"
                        class="{{ count($menu['menu']) > 0 && isset($menu['is_dashboard']) && $menu['is_dashboard'] == 0 ? 'menu-link menu-toggle' : 'menu-link' }}"
                        @if (isset($menu->newTab)) {{ 'target=_blank' }} @endif>

                        @if (isset($menu['sidebar_class']))
                            <i class="menu-icon  {{ $menu['sidebar_class'] }}"></i>
                        @endif
                        {{-- &nbsp;&nbsp; --}}
                        @if (isset($menu['sidebar_name']))
                            <div> {{ isset($menu['sidebar_name']) ? __($menu['sidebar_name']) : '' }}</div>
                            {{-- <span class="menu-title text-truncate"> {{ isset($menu['sidebar_name']) ? __($menu['sidebar_name']) : '' }}</span> --}}
                        @endif

                    </a>
                    @if (count($menu['menu']) > 0 && isset($menu['is_dashboard']) && $menu['is_dashboard'] == 0)
                        @if (isset($menu['menu']))
                            @include('layouts.sections.menu.submenu', ['menu' => $menu['menu']])
                        @endif
                    @endif
                </li>
            @endforeach

        @endif

    </ul>

</aside>
