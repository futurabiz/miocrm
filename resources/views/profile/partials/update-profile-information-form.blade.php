<section>
    <header>
        <h2 class="text-lg font-medium text-dark">
            Informazioni del Profilo
        </h2>
        <p class="mt-1 text-sm text-muted">
            Aggiorna le informazioni del tuo profilo e l'indirizzo email.
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-4">
        @csrf
        @method('patch')

        <div class="mb-3">
            <label for="name" class="form-label">Nome</label>
            <input id="name" name="name" type="text" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required autofocus autocomplete="name">
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input id="email" name="email" type="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required autocomplete="username">
             @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="mt-2">
                    <p class="text-sm text-muted">
                        Il tuo indirizzo email non è verificato.
                        <button form="send-verification" class="btn btn-link text-decoration-none p-0">
                            Clicca qui per reinviare l'email di verifica.
                        </button>
                    </p>
                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-success">
                            Un nuovo link di verifica è stato inviato al tuo indirizzo email.
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="d-flex align-items-center gap-4">
            <button type="submit" class="btn btn-primary">Salva</button>
            @if (session('status') === 'profile-updated')
                <p class="text-muted m-0">Salvato.</p>
            @endif
        </div>
    </form>
</section>