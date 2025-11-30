@php
    $user = Auth::user();
    $userDetail = optional($user)->userDetail;
    $activeTenantName = optional($userDetail->customer)->customer_name ?? 'Internal Tenant';

    $myTenants = \App\Models\TenantOwner::where('user_id', $user->id)
        ->where('is_active', true)
        ->with('customer')
        ->get()
        ->pluck('customer');
@endphp

@auth
    <li class="nav-item dropdown d-flex align-items-center">
        @if($myTenants->count() > 1)
            <a class="nav-link dropdown-toggle" href="#" id="tenantDropdown" role="button" data-toggle="dropdown"
                aria-haspopup="true" aria-expanded="false">
                <span class="badge bg-success text-white px-3 py-1 rounded-pill text-nowrap">
                    {{ $activeTenantName }}
                </span>
            </a>
            <div class="dropdown-menu" aria-labelledby="tenantDropdown">
                @foreach($myTenants as $tenant)
                    <form action="{{ route('tenant.switch') }}" method="POST" class="d-inline">
                        @csrf
                        <input type="hidden" name="customer_id" value="{{ $tenant->id }}">
                        <button type="submit" class="dropdown-item {{ $userDetail->customer_id == $tenant->id ? 'active' : '' }}">
                            {{ $tenant->customer_name }}
                        </button>
                    </form>
                @endforeach
            </div>
        @else
            <span class="badge bg-success text-white px-3 py-1 rounded-pill text-nowrap me-2">
                {{ $activeTenantName }}
            </span>
        @endif

        <a class="nav-link" data-widget="fullscreen" href="#" role="button">
            <i class="fas fa-expand-arrows-alt"></i>
        </a>
    </li>
@endauth