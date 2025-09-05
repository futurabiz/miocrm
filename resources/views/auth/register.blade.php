@extends('layouts.guest')

@section('auth_title', 'Registrati')

@section('content')
    @if (session('status'))
        <div class="mb-4 font-medium text-sm text-success">
            {{ session('status') }}
        </div>
    @endif

    <div class="d-flex justify-content-center"> {{-- AGGIUNTO: Contenitore flexbox per centrare il form --}}
        <form method="POST" action="{{ route('register') }}" class="col-10"> {{-- AGGIUNTO: col-10 per larghezza e centratura --}}
            @csrf

            <div class="mb-3">
                <label for="name" class="form-label">Nome</label>
                <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name" class="form-control @error('name') is-invalid @enderror">
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username" class="form-control @error('email') is-invalid @enderror">
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input id="password" type="password" name="password" required autocomplete="new-password" class="form-control @error('password') is-invalid @enderror">
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="password_confirmation" class="form-label">Conferma Password</label>
                <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" class="form-control @error('password_confirmation') is-invalid @enderror">
                @error('password_confirmation')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="d-flex justify-content-end align-items-center mt-4">
                <a class="text-sm text-decoration-none" href="{{ route('login') }}">
                    Già registrato?
                </a>

                <button type="submit" class="btn btn-primary ms-3">
                    Registrati
                </button>
            </div>
        </form>
    </div> {{-- Chiusura del div aggiunto --}}
@endsection