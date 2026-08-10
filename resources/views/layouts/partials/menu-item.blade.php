@php
    $level = $level ?? 0;
    $canViewMenu = !$menu->permiso_id || (auth()->user() && $menu->permiso && auth()->user()->hasPermission($menu->permiso->codigo));
    $visibleSubmenus = $menu->submenus->filter(function ($submenu) {
        return !$submenu->permiso_id || (auth()->user() && $submenu->permiso && auth()->user()->hasPermission($submenu->permiso->codigo));
    });
    $hasSubmenus = $visibleSubmenus->count() > 0;
    $isDirectActive = $menu->url && request()->is(ltrim($menu->url, '/').'*');
    $isMenuExpanded = $isDirectActive;

    if ($hasSubmenus) {
        foreach ($visibleSubmenus as $submenu) {
            if ($submenu->url && request()->is(ltrim($submenu->url, '/').'*')) {
                $isMenuExpanded = true;
                break;
            }

            foreach ($submenu->submenus as $child) {
                if ($child->url && request()->is(ltrim($child->url, '/').'*')) {
                    $isMenuExpanded = true;
                    break 2;
                }
            }
        }
    }
@endphp

@if($canViewMenu || $hasSubmenus)
    <li>
        <a href="{{ $hasSubmenus ? '#' : ($menu->url ? url($menu->url) : '#') }}"
           class="{{ (!$hasSubmenus && $isDirectActive) || ($hasSubmenus && $isMenuExpanded) ? 'active' : '' }} {{ $hasSubmenus ? 'toggle-submenu' : '' }}"
           style="{{ $level > 0 ? 'padding-left: ' . (12 + ($level * 10)) . 'px; font-size: 13px;' : '' }}"
           title="{{ $menu->nombre }}">
            <i class="{{ $menu->icono ?? 'fas fa-angle-right' }}"></i>
            <span class="nav-text">{{ $menu->nombre }}</span>
            @if($hasSubmenus)
                <i class="fas fa-chevron-right nav-toggle-icon {{ $isMenuExpanded ? 'rotated' : '' }}"></i>
            @endif
        </a>
        @if($hasSubmenus)
            <ul class="submenu submenu-level-{{ $level + 1 }} {{ $isMenuExpanded ? 'show' : '' }}">
                @foreach($visibleSubmenus as $submenu)
                    @include('layouts.partials.menu-item', ['menu' => $submenu, 'level' => $level + 1])
                @endforeach
            </ul>
        @endif
    </li>
@endif
