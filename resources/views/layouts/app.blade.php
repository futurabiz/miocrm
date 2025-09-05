<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- *** INIZIO MODIFICA: Aggiunto Meta Tag per la Content Security Policy (DEBUG) *** --}}
    <meta http-equiv="Content-Security-Policy" content="script-src 'self' 'unsafe-inline' 'unsafe-eval' https:; style-src 'self' 'unsafe-inline' https:;">
    {{-- *** FINE MODIFICA *** --}}

    <title>Mio CRM</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
    
    {{-- *** INIZIO MODIFICA: TINYMCE *** --}}
    <script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>
    {{-- *** FINE MODIFICA: TINYMCE *** --}}

    <style>
        :root { --sidebar-width: 260px; --sidebar-collapsed-width: 80px; }
        body { display: flex; background-color: #f8f9fa; }
        #sidebar {
            width: var(--sidebar-width);
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
            background-color: #212529;
            transition: width 0.3s ease-in-out;
            display: flex;
            flex-direction: column;
        }
        #main-content { margin-left: var(--sidebar-width); width: calc(100% - var(--sidebar-width)); transition: margin-left 0.3s ease-in-out; }
        #sidebar.collapsed { width: var(--sidebar-collapsed-width); }
        #sidebar.collapsed + #main-content { margin-left: var(--sidebar-collapsed-width); }

        .sidebar-header { flex-shrink: 0; }
        .sidebar-content { flex-grow: 1; overflow-y: auto; }
        .sidebar-footer { flex-shrink: 0; }

        .sidebar-brand { font-size: 1.5rem; font-weight: bold; text-align: center; white-space: nowrap; overflow: hidden; }
        .nav-link { color: rgba(255, 255, 255, 0.7); display: flex; align-items: center; padding: 0.75rem 1.5rem; white-space: nowrap; overflow: hidden; }
        .nav-link:hover, .nav-link.active { color: #fff; background-color: #343a40; }
        .nav-link .link-icon { font-size: 1.25rem; margin-right: 1rem; width: 24px; text-align: center; }
        #sidebar.collapsed .sidebar-brand .brand-text, #sidebar.collapsed .nav-link .link-text, #sidebar.collapsed .sidebar-heading, #sidebar.collapsed .sidebar-footer .dropdown-toggle strong { display: none; }
        #sidebar.collapsed .nav-link { justify-content: center; }
        #sidebar.collapsed .nav-link .link-icon { margin-right: 0; }
        #topbar { background-color: #fff; box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15); }
        .sidebar-heading { color: #6c757d; padding: .5rem 1.5rem; font-size: .75rem; text-transform: uppercase; }
        .submenu-container { background-color: #1c1f23; }
        .submenu-item { color: rgba(255, 255, 255, 0.6) !important; padding-left: 3.5rem !important; font-size: 0.9rem; }
        #sidebar.collapsed .submenu-container { display: none !important; }
    </style>
    @stack('styles')
</head>
<body>
    {{-- Aggiungi un controllo @auth per mostrare la sidebar e la topbar solo se autenticato --}}
    @auth
    <div id="sidebar" class="text-white bg-dark">
        <div class="sidebar-header p-3">
            <a href="{{ route('dashboard') }}" class="d-flex align-items-center text-white text-decoration-none sidebar-brand">
                <i class="bi bi-shield-check me-2"></i>
                <span class="brand-text">Mio CRM</span>
            </a>
        </div>

        <div class="sidebar-content">
            <ul class="nav nav-pills flex-column mt-2 sidebar-nav">
                <li class="nav-item"><a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"><i class="bi bi-speedometer2 link-icon"></i><span class="link-text">Dashboard</span></a></li>
                <div class="sidebar-heading mt-2"><span class="link-text">Vendite</span></div>
                <li><a href="{{ route('leads.index') }}" class="nav-link {{ request()->routeIs('leads.*') ? 'active' : '' }}"><i class="bi bi-person-plus-fill link-icon"></i><span class="link-text">Lead</span></a></li>
                <li><a href="{{ route('contacts.index') }}" class="nav-link {{ request()->routeIs('contacts.*') ? 'active' : '' }}"><i class="bi bi-person-lines-fill link-icon"></i><span class="link-text">Contatti</span></a></li>
                <li><a href="{{ route('companies.index') }}" class="nav-link {{ request()->routeIs('companies.*') ? 'active' : '' }}"><i class="bi bi-building link-icon"></i><span class="link-text">Aziende</span></a></li>
                <li><a href="{{ route('opportunities.index') }}" class="nav-link {{ request()->routeIs('opportunities.*') ? 'active' : '' }}"><i class="bi bi-cash-coin link-icon"></i><span class="link-text">Opportunità</span></a></li>
                <div class="sidebar-heading mt-2"><span class="link-text">Organizzazione</span></div>
                <li><a href="{{ route('calendario.index') }}" class="nav-link {{ request()->routeIs('calendario.*') ? 'active' : '' }}"><i class="bi bi-calendar-week link-icon"></i><span class="link-text">Calendario</span></a></li>
                <div class="sidebar-heading mt-2"><span class="link-text">Dati</span></div>
                <li><a href="{{ route('import.create') }}" class="nav-link {{ request()->routeIs('import.*') ? 'active' : '' }}"><i class="bi bi-box-arrow-in-down link-icon"></i><span class="link-text">Importa Dati</span></a></li>
            </ul>
        </div>

        <div class="sidebar-footer">
            <hr class="m-0">
            <ul class="nav nav-pills flex-column sidebar-nav">
                @php $isSettingsActive = request()->routeIs(['profile.edit', 'workflows.*', 'email_templates.*', 'email_lists.*', 'tags.*', 'service_types.*']); @endphp {{-- MODIFICATO QUI: email-templates.* e service-types.* --}}
                <li class="nav-item">
                    <a class="nav-link {{ $isSettingsActive ? 'active' : '' }}" data-bs-toggle="collapse" href="#settings-submenu" role="button" aria-expanded="{{ $isSettingsActive ? 'true' : 'false' }}">
                        <i class="bi bi-gear-fill link-icon"></i><span class="link-text">Impostazioni</span>
                    </a>
                    <div class="collapse submenu-container {{ $isSettingsActive ? 'show' : '' }}" id="settings-submenu">
                        <ul class="nav flex-column">
                            <li><a href="{{ route('profile.edit') }}" class="nav-link submenu-item {{ request()->routeIs('profile.edit') ? 'active' : '' }}"><span class="link-text">Profilo</span></a></li>
                            <li><a href="{{ route('workflows.index') }}" class="nav-link submenu-item {{ request()->routeIs('workflows.*') ? 'active' : '' }}"><span class="link-text">Workflow</span></a></li>
                            <li><a href="{{ route('email_templates.index') }}" class="nav-link submenu-item {{ request()->routeIs('email_templates.*') ? 'active' : '' }}"><span class="link-text">Template Email</span></a></li> {{-- MODIFICATO QUI --}}
                            <li><a href="{{ route('email_lists.index') }}" class="nav-link submenu-item {{ request()->routeIs('email_lists.*') ? 'active' : '' }}"><span class="link-text">Liste Email</span></a></li>
                            <li><a href="{{ route('tags.index') }}" class="nav-link submenu-item {{ request()->routeIs('tags.*') ? 'active' : '' }}"><span class="link-text">Tag</span></a></li>
                            <li><a href="{{ route('service_types.index') }}" class="nav-link submenu-item {{ request()->routeIs('service_types.*') ? 'active' : '' }}"><span class="link-text">Tipi di Servizio</span></a></li> {{-- MODIFICATO QUI --}}
                        </ul>
                    </div>
                </li>
            </ul>
            <hr class="m-0">
            <div class="dropdown p-3">
                <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-person-circle fs-4 me-2"></i>
                    <strong class="link-text">@if(Auth::check()){{ Auth::user()->name }}@else Utente @endif</strong>
                </a>
                <ul class="dropdown-menu dropdown-menu-dark text-small shadow">
                    <li>
                        <form method="POST" action="{{ route('logout') }}" id="logout-form-sidebar">@csrf</form>
                        <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form-sidebar').submit();">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    @endauth {{-- Fine del controllo @auth --}}

    {{-- Contenuto Principale --}}
    {{-- Se l'utente non è autenticato, il contenuto principale non avrà la sidebar --}}
    <div id="main-content" class="w-100 @guest mt-5 @endguest"> {{-- Aggiunto @guest per margini senza sidebar --}}
        @auth {{-- La topbar appare solo se autenticato --}}
        <nav id="topbar" class="navbar navbar-expand navbar-light p-2 mb-4">
             <button class="btn btn-light" id="sidebar-toggle-btn"><i class="bi bi-list fs-4"></i></button>
        </nav>
        @endauth
        <main class="container-fluid">
            @yield('content')
        </main>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const toggleBtn = document.getElementById('sidebar-toggle-btn');
            // Questo controllo è necessario perché la sidebar potrebbe non esistere se l'utente non è autenticato
            if (toggleBtn && sidebar) { 
                toggleBtn.addEventListener('click', function() {
                    sidebar.classList.toggle('collapsed');
                    localStorage.setItem('sidebarCollapsed', sidebar.classList.contains('collapsed'));
                });
            }
            if (localStorage.getItem('sidebarCollapsed') === 'true' && sidebar) {
                sidebar.classList.add('collapsed');
            }
        });
    </script>
    @stack('scripts')
</body>
</html>
