@extends('layouts.app') {{-- Estende il tuo layout principale per gli stili --}}

@section('content')
    <div class="container d-flex flex-column justify-content-center align-items-center min-vh-100">
        {{-- SEZIONE 1: LOGO DI LARAVEL --}}
        <div class="text-center mb-5">
            <a href="/">
                <x-application-logo class="w-20 h-20 fill-current text-gray-500" />
            </a>
        </div>

        {{-- SEZIONE 2: BOX DEL FORM (LA CARD) --}}
        {{-- APPLICO STILE IN LINEA PER MAX-WIDTH PER OTTENERE UN EFFETTO QUASI QUADRATO --}}
        {{-- Questa larghezza è stata scelta per bilanciare l'altezza del form di login standard --}}
        <div style="max-width: 400px; width: 90%;"> {{-- CAMBIATO QUI: Stile inline per larghezza fissa --}}
            <div class="card shadow-lg bg-white rounded py-4 px-4">
                <div class="card-header text-center bg-primary text-white py-3 mb-3">
                    <h4 class="mb-0">@yield('auth_title', 'Accedi al CRM')</h4>
                </div>
                <div class="card-body p-4">
                    @yield('form_content')
                </div>
            </div>
        </div>
    </div>
@endsection

{{-- Non è necessario @stack('scripts') o @stack('styles') qui
    perché layouts.app già li gestisce --}}