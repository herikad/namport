<ul class="menu-sub">

    @if (!empty($menu) && isset($menu))
        @foreach ($menu as $submenu)

            <li
                class="menu-item {{ Route::currentRouteName() == $submenu['sidebar_slug'] ? 'active open' : (Request::segment(1) == $submenu['sidebar_slug'] ? 'active open' : (Request::segment(2) == $submenu['sidebar_slug'] ? 'active open' : (Request::segment(3) == $submenu['sidebar_slug'] ? 'active open' : ''))) }}">

                <a href="{{ isset($submenu['sidebar_url']) ? url($submenu['sidebar_url']) : 'javascript:void(0);' }}"
                    class="{{ isset($submenu['sub_menu']) && !empty($submenu['sub_menu']) ? 'menu-link menu-toggle' : 'menu-link' }}"
                    @if (isset($submenu->newTab)) {{ 'target=_blank' }} @endif>

                    @if (isset($submenu['sidebar_class']))
                        <i class="menu-icon {{ $submenu['sidebar_class'] }} {{ Route::currentRouteName() === $submenu['sidebar_slug'] ? 'theme-text-primary' : (Request::segment(1) === $submenu['sidebar_slug'] ? 'theme-text-primary' : (Request::segment(2) === $submenu['sidebar_slug'] ? 'theme-text-primary' : (Request::segment(3) === $submenu['sidebar_slug'] ? 'theme-text-primary' : ''))) }}"></i>
                    @endif
                    &nbsp;&nbsp;
                    @if (isset($submenu['sidebar_name']))
                        {{-- <div> {{ isset($submenu['sidebar_name']) ? __($submenu['sidebar_name']) : '' }}</div> --}}
                        <div class="menu-title text-truncate {{ Route::currentRouteName() === $submenu['sidebar_slug'] ? 'theme-text-primary' : (Request::segment(1) === $submenu['sidebar_slug'] ? 'theme-text-primary' : (Request::segment(2) === $submenu['sidebar_slug'] ? 'theme-text-primary' : (Request::segment(3) === $submenu['sidebar_slug'] ? 'theme-text-primary' : ''))) }}"> {{ isset($submenu['sidebar_name']) ? __($submenu['sidebar_name']) : '' }}</div>
                    @endif

                </a>
                @if (isset($submenu['sub_menu']) && count($submenu['sub_menu']) > 0)
                    @include('layouts.sections.menu.submenu', ['menu' => $submenu['sub_menu']])
                @endif
            </li>

        @endforeach

    @endif

</ul>
