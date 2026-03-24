<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', config('app.name', 'Laravel'))</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="{{ asset('css/employees.css') }}" rel="stylesheet">
    @stack('styles')
</head>
<body>
    {{-- Sidebar --}}
    <aside class="app-sidebar" id="app-sidebar">
        <div class="sidebar-toggle" id="sidebar-toggle" title="Toggle sidebar">
            <i class="bi bi-chevron-left"></i>
        </div>

        <div class="sidebar-brand">
            <span class="sidebar-brand-icon"><i class="bi bi-grid-fill"></i></span>
            <span class="sidebar-brand-text">មុខងារបោះពុម្ភរបាយការណ៍</span>
        </div>

        <div class="sidebar-section-label">ទំព័រមុខ</div>
        <ul class="sidebar-nav">
            <li class="sidebar-nav-item">
                <a href="{{ route('dashboard.index') }}" class="sidebar-link {{ request()->routeIs('dashboard.index') ? 'active' : '' }}" data-tooltip="ផ្ទាំងគ្រប់គ្រង">
                    <span class="sidebar-link-icon"><i class="bi bi-speedometer2"></i></span>
                    <span class="sidebar-link-text">ផ្ទាំងគ្រប់គ្រង</span>
                </a>
            </li>
        </ul>

        <div class="sidebar-section-label">គ្រប់គ្រង</div>
        <ul class="sidebar-nav">
            <li class="sidebar-nav-item">
                <a href="{{ route('employees.index') }}" class="sidebar-link {{ request()->routeIs('employees.*') ? 'active' : '' }}" data-tooltip="បោះពុម្ភរូបថត">
                    <span class="sidebar-link-icon"><i class="bi bi-camera-fill"></i></span>
                    <span class="sidebar-link-text">បោះពុម្ភរូបថត</span>
                </a>
            </li>
        </ul>

        <div class="sidebar-footer">
            <div class="sidebar-user">
                <span class="sidebar-user-avatar"><i class="bi bi-person-circle"></i></span>
                <span class="sidebar-user-text">Admin</span>
            </div>
        </div>
    </aside>

    {{-- Main content wrapper --}}
    <div class="app-main" id="app-main">
    {{-- Navbar --}}
    <nav class="app-navbar">
        <div class="container">
            <div class="brand" onclick="window.location='{{ route('dashboard.index') }}'">
                <span class="brand-icon"><i class="bi bi-people-fill"></i></span>
                ក្រសួងសេដ្ឋកិច្ចនិងហិរញ្ញវត្ថុ
            </div>
        </div>
    </nav>
    <div class="container">
        {{-- Page Title --}}
        @hasSection('page-title')
        <div class="page-title-section">
            <h1 class="page-title">@yield('page-title')</h1>
            @hasSection('page-subtitle')
                <p class="page-subtitle">@yield('page-subtitle')</p>
            @endif
        </div>
        @endif

        {{-- Flash Messages --}}
        @if(session('success'))
            <div class="app-alert app-alert-success mt-3 alert alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" style="filter: invert(52%) sepia(52%) saturate(5000%) hue-rotate(130deg);"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="app-alert app-alert-danger mt-3 alert alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle-fill"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" style="filter: invert(26%) sepia(89%) saturate(5000%) hue-rotate(355deg);"></button>
            </div>
        @endif

        @yield('content')
    </div>

    {{-- Footer --}}
    <footer class="app-footer">
        <div class="container">
            &copy; {{ date('Y') }} នាយកដ្ឋានបុគ្គលិក នៃអគ្គលេខាធិការដ្ឋាន &middot; សិទ្ធិគ្រប់យ៉ាងបានរក្សា។
        </div>
    </footer>
    </div> {{-- /.app-main --}}

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    (function() {
        const sidebar = document.getElementById('app-sidebar');
        const main = document.getElementById('app-main');
        const toggle = document.getElementById('sidebar-toggle');
        const STORAGE_KEY = 'sidebar-collapsed';

        if (localStorage.getItem(STORAGE_KEY) === '1') {
            sidebar.classList.add('collapsed');
            main.classList.add('sidebar-collapsed');
        }

        toggle.addEventListener('click', function() {
            sidebar.classList.toggle('collapsed');
            main.classList.toggle('sidebar-collapsed');
            localStorage.setItem(STORAGE_KEY, sidebar.classList.contains('collapsed') ? '1' : '0');
        });
    })();
    </script>
    @stack('scripts')
</body>
</html>
