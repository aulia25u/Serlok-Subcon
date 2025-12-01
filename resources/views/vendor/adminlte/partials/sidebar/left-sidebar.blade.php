<aside
    class="main-sidebar {{ config('adminlte.classes_sidebar', 'sidebar-dark-primary elevation-4') }} d-flex flex-column">

    {{-- Sidebar brand logo --}}
    @if(config('adminlte.logo_img_xl'))
        @include('adminlte::partials.common.brand-logo-xl')
    @else
        @include('adminlte::partials.common.brand-logo-xs')
    @endif

    {{-- Sidebar menu --}}
    <div class="sidebar flex-fill">
        <nav class="pt-2">
            <ul class="nav nav-pills nav-sidebar flex-column {{ config('adminlte.classes_sidebar_nav', '') }}"
                data-widget="treeview" role="menu" @if(config('adminlte.sidebar_nav_animation_speed') != 300)
                data-animation-speed="{{ config('adminlte.sidebar_nav_animation_speed') }}" @endif
                @if(!config('adminlte.sidebar_nav_accordion')) data-accordion="false" @endif>
                {{-- Configured sidebar links --}}
                @each('adminlte::partials.sidebar.menu-item', $adminlte->menu('sidebar'), 'item')
            </ul>
        </nav>
    </div>

    {{-- Bottom Section: Profile & Location --}}
    <div class="sidebar-bottom border-top p-2">
        <ul class="nav nav-pills nav-sidebar flex-column">
            {{-- Profile Menu --}}
            <li class="nav-item">
                <a href="{{ route('profile.edit') }}"
                    class="nav-link {{ request()->routeIs('profile.edit') ? 'active' : '' }}">
                    <i class="nav-icon fas fa-fw fa-user"></i>
                    <p>
                        Profile
                    </p>
                </a>
            </li>

            {{-- IP & Location --}}
            <li class="nav-item">
                <div class="nav-link">
                    <i class="nav-icon fas fa-fw fa-map-marker-alt"></i>
                    <p>
                        IP: {{ request()->ip() }}
                        <br>
                        <span id="user-location" style="font-size: 0.8em; margin-left: 0px;">Loading location...</span>
                    </p>
                </div>
            </li>
        </ul>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            fetch('https://ipwho.is/')
                .then(response => response.json())
                .then(data => {
                    const locationSpan = document.getElementById('user-location');
                    if (data.success) {
                        locationSpan.textContent = `${data.city}, ${data.country}`;
                    } else {
                        locationSpan.textContent = 'Location unavailable';
                    }
                })
                .catch(error => {
                    console.error('Error fetching location:', error);
                    document.getElementById('user-location').textContent = 'Location error';
                });
        });
    </script>

    <style>
        /* Hide text in custom sidebar-bottom when sidebar is collapsed AND not hovered */
        .sidebar-collapse .main-sidebar:not(:hover) .sidebar-bottom .nav-link p {
            display: none !important;
            animation: none !important;
        }

        /* Ensure the bottom section doesn't overflow or look weird */
        .sidebar-collapse .main-sidebar .sidebar-bottom {
            overflow: hidden;
        }
    </style>

</aside>