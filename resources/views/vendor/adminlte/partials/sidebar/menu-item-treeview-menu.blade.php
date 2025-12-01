<li @isset($item['id']) id="{{ $item['id'] }}" @endisset class="nav-item has-treeview {{ $item['submenu_class'] }}">

    {{-- Menu toggler --}}
    <a class="nav-link {{ $item['class'] }} @isset($item['shift']) {{ $item['shift'] }} @endisset" href="" {!! $item['data-compiled'] ?? '' !!}>

        @if(isset($item['is_submenu']) && $item['is_submenu'])
            <i class="nav-icon fas fa-minus"></i>
        @else
            <i class="nav-icon {{ $item['icon'] ?? 'far fa-fw fa-circle' }} {{
            isset($item['icon_color']) ? 'text-' . $item['icon_color'] : 'text-info'
                }}"></i>
        @endif

        <p>
            {{ $item['text'] }}
            <i class="fas fa-angle-left right"></i>

            @isset($item['label'])
                <span class="badge badge-{{ $item['label_color'] ?? 'primary' }} right">
                    {{ $item['label'] }}
                </span>
            @endisset
        </p>

    </a>

    {{-- Menu items --}}
    <ul class="nav nav-treeview">
        @foreach($item['submenu'] as $subitem)
            @php $subitem['is_submenu'] = true; @endphp
            @include('adminlte::partials.sidebar.menu-item', ['item' => $subitem])
        @endforeach
    </ul>

</li>