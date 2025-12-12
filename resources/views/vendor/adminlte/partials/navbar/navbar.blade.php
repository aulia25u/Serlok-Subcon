@inject('layoutHelper', 'JeroenNoten\LaravelAdminLte\Helpers\LayoutHelper')

<nav class="main-header navbar
    {{ config('adminlte.classes_topnav_nav', 'navbar-expand') }}
    {{ config('adminlte.classes_topnav', 'navbar-white navbar-light') }}">

    {{-- Navbar left links --}}
    <ul class="navbar-nav">
        {{-- Left sidebar toggler link --}}
        @include('adminlte::partials.navbar.menu-item-left-sidebar-toggler')
    </ul>

    {{-- Navbar right links --}}
    <ul class="navbar-nav ml-auto">
        {{-- Fullscreen Widget --}}
        @include('adminlte::partials.navbar.menu-item-fullscreen-widget')

        {{-- User Menu Dropdown --}}
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" aria-expanded="false">
                Hi, {{ Auth::user()->name }}
            </a>
            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                <span class="dropdown-item dropdown-header">User Profile</span>
                <div class="dropdown-divider"></div>

                {{-- Profile Link --}}
                <a href="{{ route('profile.edit') }}" class="dropdown-item">
                    <i class="fas fa-user mr-2"></i> Profile
                </a>

                <div class="dropdown-divider"></div>

                {{-- IP & Location --}}
                <div class="dropdown-item">
                    <i class="fas fa-map-marker-alt mr-2"></i>
                    <span class="text-muted text-sm">
                        IP: {{ request()->ip() }}
                        <br>
                        <span id="user-location" style="margin-left: 20px;">Loading location...</span>
                    </span>
                </div>
            </div>
        </li>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                fetch('https://ipwho.is/')
                    .then(response => response.json())
                    .then(data => {
                        const locationSpan = document.getElementById('user-location');
                        if (locationSpan) {
                            if (data.success) {
                                locationSpan.textContent = `${data.city}, ${data.country}`;
                            } else {
                                locationSpan.textContent = 'Location unavailable';
                            }
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching location:', error);
                        const locationSpan = document.getElementById('user-location');
                        if (locationSpan) {
                            locationSpan.textContent = 'Location error';
                        }
                    });
            });
        </script>

        {{-- Tenant Switcher --}}
        @auth
            @php
                $user = Auth::user();
                $tenants = \App\Models\TenantOwner::where('user_id', $user->id)
                    ->where('is_active', true)
                    ->with('customer')
                    ->get();

                $currentTenantId = \App\Services\TenantService::currentCustomerId();
                $currentTenant = $tenants->firstWhere('customer_id', $currentTenantId);
                $currentTenantName = $currentTenant ? $currentTenant->customer->customer_name : 'Select Tenant';
            @endphp

            @if($tenants->count() > 0)
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="tenantDropdown" role="button" data-toggle="dropdown"
                        aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-building mr-2"></i>
                        {{ $currentTenantName }}
                    </a>
                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="tenantDropdown">
                        <span class="dropdown-item dropdown-header">Switch Tenant</span>
                        <div class="dropdown-divider"></div>
                        @foreach($tenants as $tenant)
                            <a class="dropdown-item {{ $currentTenantId == $tenant->customer_id ? 'active' : '' }}" href="#"
                                onclick="event.preventDefault(); document.getElementById('tenant-switch-form-{{ $tenant->customer_id }}').submit();">
                                <i class="fas fa-check mr-2 {{ $currentTenantId == $tenant->customer_id ? '' : 'invisible' }}"></i>
                                {{ $tenant->customer->customer_name }}
                            </a>
                            <form id="tenant-switch-form-{{ $tenant->customer_id }}" action="{{ route('tenant.switch') }}"
                                method="POST" style="display: none;">
                                @csrf
                                <input type="hidden" name="customer_id" value="{{ $tenant->customer_id }}">
                            </form>
                        @endforeach
                    </div>
                </li>
            @endif
        @endauth

        {{-- Logout Link --}}
        @include('adminlte::partials.navbar.menu-item-logout-link')
    </ul>

</nav>