@extends('layouts.guest')

@section('auth_title', 'Password Dimenticata')

@section('content')
    <div class="mb-4 text-sm text-gray-600">
        Hai dimenticato la password? Nessun problema. Comunicaci il tuo indirizzo email e ti invieremo un link per il reset della password che ti permetterà di sceglierne una nuova.
    </div>

    @if (session('status'))
        <div class="mb-4 font-medium text-sm text-success">
            {{ session('status') }}
        </div>
    @endif

    <div class="d-flex justify-content-center"> {{-- AGGIUNTO: Contenitore flexbox per centrare il form --}}
        <form method="POST" action="{{ route('password.email') }}" class="col-10"> {{-- AGGIUNTO: col-10 per larghezza e centratura --}}
            @csrf

            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus class="form-control @error('email') is-invalid @enderror">
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="d-flex justify-content-end mt-4">
                <button type="submit" class="btn btn-primary">
                    Invia Link Reset Password
                </button>
            </div>
        </form>
    </div> {{-- Chiusura del div aggiunto --}}
@endsection