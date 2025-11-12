@php
    $activeTenantName = optional(optional(optional(Auth::user())->userDetail)->customer)->customer_name ?? 'Internal Tenant';
@endphp

@auth
    <li class="nav-item d-flex align-items-center">
        <span class="badge bg-success text-white px-3 py-1 rounded-pill text-nowrap me-2">
            {{ $activeTenantName }}
        </span>
        <a class="nav-link" data-widget="fullscreen" href="#" role="button">
            <i class="fas fa-expand-arrows-alt"></i>
        </a>
    </li>
@endauth
