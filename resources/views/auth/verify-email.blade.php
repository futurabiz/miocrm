@extends('layouts.guest')

@section('auth_title', 'Verifica Email')

@section('content')
    <div class="mb-4 text-sm text-gray-600">
        Grazie per esserti registrato! Prima di iniziare, potresti verificare il tuo indirizzo email cliccando sul link che ti abbiamo appena inviato via email? Se non hai ricevuto l'email, saremo lieti di inviartene un'altra.
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-4 font-medium text-sm text-success">
            Un nuovo link di verifica è stato inviato all'indirizzo email che hai fornito durante la registrazione.
        </div>
    @endif

    <div class="d-flex justify-content-center"> {{-- AGGIUNTO: Contenitore flexbox per centrare i form --}}
        <div class="col-10"> {{-- AGGIUNTO: col-10 per larghezza --}}
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf

                <div class="mb-3">
                    <button type="submit" class="btn btn-primary">
                        Invia nuovamente email di verifica
                    </button>
                </div>
            </form>

            <form method="POST" action="{{ route('logout') }}" class="mt-2">
                @csrf

                <button type="submit" class="btn btn-link text-sm text-decoration-none text-muted">
                    Logout
                </button>
            </form>
        </div>
    </div> {{-- Chiusura del div aggiunto --}}
@endsection