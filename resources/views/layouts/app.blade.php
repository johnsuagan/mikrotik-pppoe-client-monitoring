<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>MikroTik Monitor</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script>
        (function() {
            var isDark = localStorage.getItem('theme') === 'dark' || (!localStorage.getItem('theme') && window.matchMedia('(prefers-color-scheme: dark)').matches);
            if (isDark) {
                document.documentElement.classList.add('dark-mode');
            }
            document.addEventListener('DOMContentLoaded', function() {
                if (isDark) document.body.classList.add('dark-mode');
            });
        })();
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="app-wrapper">

    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-lte-toggle="sidebar" href="#" role="button">
                    <i class="fas fa-bars"></i>
                </a>
            </li>
        </ul>

        <ul class="navbar-nav ms-auto">
            <li class="nav-item me-2">
                <button class="theme-toggle" id="themeToggle" title="Toggle dark mode">
                    <i class="fas fa-sun icon-sun"></i>
                    <i class="fas fa-moon icon-moon"></i>
                </button>
            </li>
            @if(isset($routers) && $routers->count() > 0)
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown" href="#">
                    <i class="fas fa-network-wired me-2" style="color: var(--accent);"></i>
                    {{ $selectedRouter->name ?? 'Select Router' }}
                    <i class="fas fa-chevron-down ms-1" style="font-size: 10px;"></i>
                </a>
                <div class="dropdown-menu dropdown-menu-end">
                    @foreach($routers as $router)
                    <a class="dropdown-item {{ $selectedRouter && $selectedRouter->id === $router->id ? 'active' : '' }}" href="{{ request()->url() . '?router=' . $router->id }}">
                        <i class="fas fa-server me-2"></i> {{ $router->name }}
                        <span class="badge bg-success ms-1">{{ $router->host }}</span>
                    </a>
                    @endforeach
                </div>
            </li>
            @endif
        </ul>
    </nav>

    <aside class="app-sidebar sidebar-dark elevation-0">
        <a href="{{ route('dashboard') }}" class="brand-link">
            <span class="brand-icon"><i class="fas fa-satellite-dish"></i></span>
            <span class="brand-text">MikroTik Monitor</span>
        </a>

        <div class="sidebar">
            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                    <li class="nav-item">
                        <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-gauge-high"></i>
                            <p>Dashboard</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('pppoe') }}" class="nav-link {{ request()->routeIs('pppoe*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-users"></i>
                            <p>PPPoE Users</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('interfaces') }}" class="nav-link {{ request()->routeIs('interfaces*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-network-wired"></i>
                            <p>Interfaces</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('queues') }}" class="nav-link {{ request()->routeIs('queues*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-layer-group"></i>
                            <p>Queues</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('usage') }}" class="nav-link {{ request()->routeIs('usage*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-chart-bar"></i>
                            <p>Client Usage</p>
                        </a>
                    </li>

                    <li class="nav-header">MANAGEMENT</li>
                    <li class="nav-item">
                        <a href="{{ route('routers.index') }}" class="nav-link {{ request()->routeIs('routers*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-cog"></i>
                            <p>Router Settings</p>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </aside>

    <div class="content-wrapper">
        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show m-3" role="alert">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show m-3" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        @yield('content')
    </div>

    <footer class="main-footer">
        <div class="float-end d-none d-sm-block">v1.0</div>
        <strong>MikroTik Monitor</strong>
    </footer>

</div>
    @stack('scripts')
</body>
</html>
