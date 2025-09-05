@extends('layouts.guest')

@section('auth_title', 'Conferma Password')

@section('content')
    <div class="mb-4 text-sm text-gray-600">
        Questa è un'area sicura dell'applicazione. Si prega di confermare la password per continuare.
    </div>

    <div class="d-flex justify-content-center"> {{-- AGGIUNTO: Contenitore flexbox per centrare il form --}}
        <form method="POST" action="{{ route('password.confirm') }}" class="col-10"> {{-- AGGIUNTO: col-10 per larghezza e centratura --}}
            @csrf

            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input id="password" type="password" name="password" required autocomplete="current-password" class="form-control @error('password') is-invalid @enderror">
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="d-flex justify-content-end mt-4">
                <button type="submit" class="btn btn-primary">
                    Conferma
                </button>
            </div>
        </form>
    </div> {{-- Chiusura del div aggiunto --}}
@endsection